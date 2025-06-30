<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;
use Symfony\Component\HttpFoundation\Response;

class InvalidLimitQueryException extends HeyPanelHttpException
{
    public function __construct(mixed $limit)
    {
        parent::__construct(
            'The limit parameter must be a positive integer greater or equals than 1. Given: {{ limit }}',
            ['limit' => $limit]
        );
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__INVALID_LIMIT_QUERY';
    }
}
