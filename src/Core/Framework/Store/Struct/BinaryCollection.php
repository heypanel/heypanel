<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Store\Struct;

/**
 * @codeCoverageIgnore
 *
 * @template-extends StoreCollection<BinaryStruct>
 */
class BinaryCollection extends StoreCollection
{
    protected function getExpectedClass(): ?string
    {
        return BinaryStruct::class;
    }

    protected function getElementFromArray(array $element): StoreStruct
    {
        return BinaryStruct::fromArray($element);
    }
}
