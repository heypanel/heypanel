<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Seo\MainCategory\Channel;

use HeyPanel\Core\Content\Seo\MainCategory\MainCategoryDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Criteria;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use HeyPanel\Core\System\Channel\ChannelContext;
use HeyPanel\Core\System\Channel\Entity\ChannelDefinitionInterface;

class ChannelMainCategoryDefinition extends MainCategoryDefinition implements ChannelDefinitionInterface
{
    public function processCriteria(Criteria $criteria, ChannelContext $context): void
    {
        $criteria->addFilter(new EqualsFilter('channelId', $context->getChannelId()));
    }
}
