<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Channel\Entity;

use HeyPanel\Core\Framework\DataAbstractionLayer\EntityDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\Event\EntityAggregationResultLoadedEvent;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\AggregationResult\AggregationResultCollection;
use HeyPanel\Core\Framework\Event\HeyPanelChannelEvent;
use HeyPanel\Core\System\Channel\ChannelContext;

class ChannelEntityAggregationResultLoadedEvent extends EntityAggregationResultLoadedEvent implements HeyPanelChannelEvent
{
    private readonly ChannelContext $channelContext;

    public function __construct(
        EntityDefinition $definition,
        AggregationResultCollection $result,
        ChannelContext $channelContext
    ) {
        parent::__construct($definition, $result, $channelContext->getContext());
        $this->channelContext = $channelContext;
    }

    public function getName(): string
    {
        return 'channel.' . parent::getName();
    }

    public function getChannelContext(): ChannelContext
    {
        return $this->channelContext;
    }
}
