<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Plugin\Event;

use HeyPanel\Core\Framework\Plugin\Context\UninstallContext;
use HeyPanel\Core\Framework\Plugin\PluginEntity;

class PluginPostUninstallEvent extends PluginLifecycleEvent
{
    public function __construct(
        PluginEntity $plugin,
        private readonly UninstallContext $context
    ) {
        parent::__construct($plugin);
    }

    public function getContext(): UninstallContext
    {
        return $this->context;
    }
}
