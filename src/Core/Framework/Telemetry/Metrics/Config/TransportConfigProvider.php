<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Telemetry\Metrics\Config;

use HeyPanel\Core\Framework\Telemetry\Metrics\Metric\Type;

/**
 * @internal
 *
 * @phpstan-import-type MetricDefinition from MetricConfig
 */
class TransportConfigProvider
{
    public function __construct(private readonly MetricConfigProvider $metricConfigProvider)
    {
    }

    public function getTransportConfig(): TransportConfig
    {
        return new TransportConfig(metricsConfig: $this->metricConfigProvider->all());
    }
}
