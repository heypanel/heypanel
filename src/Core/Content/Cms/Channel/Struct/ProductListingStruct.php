<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Cms\Channel\Struct;

use HeyPanel\Core\Content\Product\ProductCollection;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use HeyPanel\Core\Framework\Struct\Struct;

class ProductListingStruct extends Struct
{
    /**
     * @var EntitySearchResult<ProductCollection>|null
     */
    protected ?EntitySearchResult $listing = null;

    /**
     * @return EntitySearchResult<ProductCollection>|null
     */
    public function getListing(): ?EntitySearchResult
    {
        return $this->listing;
    }

    /**
     * @param EntitySearchResult<ProductCollection> $listing
     */
    public function setListing(EntitySearchResult $listing): void
    {
        $this->listing = $listing;
    }

    public function getApiAlias(): string
    {
        return 'cms_product_listing';
    }
}
