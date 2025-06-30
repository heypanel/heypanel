<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Channel\Event;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\Event\HeyPanelChannelEvent;
use HeyPanel\Core\System\Channel\ChannelContext;
use Symfony\Contracts\EventDispatcher\Event;

class ChannelContextCreatedEvent extends Event implements HeyPanelChannelEvent
{
    /**
     * @param array<string, mixed> $session
     */
    public function __construct(
        private readonly ChannelContext $channelContext,
        private readonly string $usedToken,
        private readonly array $session = []
    ) {
    }

    public function getChannelContext(): ChannelContext
    {
        return $this->channelContext;
    }

    public function getContext(): Context
    {
        return $this->channelContext->getContext();
    }

    public function getUsedToken(): string
    {
        return $this->usedToken;
    }

    /**
     * @return array<string, mixed>
     */
    public function getSession(): array
    {
        return $this->session;
    }
}
