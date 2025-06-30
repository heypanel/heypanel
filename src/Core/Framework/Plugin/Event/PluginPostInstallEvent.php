<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Plugin\Event;

use HeyPanel\Core\Framework\Plugin\Context\InstallContext;
use HeyPanel\Core\Framework\Plugin\PluginEntity;

class PluginPostInstallEvent extends PluginLifecycleEvent
{
    public function __construct(
        PluginEntity $plugin,
        private readonly InstallContext $context
    ) {
        parent::__construct($plugin);
    }

    public function getContext(): InstallContext
    {
        return $this->context;
    }
}
