<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Channel\Event;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Criteria;
use HeyPanel\Core\Framework\Event\HeyPanelChannelEvent;
use HeyPanel\Core\System\Channel\ChannelContext;

class ChannelProcessCriteriaEvent implements HeyPanelChannelEvent
{
    public function __construct(
        private readonly Criteria $criteria,
        private readonly ChannelContext $channelContext
    ) {
    }

    public function getCriteria(): Criteria
    {
        return $this->criteria;
    }

    public function getChannelContext(): ChannelContext
    {
        return $this->channelContext;
    }

    public function getContext(): Context
    {
        return $this->channelContext->getContext();
    }
}
