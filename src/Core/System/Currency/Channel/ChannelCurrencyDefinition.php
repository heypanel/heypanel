<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Currency\Channel;

use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Criteria;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use HeyPanel\Core\System\Channel\ChannelContext;
use HeyPanel\Core\System\Channel\Entity\ChannelDefinitionInterface;
use HeyPanel\Core\System\Currency\CurrencyDefinition;

class ChannelCurrencyDefinition extends CurrencyDefinition implements ChannelDefinitionInterface
{
    public function processCriteria(Criteria $criteria, ChannelContext $context): void
    {
        $criteria->addFilter(new EqualsFilter('currency.channels.id', $context->getChannelId()));
    }
}
