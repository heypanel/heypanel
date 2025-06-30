<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\ImportExport\DataAbstractionLayer\Serializer\Field;

use HeyPanel\Core\Content\ImportExport\DataAbstractionLayer\Serializer\SerializerRegistry;
use HeyPanel\Core\Content\ImportExport\Struct\Config;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Field;

abstract class AbstractFieldSerializer
{
    protected SerializerRegistry $serializerRegistry;

    /**
     * @param mixed $value
     *
     * @return iterable<string, mixed>
     */
    abstract public function serialize(Config $config, Field $field, $value): iterable;

    /**
     * @param mixed $value
     */
    abstract public function deserialize(Config $config, Field $field, $value): mixed;

    abstract public function supports(Field $field): bool;

    public function setRegistry(SerializerRegistry $serializerRegistry): void
    {
        $this->serializerRegistry = $serializerRegistry;
    }

    abstract public function getDecorated(): AbstractFieldSerializer;
}
