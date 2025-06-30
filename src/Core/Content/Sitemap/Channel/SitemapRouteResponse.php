<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Sitemap\Channel;

use HeyPanel\Core\Content\Sitemap\Struct\SitemapCollection;
use HeyPanel\Core\System\Channel\ClientApiResponse;

/**
 * @extends ClientApiResponse<SitemapCollection>
 */
class SitemapRouteResponse extends ClientApiResponse
{
    public function getSitemaps(): SitemapCollection
    {
        return $this->object;
    }
}
