<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Channel\Event;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\Event\HeyPanelChannelEvent;
use HeyPanel\Core\System\Channel\ChannelContext;
use Symfony\Contracts\EventDispatcher\Event;

class ChannelContextTokenChangeEvent extends Event implements HeyPanelChannelEvent
{
    public function __construct(
        protected ChannelContext $channelContext,
        protected string $previousToken,
        protected string $currentToken
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

    public function getPreviousToken(): string
    {
        return $this->previousToken;
    }

    public function getCurrentToken(): string
    {
        return $this->currentToken;
    }
}
