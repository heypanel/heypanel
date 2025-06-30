<?php declare(strict_types=1);

namespace HeyPanel\Frontend\Theme\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ThemeAssignedEvent extends Event
{
    public function __construct(
        private readonly string $themeId,
        private readonly string $channelId
    ) {
    }

    public function getThemeId(): string
    {
        return $this->themeId;
    }

    public function getChannelId(): string
    {
        return $this->channelId;
    }
}
