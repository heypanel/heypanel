<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Search\AggregationResult\Metric;

use HeyPanel\Core\Framework\DataAbstractionLayer\Search\AggregationResult\AggregationResult;

/**
 * @final
 */
class CountResult extends AggregationResult
{
    public function __construct(
        string $name,
        protected int $count
    ) {
        parent::__construct($name);
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
