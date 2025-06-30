<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Category\Event;

use HeyPanel\Core\Content\Category\Tree\Tree;
use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\Event\HeyPanelChannelEvent;
use HeyPanel\Core\Framework\Event\NestedEvent;
use HeyPanel\Core\System\Channel\ChannelContext;

class NavigationLoadedEvent extends NestedEvent implements HeyPanelChannelEvent
{
    protected Tree $navigation;

    protected ChannelContext $channelContext;

    public function __construct(
        Tree $navigation,
        ChannelContext $channelContext
    ) {
        $this->navigation = $navigation;
        $this->channelContext = $channelContext;
    }

    public function getContext(): Context
    {
        return $this->channelContext->getContext();
    }

    public function getNavigation(): Tree
    {
        return $this->navigation;
    }

    public function getChannelContext(): ChannelContext
    {
        return $this->channelContext;
    }
}
