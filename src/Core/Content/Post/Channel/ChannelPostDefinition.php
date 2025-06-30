<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Post\Channel;

use HeyPanel\Core\Content\Post\PostDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Criteria;
use HeyPanel\Core\System\Channel\ChannelContext;
use HeyPanel\Core\System\Channel\Entity\ChannelDefinitionInterface;

class ChannelPostDefinition extends PostDefinition implements ChannelDefinitionInterface
{
    public function processCriteria(Criteria $criteria, ChannelContext $context): void
    {
        // TODO: Implement processCriteria() method.
    }
}
