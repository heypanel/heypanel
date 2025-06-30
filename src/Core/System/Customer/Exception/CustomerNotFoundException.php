<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Customer\Exception;

use HeyPanel\Core\System\Customer\CustomerException;
use Symfony\Component\HttpFoundation\Response;

class CustomerNotFoundException extends CustomerException
{
    public function __construct(string $email)
    {
        parent::__construct(
            Response::HTTP_UNAUTHORIZED,
            self::MEMBER_NOT_FOUND,
            'No matching customer for the email "{{ email }}" was found.',
            ['email' => $email]
        );
    }
}
