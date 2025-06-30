<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Sitemap\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;

class UrlProviderNotFound extends HeyPanelHttpException
{
    public function __construct(string $provider)
    {
        parent::__construct('provider "{{ provider }}" not found.', ['provider' => $provider]);
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__SITEMAP_PROVIDER_NOT_FOUND';
    }
}
