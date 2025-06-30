<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Notification;

use HeyPanel\Core\Framework\Api\Context\AdminApiSource;
use HeyPanel\Core\Framework\Api\Context\ContextSource;
use HeyPanel\Core\Framework\Api\Context\Exception\InvalidContextSourceException;
use HeyPanel\Core\Framework\HttpException;

class NotificationException extends HttpException
{
    public const WRONG_GATEWAY_CLASS = 'FRAMEWORK__INCREMENT_WRONG_GATEWAY_CLASS';

    /**
     * @param class-string<ContextSource> $actual
     */
    public static function invalidAdminSource(string $actual): InvalidContextSourceException
    {
        return new InvalidContextSourceException(AdminApiSource::class, $actual);
    }
}
