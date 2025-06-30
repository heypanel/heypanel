<?php

declare(strict_types=1);

namespace HeyPanel\Core\Framework\Api\HealthCheck\Event;

use HeyPanel\Core\Framework\Context;
use Symfony\Contracts\EventDispatcher\Event;

class HealthCheckEvent extends Event
{
    public function __construct(
        public readonly Context $context
    ) {
    }
}
