<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Sitemap\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;

class InvalidSitemapKey extends HeyPanelHttpException
{
    public function __construct(string $sitemapKey)
    {
        parent::__construct('Invalid sitemap config key: "{{ sitemapKey }}"', ['sitemapKey' => $sitemapKey]);
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__SITEMAP_INVALID_KEY';
    }
}
