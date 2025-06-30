<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Sitemap\ConfigHandler;

interface ConfigHandlerInterface
{
    /**
     * @return array<string, array<array<string, mixed>>>
     */
    public function getSitemapConfig(): array;
}
