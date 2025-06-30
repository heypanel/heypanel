<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Api\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;
use Symfony\Component\HttpFoundation\Response;

class InvalidChannelIdException extends HeyPanelHttpException
{
    public function __construct(string $channelId)
    {
        parent::__construct(
            'The provided channelId "{{ channelId }}" is invalid.',
            ['channelId' => $channelId]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__INVALID_SALES_CHANNEL';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
