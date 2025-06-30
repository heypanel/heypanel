<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Store\Struct;

/**
 * @codeCoverageIgnore
 *
 * @template-extends StoreCollection<FaqStruct>
 */
class FaqCollection extends StoreCollection
{
    protected function getExpectedClass(): ?string
    {
        return FaqStruct::class;
    }

    protected function getElementFromArray(array $element): StoreStruct
    {
        return FaqStruct::fromArray($element);
    }
}
