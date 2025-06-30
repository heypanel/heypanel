<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Plugin;

use Composer\InstalledVersions;
use Composer\IO\NullIO;
use Composer\Semver\Comparator;
use HeyPanel\Core\Defaults;
use HeyPanel\Core\Framework\Api\Context\SystemSource;
use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityRepository;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Criteria;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use HeyPanel\Core\Framework\Migration\MigrationCollection;
use HeyPanel\Core\Framework\Migration\MigrationCollectionLoader;
use HeyPanel\Core\Framework\Migration\MigrationSource;
use HeyPanel\Core\Framework\Plugin;
use HeyPanel\Core\Framework\Plugin\Composer\CommandExecutor;
use HeyPanel\Core\Framework\Plugin\Context\ActivateContext;
use HeyPanel\Core\Framework\Plugin\Context\DeactivateContext;
use HeyPanel\Core\Framework\Plugin\Context\InstallContext;
use HeyPanel\Core\Framework\Plugin\Context\UninstallContext;
use HeyPanel\Core\Framework\Plugin\Context\UpdateContext;
use HeyPanel\Core\Framework\Plugin\Event\PluginPostActivateEvent;
use HeyPanel\Core\Framework\Plugin\Event\PluginPostDeactivateEvent;
use HeyPanel\Core\Framework\Plugin\Event\PluginPostDeactivationFailedEvent;
use HeyPanel\Core\Framework\Plugin\Event\PluginPostInstallEvent;
use HeyPanel\Core\Framework\Plugin\Event\PluginPostUninstallEvent;
use HeyPanel\Core\Framework\Plugin\Event\PluginPostUpdateEvent;
use HeyPanel\Core\Framework\Plugin\Event\PluginPreActivateEvent;
use HeyPanel\Core\Framework\Plugin\Event\PluginPreDeactivateEvent;
use HeyPanel\Core\Framework\Plugin\Event\PluginPreInstallEvent;
use HeyPanel\Core\Framework\Plugin\Event\PluginPreUninstallEvent;
use HeyPanel\Core\Framework\Plugin\Event\PluginPreUpdateEvent;
use HeyPanel\Core\Framework\Plugin\Exception\PluginHasActiveDependantsException;
use HeyPanel\Core\Framework\Plugin\Exception\PluginNotActivatedException;
use HeyPanel\Core\Framework\Plugin\Exception\PluginNotInstalledException;
use HeyPanel\Core\Framework\Plugin\KernelPluginLoader\KernelPluginLoader;
use HeyPanel\Core\Framework\Plugin\KernelPluginLoader\StaticKernelPluginLoader;
use HeyPanel\Core\Framework\Plugin\Requirement\Exception\RequirementStackException;
use HeyPanel\Core\Framework\Plugin\Requirement\RequirementsValidator;
use HeyPanel\Core\Framework\Plugin\Util\AssetService;
use HeyPanel\Core\Framework\Plugin\Util\VersionSanitizer;
use HeyPanel\Core\System\SystemConfig\SystemConfigService;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\EventListener\StopWorkerOnRestartSignalListener;

/**
 * @internal
 */
class PluginLifecycleService
{
    final public const STATE_SKIP_ASSET_BUILDING = 'skip-asset-building';

    /**
     * @var array{plugin: PluginEntity, context: Context}|null
     */
    private static ?array $pluginToBeDeleted = null;

    private static bool $registeredListener = false;

    /**
     * @param EntityRepository<PluginCollection> $pluginRepo
     */
    public function __construct(
        private readonly EntityRepository $pluginRepo,
        private EventDispatcherInterface $eventDispatcher,
        private readonly KernelPluginCollection $pluginCollection,
        private ContainerInterface $container,
        private readonly MigrationCollectionLoader $migrationLoader,
        private readonly AssetService $assetInstaller,
        private readonly CommandExecutor $executor,
        private readonly RequirementsValidator $requirementValidator,
        private readonly CacheItemPoolInterface $restartSignalCachePool,
        private readonly string $heypanelVersion,
        private readonly SystemConfigService $systemConfigService,
        private readonly PluginService $pluginService,
        private readonly VersionSanitizer $versionSanitizer,
        private readonly DefinitionInstanceRegistry $definitionRegistry,
    ) {
    }

    /**
     * @throws RequirementStackException
     */
    public function installPlugin(PluginEntity $plugin, Context $heypanelContext): InstallContext
    {
        $pluginData = [];
        $pluginBaseClass = $this->getPluginBaseClass($plugin->getBaseClass());
        $pluginVersion = $plugin->getVersion();

        $installContext = new InstallContext(
            $pluginBaseClass,
            $heypanelContext,
            $this->heypanelVersion,
            $pluginVersion,
            $this->createMigrationCollection($pluginBaseClass)
        );

        if ($plugin->getInstalledAt()) {
            return $installContext;
        }

        $didRunComposerRequire = false;

        if ($pluginBaseClass->executeComposerCommands()) {
            $didRunComposerRequire = $this->executeComposerRequireWhenNeeded($plugin, $pluginBaseClass, $pluginVersion, $heypanelContext);
        } else {
            $this->requirementValidator->validateRequirements($plugin, $heypanelContext, 'install');
        }

        try {
            $pluginData['id'] = $plugin->getId();

            // Makes sure the version is updated in the db after a re-installation
            $updateVersion = $plugin->getUpgradeVersion();
            if ($updateVersion !== null && $this->hasPluginUpdate($updateVersion, $pluginVersion)) {
                $pluginData['version'] = $updateVersion;
                $plugin->setVersion($updateVersion);
                $pluginData['upgradeVersion'] = null;
                $plugin->setUpgradeVersion(null);
                $upgradeDate = new \DateTime();
                $pluginData['upgradedAt'] = $upgradeDate->format(Defaults::STORAGE_DATE_TIME_FORMAT);
                $plugin->setUpgradedAt($upgradeDate);
            }

            $this->eventDispatcher->dispatch(new PluginPreInstallEvent($plugin, $installContext));

            $this->systemConfigService->savePluginConfiguration($pluginBaseClass, true);

            $pluginBaseClass->install($installContext);

            $this->runMigrations($installContext);

            $installDate = new \DateTime();
            $pluginData['installedAt'] = $installDate->format(Defaults::STORAGE_DATE_TIME_FORMAT);
            $plugin->setInstalledAt($installDate);

            $this->updatePluginData($pluginData, $heypanelContext);

            $pluginBaseClass->postInstall($installContext);

            $this->eventDispatcher->dispatch(new PluginPostInstallEvent($plugin, $installContext));
        } catch (\Throwable $e) {
            if ($didRunComposerRequire && $plugin->getComposerName() && !$this->container->getParameter('heypanel.deployment.cluster_setup')) {
                $this->executor->remove($plugin->getComposerName(), $plugin->getName());
            }

            throw $e;
        }

        return $installContext;
    }

    /**
     * @throws PluginNotInstalledException
     */
    public function uninstallPlugin(
        PluginEntity $plugin,
        Context $heypanelContext,
        bool $keepUserData = false
    ): UninstallContext {
        if ($plugin->getInstalledAt() === null) {
            throw PluginException::notInstalled($plugin->getName());
        }

        if ($plugin->getActive()) {
            $this->deactivatePlugin($plugin, $heypanelContext);
        }

        $pluginBaseClassString = $plugin->getBaseClass();
        $pluginBaseClass = $this->getPluginBaseClass($pluginBaseClassString);

        $uninstallContext = new UninstallContext(
            $pluginBaseClass,
            $heypanelContext,
            $this->heypanelVersion,
            $plugin->getVersion(),
            $this->createMigrationCollection($pluginBaseClass),
            $keepUserData
        );
        $uninstallContext->setAutoMigrate(false);

        $this->eventDispatcher->dispatch(new PluginPreUninstallEvent($plugin, $uninstallContext));

        if (!$heypanelContext->hasState(self::STATE_SKIP_ASSET_BUILDING)) {
            $this->assetInstaller->removeAssetsOfBundle($pluginBaseClassString);
        }

        if (!$uninstallContext->keepUserData()) {
            // plugin->uninstall() will remove the tables etc of the plugin,
            // we drop the migrations before, so we can recover in case of errors by rerunning the migrations
            $pluginBaseClass->removeMigrations();
        }

        $pluginBaseClass->uninstall($uninstallContext);

        if (!$uninstallContext->keepUserData()) {
            $this->systemConfigService->deletePluginConfiguration($pluginBaseClass);
        }

        $pluginId = $plugin->getId();
        $this->updatePluginData(
            [
                'id' => $pluginId,
                'active' => false,
                'installedAt' => null,
            ],
            $heypanelContext
        );
        $plugin->setActive(false);
        $plugin->setInstalledAt(null);

        if ($pluginBaseClass->executeComposerCommands()) {
            $this->executeComposerRemoveCommand($plugin, $heypanelContext);
        }

        $this->eventDispatcher->dispatch(new PluginPostUninstallEvent($plugin, $uninstallContext));

        return $uninstallContext;
    }

    /**
     * @throws RequirementStackException
     */
    public function updatePlugin(PluginEntity $plugin, Context $heypanelContext): UpdateContext
    {
        if ($plugin->getInstalledAt() === null) {
            throw PluginException::notInstalled($plugin->getName());
        }

        $pluginBaseClassString = $plugin->getBaseClass();
        $pluginBaseClass = $this->getPluginBaseClass($pluginBaseClassString);

        $updateContext = new UpdateContext(
            $pluginBaseClass,
            $heypanelContext,
            $this->heypanelVersion,
            $plugin->getVersion(),
            $this->createMigrationCollection($pluginBaseClass),
            $plugin->getUpgradeVersion() ?? $plugin->getVersion()
        );

        if ($pluginBaseClass->executeComposerCommands()) {
            $this->executeComposerRequireWhenNeeded($plugin, $pluginBaseClass, $updateContext->getUpdatePluginVersion(), $heypanelContext);
        } else {
            if ($plugin->getManagedByComposer() && $plugin->isLocatedInCustomDirectory()) {
                // If the plugin was previously managed by composer, but should no longer due to the update, we need to remove the composer dependency
                $this->executeComposerRemoveCommand($plugin, $heypanelContext);
            }
            $this->requirementValidator->validateRequirements($plugin, $heypanelContext, 'update');
        }

        $this->eventDispatcher->dispatch(new PluginPreUpdateEvent($plugin, $updateContext));

        $this->systemConfigService->savePluginConfiguration($pluginBaseClass);

        try {
            $pluginBaseClass->update($updateContext);
        } catch (\Throwable $updateException) {
            if ($plugin->getActive()) {
                try {
                    $this->deactivatePlugin($plugin, $heypanelContext);
                } catch (\Throwable) {
                    $this->updatePluginData(
                        [
                            'id' => $plugin->getId(),
                            'active' => false,
                        ],
                        $heypanelContext
                    );
                }
            }

            throw $updateException;
        }

        if ($plugin->getActive() && !$heypanelContext->hasState(self::STATE_SKIP_ASSET_BUILDING)) {
            $this->assetInstaller->copyAssetsFromBundle($pluginBaseClassString);
        }

        $this->runMigrations($updateContext);

        $updateVersion = $updateContext->getUpdatePluginVersion();
        $updateDate = new \DateTime();
        $this->updatePluginData(
            [
                'id' => $plugin->getId(),
                'version' => $updateVersion,
                'upgradeVersion' => null,
                'upgradedAt' => $updateDate->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ],
            $heypanelContext
        );
        $plugin->setVersion($updateVersion);
        $plugin->setUpgradeVersion(null);
        $plugin->setUpgradedAt($updateDate);

        $pluginBaseClass->postUpdate($updateContext);

        $this->eventDispatcher->dispatch(new PluginPostUpdateEvent($plugin, $updateContext));

        return $updateContext;
    }

    /**
     * @throws PluginNotInstalledException
     */
    public function activatePlugin(PluginEntity $plugin, Context $heypanelContext, bool $reactivate = false): ActivateContext
    {
        if ($plugin->getInstalledAt() === null) {
            throw PluginException::notInstalled($plugin->getName());
        }

        $pluginBaseClassString = $plugin->getBaseClass();
        $pluginBaseClass = $this->getPluginBaseClass($pluginBaseClassString);

        $activateContext = new ActivateContext(
            $pluginBaseClass,
            $heypanelContext,
            $this->heypanelVersion,
            $plugin->getVersion(),
            $this->createMigrationCollection($pluginBaseClass)
        );

        if ($reactivate === false && $plugin->getActive()) {
            return $activateContext;
        }

        $this->requirementValidator->validateRequirements($plugin, $heypanelContext, 'activate');

        $this->eventDispatcher->dispatch(new PluginPreActivateEvent($plugin, $activateContext));

        $plugin->setActive(true);

        // only skip rebuild if plugin has overwritten rebuildContainer method and source is system source (CLI)
        if ($pluginBaseClass->rebuildContainer() || !$heypanelContext->getSource() instanceof SystemSource) {
            $this->rebuildContainerWithNewPluginState($plugin, $pluginBaseClass->getNamespace());
        }

        $pluginBaseClass = $this->getPluginInstance($pluginBaseClassString);
        $activateContext = new ActivateContext(
            $pluginBaseClass,
            $heypanelContext,
            $this->heypanelVersion,
            $plugin->getVersion(),
            $this->createMigrationCollection($pluginBaseClass)
        );
        $activateContext->setAutoMigrate(false);

        $pluginBaseClass->activate($activateContext);

        $this->runMigrations($activateContext);

        if (!$heypanelContext->hasState(self::STATE_SKIP_ASSET_BUILDING)) {
            $this->assetInstaller->copyAssetsFromBundle($pluginBaseClassString);
        }

        $this->updatePluginData(
            [
                'id' => $plugin->getId(),
                'active' => true,
            ],
            $heypanelContext
        );

        $this->signalWorkerStopInOldCacheDir();

        $this->eventDispatcher->dispatch(new PluginPostActivateEvent($plugin, $activateContext));

        return $activateContext;
    }

    /**
     * @throws PluginNotInstalledException
     * @throws PluginNotActivatedException
     * @throws PluginHasActiveDependantsException
     */
    public function deactivatePlugin(PluginEntity $plugin, Context $heypanelContext): DeactivateContext
    {
        if ($plugin->getInstalledAt() === null) {
            throw PluginException::notInstalled($plugin->getName());
        }

        if ($plugin->getActive() === false) {
            throw PluginException::notActivated($plugin->getName());
        }

        $dependantPlugins = array_values($this->getEntities($this->pluginCollection->all(), $heypanelContext)->getEntities()->getElements());

        $dependants = $this->requirementValidator->resolveActiveDependants(
            $plugin,
            $dependantPlugins
        );

        if (\count($dependants) > 0) {
            throw PluginException::hasActiveDependants($plugin->getName(), $dependants);
        }

        $pluginBaseClassString = $plugin->getBaseClass();
        $pluginBaseClass = $this->getPluginInstance($pluginBaseClassString);

        $deactivateContext = new DeactivateContext(
            $pluginBaseClass,
            $heypanelContext,
            $this->heypanelVersion,
            $plugin->getVersion(),
            $this->createMigrationCollection($pluginBaseClass)
        );
        $deactivateContext->setAutoMigrate(false);

        $this->eventDispatcher->dispatch(new PluginPreDeactivateEvent($plugin, $deactivateContext));

        try {
            $pluginBaseClass->deactivate($deactivateContext);

            if (!$heypanelContext->hasState(self::STATE_SKIP_ASSET_BUILDING)) {
                $this->assetInstaller->removeAssetsOfBundle($plugin->getName());
            }

            $plugin->setActive(false);

            // only skip rebuild if plugin has overwritten rebuildContainer method and source is system source (CLI)
            if ($pluginBaseClass->rebuildContainer() || !$heypanelContext->getSource() instanceof SystemSource) {
                $this->rebuildContainerWithNewPluginState($plugin, $pluginBaseClass->getNamespace());
            }

            $this->updatePluginData(
                [
                    'id' => $plugin->getId(),
                    'active' => false,
                ],
                $heypanelContext
            );
        } catch (\Throwable $exception) {
            $activateContext = new ActivateContext(
                $pluginBaseClass,
                $heypanelContext,
                $this->heypanelVersion,
                $plugin->getVersion(),
                $this->createMigrationCollection($pluginBaseClass)
            );

            $this->eventDispatcher->dispatch(
                new PluginPostDeactivationFailedEvent(
                    $plugin,
                    $activateContext,
                    $exception
                )
            );

            throw $exception;
        }

        $this->signalWorkerStopInOldCacheDir();

        $this->eventDispatcher->dispatch(new PluginPostDeactivateEvent($plugin, $deactivateContext));

        return $deactivateContext;
    }

    /**
     * Only run composer remove as last thing in the request context,
     * as there might be some other event listeners that will break after the composer dependency is removed.
     *
     * This is not run on Kernel Terminate as this way we can give feedback to the user by letting the request fail,
     * if there is an issue with removing the composer dependency.
     */
    public function onResponse(): void
    {
        if (!self::$pluginToBeDeleted) {
            return;
        }

        $plugin = self::$pluginToBeDeleted['plugin'];
        $context = self::$pluginToBeDeleted['context'];
        self::$pluginToBeDeleted = null;

        $this->removePluginComposerDependency($plugin, $context);
    }

    private function removePluginComposerDependency(PluginEntity $plugin, Context $context): void
    {
        if ($this->container->getParameter('heypanel.deployment.cluster_setup')) {
            return;
        }

        $pluginComposerName = $plugin->getComposerName();
        if ($pluginComposerName === null) {
            throw PluginException::composerJsonInvalid(
                $plugin->getPath() . '/composer.json',
                ['No name defined in composer.json']
            );
        }

        $this->executor->remove($pluginComposerName, $plugin->getName());

        // running composer require may have consequences for other plugins, when they are required by the plugin being uninstalled
        $this->pluginService->refreshPlugins($context, new NullIO());
    }

    private function getPluginBaseClass(string $pluginBaseClassString): Plugin
    {
        $baseClass = $this->pluginCollection->get($pluginBaseClassString);

        if ($baseClass === null) {
            throw PluginException::baseClassNotFound($pluginBaseClassString);
        }

        // set container because the plugin has not been initialized yet and therefore has no container set
        $baseClass->setContainer($this->container);

        return $baseClass;
    }

    private function createMigrationCollection(Plugin $pluginBaseClass): MigrationCollection
    {
        $migrationPath = str_replace(
            '\\',
            '/',
            $pluginBaseClass->getPath() . str_replace(
                $pluginBaseClass->getNamespace(),
                '',
                $pluginBaseClass->getMigrationNamespace()
            )
        );

        if (!is_dir($migrationPath)) {
            return $this->migrationLoader->collect('null');
        }

        $this->migrationLoader->addSource(new MigrationSource($pluginBaseClass->getName(), [
            $migrationPath => $pluginBaseClass->getMigrationNamespace(),
        ]));

        $collection = $this->migrationLoader
            ->collect($pluginBaseClass->getName());

        $collection->sync();

        return $collection;
    }

    private function runMigrations(InstallContext $context): void
    {
        if (!$context->isAutoMigrate()) {
            return;
        }

        $context->getMigrationCollection()->migrateInPlace();
    }

    private function hasPluginUpdate(string $updateVersion, string $currentVersion): bool
    {
        return version_compare($updateVersion, $currentVersion, '>');
    }

    /**
     * @param array<string, mixed|null> $pluginData
     */
    private function updatePluginData(array $pluginData, Context $context): void
    {
        $this->pluginRepo->update([$pluginData], $context);
    }

    private function rebuildContainerWithNewPluginState(PluginEntity $plugin, string $pluginNamespace): void
    {
        $kernel = $this->container->get('kernel');

        $pluginDir = $kernel->getContainer()->getParameter('kernel.plugin_dir');
        if (!\is_string($pluginDir)) {
            throw PluginException::invalidContainerParameter('kernel.plugin_dir', 'string');
        }

        $pluginLoader = $this->container->get(KernelPluginLoader::class);

        $plugins = $pluginLoader->getPluginInfos();
        foreach ($plugins as $i => $pluginData) {
            if ($pluginData['baseClass'] === $plugin->getBaseClass()) {
                $plugins[$i]['active'] = $plugin->getActive();
            }
        }

        if (!$plugin->getActive()) {
            $this->clearEntityExtensions($pluginNamespace);
        }

        /*
         * Reboot kernel with $plugin active=true.
         *
         * All other Requests won't have this plugin active until it's updated in the db
         */
        $tmpStaticPluginLoader = new StaticKernelPluginLoader($pluginLoader->getClassLoader(), $pluginDir, $plugins);
        $kernel->reboot(null, $tmpStaticPluginLoader);

        try {
            $newContainer = $kernel->getContainer();
        } catch (\LogicException) {
            // If symfony throws an exception when calling getContainer on a not booted kernel and catch it here
            throw PluginException::failedKernelReboot();
        }

        $this->container = $newContainer;
        $this->eventDispatcher = $newContainer->get('event_dispatcher');
    }

    private function clearEntityExtensions(string $pluginNamespace): void
    {
        if ($pluginNamespace === '') {
            return;
        }

        $definitions = $this->definitionRegistry->getDefinitions();
        foreach ($definitions as $definition) {
            $definition->removeExtensions($pluginNamespace);
        }
    }

    private function getPluginInstance(string $pluginBaseClassString): Plugin
    {
        if ($this->container->has($pluginBaseClassString)) {
            $containerPlugin = $this->container->get($pluginBaseClassString);
            if (!$containerPlugin instanceof Plugin) {
                throw PluginException::wrongBaseClass($pluginBaseClassString);
            }

            return $containerPlugin;
        }

        return $this->getPluginBaseClass($pluginBaseClassString);
    }

    private function signalWorkerStopInOldCacheDir(): void
    {
        $cacheItem = $this->restartSignalCachePool->getItem(StopWorkerOnRestartSignalListener::RESTART_REQUESTED_TIMESTAMP_KEY);
        $cacheItem->set(microtime(true));
        $this->restartSignalCachePool->save($cacheItem);
    }

    /**
     * Takes plugin base classes and returns the corresponding entities.
     *
     * @param Plugin[] $plugins
     *
     * @return EntitySearchResult<PluginCollection>
     */
    private function getEntities(array $plugins, Context $context): EntitySearchResult
    {
        $names = array_map(static fn (Plugin $plugin) => $plugin->getName(), $plugins);

        return $this->pluginRepo->search(
            (new Criteria())->addFilter(new EqualsAnyFilter('name', $names)),
            $context
        );
    }

    private function executeComposerRequireWhenNeeded(PluginEntity $plugin, Plugin $pluginBaseClass, string $pluginVersion, Context $heypanelContext): bool
    {
        if ($this->container->getParameter('heypanel.deployment.cluster_setup')) {
            return false;
        }

        $pluginComposerName = $plugin->getComposerName();
        if ($pluginComposerName === null) {
            throw PluginException::composerJsonInvalid(
                $pluginBaseClass->getPath() . '/composer.json',
                ['No name defined in composer.json']
            );
        }

        try {
            $installedVersion = InstalledVersions::getVersion($pluginComposerName);
        } catch (\OutOfBoundsException) {
            // plugin is not installed using composer yet
            $installedVersion = null;
        }

        if ($installedVersion !== null) {
            $sanitizedVersion = $this->versionSanitizer->sanitizePluginVersion($installedVersion);

            if (Comparator::equalTo($sanitizedVersion, $pluginVersion)) {
                // plugin was already required at build time, no need to do so again at runtime
                return false;
            }
        }

        $this->executor->require($pluginComposerName . ':' . $pluginVersion, $plugin->getName());

        // running composer require may have consequences for other plugins, when they are required by the plugin being installed
        $this->pluginService->refreshPlugins($heypanelContext, new NullIO());

        return true;
    }

    private function executeComposerRemoveCommand(PluginEntity $plugin, Context $heypanelContext): void
    {
        if (\PHP_SAPI === 'cli') {
            // only remove the plugin composer dependency directly when running in CLI
            // otherwise do it async in kernel.response
            $this->removePluginComposerDependency($plugin, $heypanelContext);
        /* @codeCoverageIgnoreStart -> code path can not be executed in unit tests as SAPI will always be CLI */
        } else {
            self::$pluginToBeDeleted = [
                'plugin' => $plugin,
                'context' => $heypanelContext,
            ];

            if (!self::$registeredListener) {
                $this->eventDispatcher->addListener(KernelEvents::RESPONSE, $this->onResponse(...), \PHP_INT_MAX);
                self::$registeredListener = true;
            }
        }
        /* @codeCoverageIgnoreEnd */
    }
}
