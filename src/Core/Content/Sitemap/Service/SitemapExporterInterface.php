<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Sitemap\Service;

use HeyPanel\Core\Content\Sitemap\Exception\AlreadyLockedException;
use HeyPanel\Core\Content\Sitemap\Struct\SitemapGenerationResult;
use HeyPanel\Core\System\Channel\ChannelContext;

interface SitemapExporterInterface
{
    public const SITEMAP_URL_LIMIT = 49999;

    public const STRATEGY_MANUAL = 1;
    public const STRATEGY_SCHEDULED_TASK = 2;
    public const STRATEGY_LIVE = 3;

    /**
     * @throws AlreadyLockedException
     */
    public function generate(ChannelContext $context, bool $force = false, ?string $lastProvider = null, ?int $offset = null): SitemapGenerationResult;
}
