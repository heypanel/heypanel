<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Rule\DataAbstractionLayer;

use Doctrine\DBAL\Connection;
use HeyPanel\Core\Framework\DataAbstractionLayer\Doctrine\RetryableQuery;
use HeyPanel\Core\Framework\Plugin\Event\PluginPostActivateEvent;
use HeyPanel\Core\Framework\Plugin\Event\PluginPostDeactivateEvent;
use HeyPanel\Core\Framework\Plugin\Event\PluginPostInstallEvent;
use HeyPanel\Core\Framework\Plugin\Event\PluginPostUninstallEvent;
use HeyPanel\Core\Framework\Plugin\Event\PluginPostUpdateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @internal
 */
class RuleIndexerSubscriber implements EventSubscriberInterface
{
    /**
     * @internal
     */
    public function __construct(
        private readonly Connection $connection
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PluginPostInstallEvent::class => 'refreshPlugin',
            PluginPostActivateEvent::class => 'refreshPlugin',
            PluginPostUpdateEvent::class => 'refreshPlugin',
            PluginPostDeactivateEvent::class => 'refreshPlugin',
            PluginPostUninstallEvent::class => 'refreshPlugin',
        ];
    }

    public function refreshPlugin(): void
    {
        // Delete the payload and invalid flag of all rules
        $update = new RetryableQuery(
            $this->connection,
            $this->connection->prepare('UPDATE `rule` SET `payload` = null, `invalid` = 0')
        );
        $update->execute();
    }
}
