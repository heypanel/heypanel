<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Plugin\Event;

use HeyPanel\Core\Framework\Plugin\Context\UpdateContext;
use HeyPanel\Core\Framework\Plugin\PluginEntity;

class PluginPreUpdateEvent extends PluginLifecycleEvent
{
    public function __construct(
        PluginEntity $plugin,
        private readonly UpdateContext $context
    ) {
        parent::__construct($plugin);
    }

    public function getContext(): UpdateContext
    {
        return $this->context;
    }
}
