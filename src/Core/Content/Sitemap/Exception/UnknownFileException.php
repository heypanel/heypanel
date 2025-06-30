<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Sitemap\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;

class UnknownFileException extends HeyPanelHttpException
{
    public function getErrorCode(): string
    {
        return 'CONTENT__SITEMAP_UNKNOWN_FILE';
    }
}
