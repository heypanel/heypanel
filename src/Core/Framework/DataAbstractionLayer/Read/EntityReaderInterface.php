<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Read;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\DataAbstractionLayer\Entity;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityCollection;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Criteria;

/**
 * @internal
 */
interface EntityReaderInterface
{
    /**
     * @return EntityCollection<Entity>
     */
    public function read(EntityDefinition $definition, Criteria $criteria, Context $context): EntityCollection;
}
