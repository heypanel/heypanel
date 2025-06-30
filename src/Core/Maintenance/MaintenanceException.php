<?php declare(strict_types=1);

namespace HeyPanel\Core\Maintenance;

use HeyPanel\Core\Framework\HttpException;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 *
 * @codeCoverageIgnore
 */
class MaintenanceException extends HttpException
{
    final public const MAINTENANCE_SYMFONY_CONSOLE_APPLICATION_NOT_FOUND = 'MAINTENANCE__SYMFONY_CONSOLE_APPLICATION_NOT_FOUND';
    final public const MAINTENANCE_USER_PASSWORD_TOO_SHORT = 'MAINTENANCE__USER_PASSWORD_TOO_SHORT';
    final public const MAINTENANCE_USER_ALREADY_EXISTS = 'MAINTENANCE__USER_ALREADY_EXISTS';
    final public const MAINTENANCE_WEBSITE_CONFIGURATION_NOT_VALID = 'MAINTENANCE__WEBSITE_CONFIGURATION_NOT_VALID';

    public static function websiteConfigurationNotValid(string $message): self
    {
        return new self(
            Response::HTTP_INTERNAL_SERVER_ERROR,
            self::MAINTENANCE_WEBSITE_CONFIGURATION_NOT_VALID,
            $message
        );
    }

    public static function userAlreadyExists(string $username): self
    {
        return new self(
            Response::HTTP_BAD_REQUEST,
            self::MAINTENANCE_USER_ALREADY_EXISTS,
            'User with username "{{ username }}" already exists.',
            ['username' => $username]
        );
    }

    public static function passwordTooShort(int $minPasswordLength): self
    {
        return new self(
            Response::HTTP_BAD_REQUEST,
            self::MAINTENANCE_USER_PASSWORD_TOO_SHORT,
            'The password must have at least {{ minPasswordLength }} characters.',
            ['minPasswordLength' => $minPasswordLength]
        );
    }

    public static function consoleApplicationNotFound(): self
    {
        return new self(
            Response::HTTP_INTERNAL_SERVER_ERROR,
            self::MAINTENANCE_SYMFONY_CONSOLE_APPLICATION_NOT_FOUND,
            'Symfony console application not found'
        );
    }
}
