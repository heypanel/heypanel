<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Uuid\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;
use Symfony\Component\HttpFoundation\Response;

class InvalidUuidException extends HeyPanelHttpException
{
    public function __construct(string $uuid)
    {
        parent::__construct('Value is not a valid UUID: {{ input }}', ['input' => $uuid]);
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__INVALID_UUID';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
