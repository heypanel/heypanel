<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Cms\Channel\Struct;

use HeyPanel\Core\Content\Product\Channel\ChannelProductEntity;
use HeyPanel\Core\Content\Property\PropertyGroupCollection;
use HeyPanel\Core\Framework\Struct\Struct;

class BuyBoxStruct extends Struct
{
    protected ?string $productId = null;

    protected int $totalReviews;

    protected ?ChannelProductEntity $product = null;

    protected ?PropertyGroupCollection $configuratorSettings = null;

    public function getProduct(): ?ChannelProductEntity
    {
        return $this->product;
    }

    public function getConfiguratorSettings(): ?PropertyGroupCollection
    {
        return $this->configuratorSettings;
    }

    public function setConfiguratorSettings(?PropertyGroupCollection $configuratorSettings): void
    {
        $this->configuratorSettings = $configuratorSettings;
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

    public function getTotalReviews(): ?int
    {
        return $this->totalReviews;
    }

    public function setTotalReviews(int $totalReviews): void
    {
        $this->totalReviews = $totalReviews;
    }

    public function getApiAlias(): string
    {
        return 'cms_buy_box';
    }
}
