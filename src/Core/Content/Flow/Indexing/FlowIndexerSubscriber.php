<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Flow\Indexing;

use HeyPanel\Core\Framework\App\Event\AppActivatedEvent;
use HeyPanel\Core\Framework\App\Event\AppDeactivatedEvent;
use HeyPanel\Core\Framework\App\Event\AppDeletedEvent;
use HeyPanel\Core\Framework\App\Event\AppInstalledEvent;
use HeyPanel\Core\Framework\App\Event\AppUpdatedEvent;
use HeyPanel\Core\Framework\DataAbstractionLayer\Indexing\MessageQueue\IterateEntityIndexerMessage;
use HeyPanel\Core\Framework\Plugin\Event\PluginPostActivateEvent;
use HeyPanel\Core\Framework\Plugin\Event\PluginPostDeactivateEvent;
use HeyPanel\Core\Framework\Plugin\Event\PluginPostInstallEvent;
use HeyPanel\Core\Framework\Plugin\Event\PluginPostUninstallEvent;
use HeyPanel\Core\Framework\Plugin\Event\PluginPostUpdateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @internal
 */
class FlowIndexerSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly MessageBusInterface $messageBus)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PluginPostInstallEvent::class => 'refreshPlugin',
            PluginPostActivateEvent::class => 'refreshPlugin',
            PluginPostUpdateEvent::class => 'refreshPlugin',
            PluginPostDeactivateEvent::class => 'refreshPlugin',
            PluginPostUninstallEvent::class => 'refreshPlugin',
            AppInstalledEvent::class => 'refreshPlugin',
            AppUpdatedEvent::class => 'refreshPlugin',
            AppActivatedEvent::class => 'refreshPlugin',
            AppDeletedEvent::class => 'refreshPlugin',
            AppDeactivatedEvent::class => 'refreshPlugin',
        ];
    }

    public function refreshPlugin(): void
    {
        // Schedule indexer to update flows
        $this->messageBus->dispatch(new IterateEntityIndexerMessage(FlowIndexer::NAME, null));
    }
}
