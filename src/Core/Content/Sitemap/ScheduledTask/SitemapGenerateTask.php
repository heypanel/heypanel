<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Sitemap\ScheduledTask;

use HeyPanel\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SitemapGenerateTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'heypanel.sitemap_generate';
    }

    public static function getDefaultInterval(): int
    {
        return self::DAILY;
    }

    public static function shouldRun(ParameterBagInterface $bag): bool
    {
        return (bool) $bag->get('heypanel.sitemap.scheduled_task.enabled');
    }

    public static function shouldRescheduleOnFailure(): bool
    {
        return true;
    }
}
