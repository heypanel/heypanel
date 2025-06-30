<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\LandingPage;

use HeyPanel\Core\Framework\HttpException;

class LandingPageException extends HttpException
{
    public const EXCEPTION_CODE_LANDING_PAGE_NOT_FOUND = 'CONTENT__LANDING_PAGE_NOT_FOUND';

    public static function notFound(string $id): self
    {
        return new self(
            404,
            self::EXCEPTION_CODE_LANDING_PAGE_NOT_FOUND,
            'Landing page "{{ landingPageId }}" not found.',
            ['landingPageId' => $id]
        );
    }
}
