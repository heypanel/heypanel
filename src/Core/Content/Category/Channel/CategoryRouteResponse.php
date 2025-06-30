<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Category\Channel;

use HeyPanel\Core\Content\Category\CategoryEntity;
use HeyPanel\Core\System\Channel\ClientApiResponse;

/**
 * @extends ClientApiResponse<CategoryEntity>
 */
class CategoryRouteResponse extends ClientApiResponse
{
    public function getCategory(): CategoryEntity
    {
        return $this->object;
    }
}
