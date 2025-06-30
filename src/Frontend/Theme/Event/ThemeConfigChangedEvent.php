<?php declare(strict_types=1);

namespace HeyPanel\Frontend\Theme\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ThemeConfigChangedEvent extends Event
{
    public function __construct(
        private readonly string $themeId,
        protected array $config
    ) {
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function getThemeId(): string
    {
        return $this->themeId;
    }
}
