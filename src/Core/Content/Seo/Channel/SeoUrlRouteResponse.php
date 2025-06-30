<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Seo\Channel;

use HeyPanel\Core\Content\Seo\SeoUrl\SeoUrlCollection;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use HeyPanel\Core\System\Channel\ClientApiResponse;

/**
 * @extends ClientApiResponse<EntitySearchResult<SeoUrlCollection>>
 */
class SeoUrlRouteResponse extends ClientApiResponse
{
    public function getSeoUrls(): SeoUrlCollection
    {
        return $this->object->getEntities();
    }
}
