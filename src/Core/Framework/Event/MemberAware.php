<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Event;

interface MemberAware
{
    public const MEMBER_ID = 'customerId';

    public const MEMBER = 'customer';

    public function getCustomerId(): string;
}
