<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Flow\Events;

use HeyPanel\Core\Content\Flow\Api\FlowActionCollectorResponse;
use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\Event\NestedEvent;

class FlowActionCollectorEvent extends NestedEvent
{
    public function __construct(
        private readonly FlowActionCollectorResponse $flowActionCollectorResponse,
        private readonly Context $context
    ) {
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function getCollection(): FlowActionCollectorResponse
    {
        return $this->flowActionCollectorResponse;
    }
}
