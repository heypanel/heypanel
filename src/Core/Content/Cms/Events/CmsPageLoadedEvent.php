<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Cms\Events;

use HeyPanel\Core\Content\Cms\CmsPageCollection;
use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityCollection;
use HeyPanel\Core\Framework\Event\HeyPanelChannelEvent;
use HeyPanel\Core\Framework\Event\NestedEvent;
use HeyPanel\Core\System\Channel\ChannelContext;
use Symfony\Component\HttpFoundation\Request;

class CmsPageLoadedEvent extends NestedEvent implements HeyPanelChannelEvent
{
    protected Request $request;

    protected CmsPageCollection $result;

    protected ChannelContext $channelContext;

    /**
     * @param CmsPageCollection $result
     */
    public function __construct(
        Request $request,
        EntityCollection $result,
        ChannelContext $channelContext
    ) {
        $this->request = $request;
        $this->result = $result;
        $this->channelContext = $channelContext;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return CmsPageCollection
     */
    public function getResult(): EntityCollection
    {
        return $this->result;
    }

    public function getContext(): Context
    {
        return $this->channelContext->getContext();
    }

    public function getChannelContext(): ChannelContext
    {
        return $this->channelContext;
    }
}
