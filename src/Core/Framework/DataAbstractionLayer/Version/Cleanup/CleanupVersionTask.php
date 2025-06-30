<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Version\Cleanup;

use HeyPanel\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class CleanupVersionTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'version.cleanup';
    }

    public static function getDefaultInterval(): int
    {
        return self::DAILY;
    }

    public static function shouldRescheduleOnFailure(): bool
    {
        return true;
    }
}
