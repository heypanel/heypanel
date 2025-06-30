<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Channel;

use HeyPanel\Core\Framework\HeyPanelHttpException;
use HeyPanel\Core\Framework\HttpException;
use Symfony\Component\HttpFoundation\Response;

/**
 * @codeCoverageIgnore
 */
class ChannelException extends HttpException
{
    final public const CHANNEL_DOES_NOT_EXISTS_EXCEPTION = 'SYSTEM__CHANNEL_DOES_NOT_EXISTS';
    final public const CURRENCY_INVALID_EXCEPTION = 'SYSTEM__CURRENCY_INVALID_EXCEPTION';
    final public const CURRENCY_DOES_NOT_EXISTS_EXCEPTION = 'SYSTEM__CURRENCY_DOES_NOT_EXISTS_EXCEPTION';
    final public const MEMBER_GROUP_DOES_NOT_EXISTS_EXCEPTION = 'SYSTEM__MEMBER_GROUP_DOES_NOT_EXISTS_EXCEPTION';
    final public const COUNTRY_INVALID_EXCEPTION = 'SYSTEM__COUNTRY_INVALID_EXCEPTION';
    final public const COUNTRY_DOES_NOT_EXISTS_EXCEPTION = 'SYSTEM__COUNTRY_DOES_NOT_EXISTS_EXCEPTION';
    final public const LANGUAGE_NOT_FOUND = 'SYSTEM__LANGUAGE_NOT_FOUND';
    final public const ENCODING_MISSING_AGGREGATION_EXCEPTION = 'SYSTEM__ENCODING_MISSING_AGGREGATION_EXCEPTION';
    final public const ENCODING_INVALID_STRUCT_EXCEPTION = 'SYSTEM__ENCODING_INVALID_STRUCT_EXCEPTION';
    public const INVALID_TYPE = 'FRAMEWORK__INVALID_TYPE';
    private const INVALID_UUID_MESSAGE_TEMPLATE = 'Provided %s is not a valid UUID';
    final public const CHANNEL_CONTEXT_PERMISSIONS_LOCKED = 'SYSTEM__HANNEL_CONTEXT_PERMISSIONS_LOCKED';

    public static function contextPermissionsLocked(): self
    {
        return new self(
            Response::HTTP_BAD_REQUEST,
            self::CHANNEL_CONTEXT_PERMISSIONS_LOCKED,
            'Context permission in SalesChannel context already locked.'
        );
    }

    public static function invalidType(string $message): self
    {
        return new self(
            Response::HTTP_INTERNAL_SERVER_ERROR,
            self::INVALID_TYPE,
            $message
        );
    }

    public static function encodingInvalidStructException(string $context): self
    {
        return new self(
            Response::HTTP_BAD_REQUEST,
            self::ENCODING_INVALID_STRUCT_EXCEPTION,
            'Invalid struct: "{{ context }}"',
            ['context' => $context]
        );
    }

    public static function encodingMissingAggregationException(int|string $key, int $index): self
    {
        return new self(
            Response::HTTP_BAD_REQUEST,
            self::ENCODING_MISSING_AGGREGATION_EXCEPTION,
            'Can not find encoded aggregation "{{ key }}" for data index "{{ index }}"',
            ['key' => $key, 'index' => $index]
        );
    }

    public static function languageNotFound(string $languageId): HeyPanelHttpException
    {
        return new self(
            Response::HTTP_PRECONDITION_FAILED,
            self::LANGUAGE_NOT_FOUND,
            self::$couldNotFindMessage,
            ['entity' => 'language', 'field' => 'id', 'value' => $languageId]
        );
    }

    public static function countryNotFound(string $countryId): self
    {
        return new self(
            Response::HTTP_NOT_FOUND,
            self::COUNTRY_DOES_NOT_EXISTS_EXCEPTION,
            self::$couldNotFindMessage,
            ['entity' => 'country', 'field' => 'id', 'value' => $countryId]
        );
    }

    public static function invalidCountryId(): self
    {
        return new self(
            Response::HTTP_PRECONDITION_FAILED,
            self::COUNTRY_INVALID_EXCEPTION,
            \sprintf(self::INVALID_UUID_MESSAGE_TEMPLATE, 'country ID'),
        );
    }

    public static function channelNotFound(string $channelId): self
    {
        return new self(
            Response::HTTP_NOT_FOUND,
            self::CHANNEL_DOES_NOT_EXISTS_EXCEPTION,
            'Channel with id "{{ channelId }}" not found or not valid!.',
            ['channelId' => $channelId]
        );
    }

    public static function customerGroupNotFound(string $customerGroupId): self
    {
        return new self(
            Response::HTTP_NOT_FOUND,
            self::MEMBER_GROUP_DOES_NOT_EXISTS_EXCEPTION,
            self::$couldNotFindMessage,
            ['entity' => 'customer group', 'field' => 'id', 'value' => $customerGroupId]
        );
    }

    public static function currencyNotFound(string $currencyId): self
    {
        return new self(
            Response::HTTP_NOT_FOUND,
            self::CURRENCY_DOES_NOT_EXISTS_EXCEPTION,
            self::$couldNotFindMessage,
            ['entity' => 'currency', 'field' => 'id', 'value' => $currencyId]
        );
    }

    public static function invalidCurrencyId(): self
    {
        return new self(
            Response::HTTP_PRECONDITION_FAILED,
            self::CURRENCY_INVALID_EXCEPTION,
            \sprintf(self::INVALID_UUID_MESSAGE_TEMPLATE, 'currency ID'),
        );
    }
}
