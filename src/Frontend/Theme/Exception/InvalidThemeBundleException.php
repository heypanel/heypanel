<?php declare(strict_types=1);

namespace HeyPanel\Frontend\Theme\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;
use Symfony\Component\HttpFoundation\Response;

class InvalidThemeBundleException extends HeyPanelHttpException
{
    public function __construct(string $themeName)
    {
        parent::__construct('Unable to find the theme.json for "{{ themeName }}"', ['themeName' => $themeName]);
    }

    public function getErrorCode(): string
    {
        return 'THEME__INVALID_THEME_BUNDLE';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
