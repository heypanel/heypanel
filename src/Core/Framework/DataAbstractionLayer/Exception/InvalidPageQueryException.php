<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;
use Symfony\Component\HttpFoundation\Response;

class InvalidPageQueryException extends HeyPanelHttpException
{
    public function __construct(mixed $page)
    {
        parent::__construct('The page parameter must be a positive integer. Given: {{ page }}', ['page' => $page]);
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__INVALID_PAGE_QUERY';
    }
}
