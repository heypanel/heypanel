<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\LandingPage\Channel;

use HeyPanel\Core\Content\LandingPage\LandingPageDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Criteria;
use HeyPanel\Core\System\Channel\ChannelContext;
use HeyPanel\Core\System\Channel\Entity\ChannelDefinitionInterface;

class ChannelLandingPageDefinition extends LandingPageDefinition implements ChannelDefinitionInterface
{
    public function processCriteria(Criteria $criteria, ChannelContext $context): void
    {
    }
}
