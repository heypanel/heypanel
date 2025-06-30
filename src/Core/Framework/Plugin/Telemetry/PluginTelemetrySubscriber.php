<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Plugin\Telemetry;

use HeyPanel\Core\Framework\Plugin\Event\PluginPostInstallEvent;
use HeyPanel\Core\Framework\Telemetry\Metrics\Meter;
use HeyPanel\Core\Framework\Telemetry\Metrics\Metric\ConfiguredMetric;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @internal
 */
class PluginTelemetrySubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly Meter $meter)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PluginPostInstallEvent::class => 'emitPluginInstallCountMetric',
        ];
    }

    public function emitPluginInstallCountMetric(): void
    {
        $this->meter->emit(new ConfiguredMetric(name: 'plugin.install.count', value: 1));
    }
}
