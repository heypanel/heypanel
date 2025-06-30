<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Event;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityCollection;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use HeyPanel\Core\Framework\Event\GenericEvent;
use HeyPanel\Core\Framework\Event\NestedEvent;

/**
 * @template TEntityCollection of EntityCollection
 */
class EntitySearchResultLoadedEvent extends NestedEvent implements GenericEvent
{
    protected string $name;

    /**
     * @param EntitySearchResult<TEntityCollection> $result
     */
    public function __construct(
        protected EntityDefinition $definition,
        protected EntitySearchResult $result
    ) {
        $this->name = $this->definition->getEntityName() . '.search.result.loaded';
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getContext(): Context
    {
        return $this->result->getContext();
    }

    /**
     * @return EntitySearchResult<TEntityCollection>
     */
    public function getResult(): EntitySearchResult
    {
        return $this->result;
    }
}
