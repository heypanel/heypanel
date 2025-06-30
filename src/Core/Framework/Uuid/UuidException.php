<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Uuid;

use HeyPanel\Core\Framework\HeyPanelHttpException;
use HeyPanel\Core\Framework\HttpException;
use HeyPanel\Core\Framework\Uuid\Exception\InvalidUuidException;
use HeyPanel\Core\Framework\Uuid\Exception\InvalidUuidLengthException;

class UuidException extends HttpException
{
    public static function invalidUuid(string $uuid): HeyPanelHttpException
    {
        return new InvalidUuidException($uuid);
    }

    public static function invalidUuidLength(int $length, string $hex): HeyPanelHttpException
    {
        return new InvalidUuidLengthException($length, $hex);
    }
}
