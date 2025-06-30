<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Flow\Events;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\Event\NestedEvent;

class FlowIndexerEvent extends NestedEvent
{
    public function __construct(
        private readonly array $ids,
        private readonly Context $context
    ) {
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function getIds(): array
    {
        return $this->ids;
    }
}
