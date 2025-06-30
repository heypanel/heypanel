<?php declare(strict_types=1);

namespace HeyPanel\Core\System\User;

use HeyPanel\Core\Framework\HttpException;
use Symfony\Component\HttpFoundation\Response;

class UserException extends HttpException
{
    final public const CHANNEL_NOT_FOUND = 'USER__CHANNEL_NOT_FOUND';

    public static function channelNotFound(): HttpException
    {
        return new self(
            Response::HTTP_PRECONDITION_FAILED,
            self::CHANNEL_NOT_FOUND,
            'No channel found.',
        );
    }
}
