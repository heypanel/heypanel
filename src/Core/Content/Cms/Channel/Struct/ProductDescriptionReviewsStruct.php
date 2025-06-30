<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Cms\Channel\Struct;

use HeyPanel\Core\Content\Product\Channel\ChannelProductEntity;
use HeyPanel\Core\Content\Product\Channel\Review\ProductReviewResult;
use HeyPanel\Core\Framework\Struct\Struct;

class ProductDescriptionReviewsStruct extends Struct
{
    protected ?string $productId = null;

    protected ?bool $ratingSuccess = null;

    protected ?ProductReviewResult $reviews = null;

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

    public function getReviews(): ?ProductReviewResult
    {
        return $this->reviews;
    }

    public function setReviews(ProductReviewResult $result): void
    {
        $this->reviews = $result;
    }

    public function getRatingSuccess(): ?bool
    {
        return $this->ratingSuccess;
    }

    public function setRatingSuccess(bool $rateSuccess): void
    {
        $this->ratingSuccess = $rateSuccess;
    }

    public function getApiAlias(): string
    {
        return 'cms_product_description_reviews';
    }
}
