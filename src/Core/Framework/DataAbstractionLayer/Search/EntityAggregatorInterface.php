<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Search;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\AggregationResult\AggregationResultCollection;

/**
 * @internal
 */
interface EntityAggregatorInterface
{
    public function aggregate(EntityDefinition $definition, Criteria $criteria, Context $context): AggregationResultCollection;
}
