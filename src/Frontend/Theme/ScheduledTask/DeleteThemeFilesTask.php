<?php

declare(strict_types=1);

namespace HeyPanel\Frontend\Theme\ScheduledTask;

use HeyPanel\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

/**
 * @internal
 *
 * @codeCoverageIgnore
 */
final class DeleteThemeFilesTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'theme.delete_files';
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
