<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\ImportExport\ScheduledTask;

use HeyPanel\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class CleanupImportExportFileTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'import_export_file.cleanup';
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
