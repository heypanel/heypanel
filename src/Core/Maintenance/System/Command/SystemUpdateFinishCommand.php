<?php declare(strict_types=1);

namespace HeyPanel\Core\Maintenance\System\Command;

use HeyPanel\Core\DevOps\Environment\EnvironmentHelper;
use HeyPanel\Core\Framework\Adapter\Console\HeyPanelStyle;
use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\Migration\MigrationCollectionLoader;
use HeyPanel\Core\Framework\Plugin\PluginLifecycleService;
use HeyPanel\Core\Framework\Update\Api\UpdateController;
use HeyPanel\Core\Framework\Update\Event\UpdatePostFinishEvent;
use HeyPanel\Core\Framework\Update\Event\UpdatePreFinishEvent;
use HeyPanel\Core\Maintenance\MaintenanceException;
use HeyPanel\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @internal should be used over the CLI only
 */
#[AsCommand(
    name: 'system:update:finish',
    description: 'Finishes the update process',
)]
class SystemUpdateFinishCommand extends Command
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly SystemConfigService $systemConfigService,
        private readonly string $heypanelVersion
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'skip-migrations',
                null,
                InputOption::VALUE_NONE,
                'Use this option to skip migrations'
            )
            ->addOption(
                'skip-asset-build',
                null,
                InputOption::VALUE_NONE,
                'Use this option to skip asset building'
            )
            ->addOption(
                'version-selection-mode',
                null,
                InputOption::VALUE_REQUIRED,
                \sprintf(
                    'Define upto which version destructive migrations are executed. Possible values: "%s".',
                    implode('", "', MigrationCollectionLoader::VALID_VERSION_SELECTION_VALUES)
                ),
                MigrationCollectionLoader::VERSION_SELECTION_SAFE
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new HeyPanelStyle($input, $output);

        $dsn = trim((string) EnvironmentHelper::getVariable('DATABASE_URL', getenv('DATABASE_URL')));
        if ($dsn === '') {
            $io->note('Environment variable \'DATABASE_URL\' not defined. Skipping ' . $this->getName() . '...');

            return self::SUCCESS;
        }

        $io->writeln('Run Post Update');
        $io->writeln('');

        $context = Context::createCLIContext();
        $oldVersion = $this->systemConfigService->getString(UpdateController::UPDATE_PREVIOUS_VERSION_KEY);

        if ($input->getOption('skip-asset-build')) {
            $context->addState(PluginLifecycleService::STATE_SKIP_ASSET_BUILDING);
        }

        $this->eventDispatcher->dispatch(new UpdatePreFinishEvent($context, $oldVersion, $this->heypanelVersion));

        if (!$input->getOption('skip-migrations')) {
            $this->runMigrations($io, $input);
        }

        $updateEvent = new UpdatePostFinishEvent($context, $oldVersion, $this->heypanelVersion);
        $this->eventDispatcher->dispatch($updateEvent);

        $io->writeln($updateEvent->getPostUpdateMessage());

        if (!$input->getOption('skip-asset-build')) {
            $exitCode = $this->installAssets($io);
            if ($exitCode !== self::SUCCESS) {
                $io->warning('Error while installing assets');
            }
        }

        $io->writeln('');

        return self::SUCCESS;
    }

    private function runMigrations(HeyPanelStyle $io, InputInterface $input): void
    {
        $application = $this->getConsoleApplication();

        $command = $application->find('database:migrate');
        $exitCode = $this->runCommand($application, $command, [
            'identifier' => 'core',
            '--all' => true,
        ], $io);
        if ($exitCode !== self::SUCCESS) {
            $io->warning('Error while running migrations');
        }

        $mode = (string) $input->getOption('version-selection-mode');
        if (!\in_array($mode, MigrationCollectionLoader::VALID_VERSION_SELECTION_VALUES, true)) {
            throw MaintenanceException::invalidVersionSelectionMode($mode);
        }
        $command = $application->find('database:migrate-destructive');
        $exitCode = $this->runCommand($application, $command, [
            'identifier' => 'core',
            '--all' => true,
            '--version-selection-mode' => $mode,
        ], $io);
        if ($exitCode !== self::SUCCESS) {
            $io->warning('Error while running destructive migrations');
        }
    }

    private function installAssets(HeyPanelStyle $io): int
    {
        $application = $this->getConsoleApplication();
        $command = $application->find('assets:install');

        return $this->runCommand($application, $command, [], $io);
    }

    /**
     * @param array<string, string|bool|null> $arguments
     */
    private function runCommand(Application $application, Command $command, array $arguments, HeyPanelStyle $io): int
    {
        \array_unshift($arguments, $command->getName());

        return $application->doRun(new ArrayInput($arguments), $io);
    }

    private function getConsoleApplication(): Application
    {
        $application = $this->getApplication();
        if (!$application instanceof Application) {
            throw MaintenanceException::consoleApplicationNotFound();
        }

        return $application;
    }
}
