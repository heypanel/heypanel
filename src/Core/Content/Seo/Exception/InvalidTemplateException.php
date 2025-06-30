<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Seo\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;
use Symfony\Component\HttpFoundation\Response;

class InvalidTemplateException extends HeyPanelHttpException
{
    final public const ERROR_CODE = 'FRAMEWORK__INVALID_SEO_TEMPLATE';

    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function getErrorCode(): string
    {
        return self::ERROR_CODE;
    }
}
