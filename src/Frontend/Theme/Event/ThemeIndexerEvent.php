<?php declare(strict_types=1);

namespace HeyPanel\Frontend\Theme\Event;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\Event\NestedEvent;

class ThemeIndexerEvent extends NestedEvent
{
    public function __construct(
        private readonly array $ids,
        private readonly Context $context,
        private readonly array $skip = []
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

    public function getSkip(): array
    {
        return $this->skip;
    }
}
