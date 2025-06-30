<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Customer\Exception;

use HeyPanel\Core\System\Customer\CustomerException;
use Symfony\Component\HttpFoundation\Response;

class CustomerNotFoundByIdException extends CustomerException
{
    public function __construct(string $id)
    {
        parent::__construct(
            Response::HTTP_UNAUTHORIZED,
            self::MEMBER_NOT_FOUND_BY_ID,
            'No matching customer for the id "{{ id }}" was found.',
            ['id' => $id]
        );
    }
}
