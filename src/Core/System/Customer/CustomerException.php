<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Customer;

use HeyPanel\Core\Framework\HeyPanelHttpException;
use HeyPanel\Core\Framework\HttpException;
use HeyPanel\Core\System\Customer\Exception\CustomerAuthThrottledException;
use HeyPanel\Core\System\Customer\Exception\CustomerNotFoundByIdException;
use HeyPanel\Core\System\Customer\Exception\CustomerNotFoundException;
use HeyPanel\Core\System\Customer\Exception\PasswordPoliciesUpdatedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Validator\Constraint;

class CustomerException extends HttpException
{
    public const MEMBER_AUTH_THROTTLED = 'SYSTEM__MEMBER_AUTH_THROTTLED';

    public const MEMBER_NOT_FOUND = 'SYSTEM__MEMBER_NOT_FOUND';

    public const MEMBER_AUTH_BAD_CREDENTIALS = 'SYSTEM__MEMBER_AUTH_BAD_CREDENTIALS';
    public const MEMBER_NOT_FOUND_BY_ID = 'SYSTEM__MEMBER_NOT_FOUND_BY_ID';

    public const MISSING_ROUTE_ANNOTATION = 'SYSTEM__MISSING_ROUTE_ANNOTATION';
    public const MISSING_ROUTE_CHANNEL = 'SYSTEM__MISSING_ROUTE_CHANNEL';
    public const MISSING_OPTION = 'CONTENT__MISSING_OPTION';
    public const UNEXPECTED_TYPE = 'SYSTEM__UNEXPECTED_TYPE';
    public const LEGACY_PASSWORD_ENCODER_NOT_FOUND = 'SYSTEM__LEGACY_PASSWORD_ENCODER_NOT_FOUND';
    public const NO_HASH_PROVIDED = 'SYSTEM__NO_HASH_PROVIDED';

    public static function customerAuthThrottledException(int $waitTime, ?\Throwable $e = null): CustomerAuthThrottledException
    {
        return new CustomerAuthThrottledException(
            $waitTime,
            $e
        );
    }

    public static function customerNotFound(string $email): CustomerNotFoundException
    {
        return new CustomerNotFoundException($email);
    }

    public static function customerNotFoundByIdException(string $id): CustomerNotFoundByIdException
    {
        return new CustomerNotFoundByIdException($id);
    }

    public static function passwordPoliciesUpdated(): PasswordPoliciesUpdatedException
    {
        return new PasswordPoliciesUpdatedException();
    }

    public static function unexpectedType(Constraint $constraint, string $class): self
    {
        return new self(
            Response::HTTP_INTERNAL_SERVER_ERROR,
            self::UNEXPECTED_TYPE,
            'Expected argument of type "{{ expectedType }}", "{{ givenType }}" given',
            ['expectedType' => $class, 'givenType' => get_debug_type($constraint)]
        );
    }

    public static function badCredentials(): BadCredentialsException
    {
        return new BadCredentialsException();
    }

    public static function noHashProvided(): HeyPanelHttpException
    {
        return new self(
            Response::HTTP_NOT_FOUND,
            self::NO_HASH_PROVIDED,
            'The given hash is empty.'
        );
    }

    public static function legacyPasswordEncoderNotFound(string $encoder): HeyPanelHttpException
    {
        return new self(
            Response::HTTP_BAD_REQUEST,
            self::LEGACY_PASSWORD_ENCODER_NOT_FOUND,
            self::$couldNotFindMessage,
            ['entity' => 'encoder', 'field' => 'name', 'value' => $encoder]
        );
    }

    public static function missingOption(string $option, string $constraint): self
    {
        return new self(
            Response::HTTP_BAD_REQUEST,
            self::MISSING_OPTION,
            'Option "{{ option }}" must be given for constraint {{ constraint }}',
            ['option' => $option, 'constraint' => $constraint]
        );
    }

    public static function missingRouteChannel(string $route): self
    {
        return new self(
            Response::HTTP_INTERNAL_SERVER_ERROR,
            self::MISSING_ROUTE_CHANNEL,
            'Missing channel context for route {{ route }}',
            ['route' => $route]
        );
    }

    public static function missingRouteAnnotation(string $annotation, string $route): self
    {
        return new self(
            Response::HTTP_INTERNAL_SERVER_ERROR,
            self::MISSING_ROUTE_ANNOTATION,
            'Missing @{{ annotation }} annotation for route: {{ route }}',
            ['annotation' => $annotation, 'route' => $route]
        );
    }
}
