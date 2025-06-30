<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Customer\Exception;

use HeyPanel\Core\System\Customer\CustomerException;
use Symfony\Component\HttpFoundation\Response;

class CustomerAuthThrottledException extends CustomerException
{
    public function __construct(
        private readonly int $waitTime,
        ?\Throwable $e = null
    ) {
        parent::__construct(
            Response::HTTP_TOO_MANY_REQUESTS,
            self::MEMBER_AUTH_THROTTLED,
            'Customer auth throttled for {{ seconds }} seconds.',
            ['seconds' => $this->waitTime],
            $e
        );
    }

    public function getWaitTime(): int
    {
        return $this->waitTime;
    }
}
