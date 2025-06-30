<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Customer\Exception;

use HeyPanel\Core\System\Customer\CustomerException;
use Symfony\Component\HttpFoundation\Response;

class BadCredentialsException extends CustomerException
{
    public function __construct()
    {
        parent::__construct(
            Response::HTTP_UNAUTHORIZED,
            self::MEMBER_AUTH_BAD_CREDENTIALS,
            'Invalid username and/or password.'
        );
    }
}
