<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Event;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\PartialEntity;

/**
 * @extends EntityLoadedEvent<PartialEntity>
 */
class PartialEntityLoadedEvent extends EntityLoadedEvent
{
    /**
     * @param PartialEntity[] $entities
     */
    public function __construct(
        EntityDefinition $definition,
        array $entities,
        Context $context
    ) {
        parent::__construct($definition, $entities, $context);
        $this->name = $this->definition->getEntityName() . '.partial_loaded';
    }
}
