<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Language\Channel;

use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Criteria;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use HeyPanel\Core\System\Channel\ChannelContext;
use HeyPanel\Core\System\Channel\Entity\ChannelDefinitionInterface;
use HeyPanel\Core\System\Language\LanguageDefinition;

class ChannelLanguageDefinition extends LanguageDefinition implements ChannelDefinitionInterface
{
    public function processCriteria(Criteria $criteria, ChannelContext $context): void
    {
        $criteria->addFilter(new EqualsFilter('language.channels.id', $context->getChannelId()));
    }
}
