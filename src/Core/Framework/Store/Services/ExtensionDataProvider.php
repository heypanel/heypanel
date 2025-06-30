<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Store\Services;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityRepository;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Criteria;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use HeyPanel\Core\Framework\Plugin\Exception\DecorationPatternException;
use HeyPanel\Core\Framework\Plugin\PluginCollection;
use HeyPanel\Core\Framework\Store\Event\InstalledExtensionsListingLoadedEvent;
use HeyPanel\Core\Framework\Store\Struct\ExtensionCollection;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * @internal
 */
class ExtensionDataProvider extends AbstractExtensionDataProvider
{
    final public const HEADER_NAME_TOTAL_COUNT = 'SW-Meta-Total';

    public function __construct(
        private readonly ExtensionLoader $extensionLoader,
        private readonly EntityRepository $pluginRepository,
        private readonly ExtensionListingLoader $extensionListingLoader,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function getInstalledExtensions(Context $context, bool $loadCloudExtensions = true, ?Criteria $searchCriteria = null): ExtensionCollection
    {
        $appCriteria = $searchCriteria ? clone $searchCriteria : new Criteria();
        $appCriteria->addAssociation('translations');
        $appCriteria->addFilter(new EqualsFilter('selfManaged', false));

        $pluginCriteria = $searchCriteria ? clone $searchCriteria : new Criteria();
        $pluginCriteria->addAssociation('translations');

        /** @var PluginCollection $installedPlugins */
        $installedPlugins = $this->pluginRepository->search($pluginCriteria, $context)->getEntities();

        $pluginCollection = $this->extensionLoader->loadFromPluginCollection($context, $installedPlugins);

        if ($loadCloudExtensions) {
            $pluginCollection = $this->extensionListingLoader->load($pluginCollection, $context);
        }

        $this->eventDispatcher->dispatch($event = new InstalledExtensionsListingLoadedEvent($pluginCollection, $context));

        return $event->extensionCollection;
    }

    protected function getDecorated(): AbstractExtensionDataProvider
    {
        throw new DecorationPatternException(self::class);
    }
}
