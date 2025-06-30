<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Sitemap\Event;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\Event\HeyPanelEvent;
use HeyPanel\Core\System\Channel\ChannelContext;
use Symfony\Contracts\EventDispatcher\Event;

class SitemapGeneratedEvent extends Event implements HeyPanelEvent
{
    public function __construct(private readonly ChannelContext $context)
    {
    }

    public function getChannelContext(): ChannelContext
    {
        return $this->context;
    }

    public function getContext(): Context
    {
        return $this->context->getContext();
    }
}
