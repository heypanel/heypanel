<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Telemetry\Metrics;

use HeyPanel\Core\Framework\Telemetry\Metrics\Exception\MetricNotSupportedException;
use HeyPanel\Core\Framework\Telemetry\Metrics\Metric\Metric;

/**
 * @experimental feature:TELEMETRY_METRICS stableVersion:v6.8.0
 */
interface MetricTransportInterface
{
    /**
     * @throws MetricNotSupportedException
     */
    public function emit(Metric $metric): void;
}
