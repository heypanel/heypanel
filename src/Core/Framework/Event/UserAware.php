<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Event;

#[IsFlowEventAware]
interface UserAware
{
    public const USER_RECOVERY = 'userRecovery';

    public const USER_RECOVERY_ID = 'userRecoveryId';

    public function getUserId(): string;
}
