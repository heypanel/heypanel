<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Store\Struct;

/**
 * @codeCoverageIgnore
 *
 * @template-extends StoreCollection<ReviewStruct>
 */
class ReviewCollection extends StoreCollection
{
    protected function getExpectedClass(): ?string
    {
        return ReviewStruct::class;
    }

    protected function getElementFromArray(array $element): StoreStruct
    {
        return ReviewStruct::fromArray($element);
    }
}
