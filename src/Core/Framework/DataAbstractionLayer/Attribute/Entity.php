<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Attribute;

use HeyPanel\Core\Framework\DataAbstractionLayer\Dbal\EntityHydrator;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityCollection;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class Entity
{
    /**
     * @var class-string
     */
    public string $class;

    /**
     * @param class-string<EntityCollection> $collectionClass
     * @param class-string<EntityHydrator> $hydratorClass
     *
     * @phpstan-ignore missingType.generics (At this point it is not really possible to determine the correct entity class)
     */
    public function __construct(
        public string $name,
        public ?string $parent = null,
        public ?string $since = null,
        public string $collectionClass = EntityCollection::class,
        public string $hydratorClass = EntityHydrator::class,
    ) {
    }
}
