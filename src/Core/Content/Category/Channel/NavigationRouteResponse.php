<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Category\Channel;

use HeyPanel\Core\Content\Category\CategoryCollection;
use HeyPanel\Core\System\Channel\ClientApiResponse;

/**
 * @extends ClientApiResponse<CategoryCollection>
 */
class NavigationRouteResponse extends ClientApiResponse
{
    public function getCategories(): CategoryCollection
    {
        return $this->object;
    }
}
