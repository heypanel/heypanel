<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Category\Event;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\Event\NestedEvent;

class CategoryIndexerEvent extends NestedEvent
{
    /**
     * @var list<string>
     */
    protected array $ids;

    protected Context $context;

    /**
     * @param list<string> $ids
     * @param array<string> $skip
     */
    public function __construct(
        array $ids,
        Context $context,
        private readonly array $skip = [],
        public bool $isFullIndexing = false
    ) {
        $this->ids = $ids;
        $this->context = $context;
    }

    /**
     * @return list<string>
     */
    public function getIds(): array
    {
        return $this->ids;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    /**
     * @return array<string>
     */
    public function getSkip(): array
    {
        return $this->skip;
    }
}
