<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Telemetry;

use HeyPanel\Core\Framework\HttpException;
use HeyPanel\Core\Framework\Telemetry\Metrics\Exception\MetricNotSupportedException;
use HeyPanel\Core\Framework\Telemetry\Metrics\Exception\MissingMetricConfigurationException;
use HeyPanel\Core\Framework\Telemetry\Metrics\Metric\Metric;
use HeyPanel\Core\Framework\Telemetry\Metrics\MetricTransportInterface;

/**
 * @experimental feature:TELEMETRY_METRICS stableVersion:v6.8.0
 */
abstract class TelemetryException extends HttpException
{
    public static function metricNotSupported(
        Metric $metric,
        MetricTransportInterface $transport
    ): MetricNotSupportedException {
        return new MetricNotSupportedException(
            metric: $metric,
            transport: $transport,
            message: \sprintf('Metric %s, not supported by transport %s', $metric::class, $transport::class),
        );
    }

    /**
     * @internal
     */
    public static function metricMissingConfiguration(string $metric): MissingMetricConfigurationException
    {
        return new MissingMetricConfigurationException(
            metric: $metric,
            message: \sprintf('Missing configuration for metric %s', $metric),
        );
    }
}
