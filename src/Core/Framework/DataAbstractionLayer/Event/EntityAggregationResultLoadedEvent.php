<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Event;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\AggregationResult\AggregationResultCollection;
use HeyPanel\Core\Framework\Event\GenericEvent;
use HeyPanel\Core\Framework\Event\NestedEvent;

class EntityAggregationResultLoadedEvent extends NestedEvent implements GenericEvent
{
    protected string $name;

    public function __construct(
        protected EntityDefinition $definition,
        protected AggregationResultCollection $result,
        protected Context $context
    ) {
        $this->name = $this->definition->getEntityName() . '.aggregation.result.loaded';
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function getResult(): AggregationResultCollection
    {
        return $this->result;
    }
}
