<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Plugin\Event;

use HeyPanel\Core\Framework\Plugin\PluginEntity;
use Symfony\Contracts\EventDispatcher\Event;

abstract class PluginLifecycleEvent extends Event
{
    public function __construct(private readonly PluginEntity $plugin)
    {
    }

    public function getPlugin(): PluginEntity
    {
        return $this->plugin;
    }
}
