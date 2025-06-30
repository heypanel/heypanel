<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Sitemap\Service;

use HeyPanel\Core\System\Channel\ChannelContext;
use League\Flysystem\FilesystemOperator;

interface SitemapHandleFactoryInterface
{
    public function create(
        FilesystemOperator $filesystem,
        ChannelContext $context,
        ?string $domain = null,
        ?string $domainId = null,
    ): SitemapHandleInterface;
}
