<?php declare(strict_types=1);

namespace HeyPanel\Core\System\SystemConfig\Event;

use Symfony\Contracts\EventDispatcher\Event;

class SystemConfigDomainLoadedEvent extends Event
{
    public function __construct(
        private readonly string $domain,
        private array $config,
        private readonly bool $inherit,
        private readonly ?string $channelId
    ) {
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function isInherit(): bool
    {
        return $this->inherit;
    }

    public function getChannelId(): ?string
    {
        return $this->channelId;
    }
}
