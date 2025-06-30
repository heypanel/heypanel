<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Event;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;
use HeyPanel\Core\Framework\Event\GenericEvent;
use HeyPanel\Core\Framework\Event\NestedEvent;

class EntityIdSearchResultLoadedEvent extends NestedEvent implements GenericEvent
{
    protected string $name;

    public function __construct(
        protected EntityDefinition $definition,
        protected IdSearchResult $result
    ) {
        $this->name = $this->definition->getEntityName() . '.id.search.result.loaded';
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getContext(): Context
    {
        return $this->result->getContext();
    }

    public function getResult(): IdSearchResult
    {
        return $this->result;
    }
}
