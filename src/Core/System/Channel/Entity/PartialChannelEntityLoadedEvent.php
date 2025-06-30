<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Channel\Entity;

use HeyPanel\Core\Framework\DataAbstractionLayer\EntityDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\PartialEntity;
use HeyPanel\Core\System\Channel\ChannelContext;

/**
 * @extends ChannelEntityLoadedEvent<PartialEntity>
 */
class PartialChannelEntityLoadedEvent extends ChannelEntityLoadedEvent
{
    /**
     * @param PartialEntity[] $entities
     */
    public function __construct(
        EntityDefinition $definition,
        array $entities,
        ChannelContext $context
    ) {
        parent::__construct($definition, $entities, $context);

        $this->name = $this->definition->getEntityName() . '.partial_loaded';
    }
}
