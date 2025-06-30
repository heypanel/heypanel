<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Sitemap\Service;

use HeyPanel\Core\Content\Sitemap\Struct\Sitemap;
use HeyPanel\Core\System\Channel\ChannelContext;

interface SitemapListerInterface
{
    /**
     * @return Sitemap[]
     */
    public function getSitemaps(ChannelContext $channelContext): array;
}
