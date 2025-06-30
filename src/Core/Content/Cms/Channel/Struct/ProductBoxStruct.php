<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Cms\Channel\Struct;

use HeyPanel\Core\Content\Product\Channel\ChannelProductEntity;
use HeyPanel\Core\Framework\Struct\Struct;

class ProductBoxStruct extends Struct
{
    protected ?string $productId = null;

    protected ?ChannelProductEntity $product = null;

    public function getProduct(): ?ChannelProductEntity
    {
        return $this->product;
    }

    public function setProduct(ChannelProductEntity $product): void
    {
        $this->product = $product;
    }

    public function getProductId(): ?string
    {
        return $this->productId;
    }

    public function setProductId(string $productId): void
    {
        $this->productId = $productId;
    }

    public function getApiAlias(): string
    {
        return 'cms_product_box';
    }
}
