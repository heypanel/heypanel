<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Routing\Event;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\Event\HeyPanelChannelEvent;
use HeyPanel\Core\System\Channel\ChannelContext;
use Symfony\Contracts\EventDispatcher\Event;

class ChannelContextResolvedEvent extends Event implements HeyPanelChannelEvent
{
    public function __construct(
        private readonly ChannelContext $channelContext,
        private readonly string $usedToken
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
}
