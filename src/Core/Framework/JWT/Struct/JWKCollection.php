<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\JWT\Struct;

use HeyPanel\Core\Framework\JWT\JWTException;
use HeyPanel\Core\Framework\Struct\AssignArrayTrait;
use HeyPanel\Core\Framework\Struct\Collection;
use HeyPanel\Core\Framework\Validation\ValidatorFactory;

/**
 * @phpstan-import-type JSONWebKey from JWKStruct
 *
 * @extends Collection<JWKStruct>
 */
class JWKCollection extends Collection
{
    use AssignArrayTrait;

    /**
     * @param array{keys: array<int, JSONWebKey>} $data
     */
    public static function fromArray(array $data): self
    {
        $elements['elements'] = \array_map(static function (array $element): JWKStruct {
            $dto = ValidatorFactory::create($element, JWKStruct::class);
            if (!$dto instanceof JWKStruct) {
                throw JWTException::invalidType(JWKStruct::class, $dto::class);
            }

            return $dto;
        }, $data['keys']);

        return (new self())->assign($elements);
    }
}
