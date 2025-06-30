<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Cms\Events;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Criteria;
use HeyPanel\Core\Framework\Event\HeyPanelChannelEvent;
use HeyPanel\Core\Framework\Event\NestedEvent;
use HeyPanel\Core\System\Channel\ChannelContext;
use Symfony\Component\HttpFoundation\Request;

class CmsPageLoaderCriteriaEvent extends NestedEvent implements HeyPanelChannelEvent
{
    protected Request $request;

    protected Criteria $criteria;

    protected ChannelContext $channelContext;

    public function __construct(
        Request $request,
        Criteria $criteria,
        ChannelContext $channelContext
    ) {
        $this->request = $request;
        $this->criteria = $criteria;
        $this->channelContext = $channelContext;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getCriteria(): Criteria
    {
        return $this->criteria;
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
