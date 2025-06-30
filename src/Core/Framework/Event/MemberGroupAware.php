<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Event;

interface CustomerGroupAware
{
    public const MEMBER_GROUP_ID = 'customerGroupId';

    public const MEMBER_GROUP = 'customerGroup';

    public function getCustomerGroupId(): string;
}
