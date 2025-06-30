<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Cms\Channel\Struct;

use HeyPanel\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerEntity;

class ManufacturerLogoStruct extends ImageStruct
{
    protected ?ProductManufacturerEntity $manufacturer = null;

    public function getManufacturer(): ?ProductManufacturerEntity
    {
        return $this->manufacturer;
    }

    public function setManufacturer(?ProductManufacturerEntity $manufacturer): void
    {
        $this->manufacturer = $manufacturer;
    }

    public function getApiAlias(): string
    {
        return 'cms_manufacturer_logo';
    }
}
