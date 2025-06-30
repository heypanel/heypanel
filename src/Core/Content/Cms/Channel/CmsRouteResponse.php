<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Cms\Channel;

use HeyPanel\Core\Content\Cms\CmsPageEntity;
use HeyPanel\Core\System\Channel\ClientApiResponse;

/**
 * @extends ClientApiResponse<CmsPageEntity>
 */
class CmsRouteResponse extends ClientApiResponse
{
    public function getCmsPage(): CmsPageEntity
    {
        return $this->object;
    }
}
