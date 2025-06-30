<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Store\Struct;

/**
 * @codeCoverageIgnore
 *
 * @template-extends StoreCollection<ImageStruct>
 */
class ImageCollection extends StoreCollection
{
    protected function getExpectedClass(): ?string
    {
        return ImageStruct::class;
    }

    protected function getElementFromArray(array $element): StoreStruct
    {
        return ImageStruct::fromArray($element);
    }
}
