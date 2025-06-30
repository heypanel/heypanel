<?php
declare(strict_types=1);

namespace HeyPanel\Core\System\Customer\Exception;

use HeyPanel\Core\System\Customer\CustomerException;
use Symfony\Component\HttpFoundation\Response;

class PasswordPoliciesUpdatedException extends CustomerException
{
    public function __construct()
    {
        parent::__construct(
            Response::HTTP_BAD_REQUEST,
            'CHECKOUT__PASSWORD_POLICIES_UPDATED',
            'Password policies updated.'
        );
    }
}
