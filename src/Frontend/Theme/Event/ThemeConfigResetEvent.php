<?php declare(strict_types=1);

namespace HeyPanel\Frontend\Theme\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ThemeConfigResetEvent extends Event
{
    public function __construct(private readonly string $themeId)
    {
    }

    public function getThemeId(): string
    {
        return $this->themeId;
    }
}
