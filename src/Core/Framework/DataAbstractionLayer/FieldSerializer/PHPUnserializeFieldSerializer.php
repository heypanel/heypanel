<?php
declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\FieldSerializer;

use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Field;
use HeyPanel\Core\Framework\DataAbstractionLayer\Write\DataStack\KeyValuePair;
use HeyPanel\Core\Framework\DataAbstractionLayer\Write\EntityExistence;
use HeyPanel\Core\Framework\DataAbstractionLayer\Write\WriteParameterBag;

/**
 * @internal
 */
class PHPUnserializeFieldSerializer extends AbstractFieldSerializer
{
    /**
     * @internal
     */
    public function __construct()
    {
    }

    public function encode(Field $field, EntityExistence $existence, KeyValuePair $data, WriteParameterBag $parameters): \Generator
    {
        throw new \RuntimeException('Serialized fields can only be written by an indexer');
    }

    public function decode(Field $field, mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        return unserialize($value);
    }
}
