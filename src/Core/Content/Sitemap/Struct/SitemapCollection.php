<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Sitemap\Struct;

use HeyPanel\Core\Framework\Struct\Collection;

/**
 * @extends Collection<Sitemap>
 */
class SitemapCollection extends Collection
{
    protected function getExpectedClass(): ?string
    {
        return Sitemap::class;
    }
}
