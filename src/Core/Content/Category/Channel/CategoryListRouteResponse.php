<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Category\Channel;

use HeyPanel\Core\Content\Category\CategoryCollection;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use HeyPanel\Core\System\Channel\ClientApiResponse;

/**
 * @extends ClientApiResponse<EntitySearchResult<CategoryCollection>>
 */
class CategoryListRouteResponse extends ClientApiResponse
{
    public function getCategories(): CategoryCollection
    {
        return $this->object->getEntities();
    }
}
