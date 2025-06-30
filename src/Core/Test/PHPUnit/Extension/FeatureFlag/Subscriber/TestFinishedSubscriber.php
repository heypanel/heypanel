<?php declare(strict_types=1);

namespace HeyPanel\Core\Test\PHPUnit\Extension\FeatureFlag\Subscriber;

use HeyPanel\Core\Framework\Feature;
use HeyPanel\Core\Test\PHPUnit\Extension\FeatureFlag\SavedConfig;
use PHPUnit\Event\Test\Finished;
use PHPUnit\Event\Test\FinishedSubscriber;

/**
 * @internal
 */
class TestFinishedSubscriber implements FinishedSubscriber
{
    public function __construct(private readonly SavedConfig $savedConfig)
    {
    }

    public function notify(Finished $event): void
    {
        if ($this->savedConfig->savedFeatureConfig === null) {
            return;
        }

        $_SERVER = $this->savedConfig->savedServerVars;

        Feature::resetRegisteredFeatures();
        Feature::registerFeatures($this->savedConfig->savedFeatureConfig);

        $this->savedConfig->savedFeatureConfig = null;
    }
}
