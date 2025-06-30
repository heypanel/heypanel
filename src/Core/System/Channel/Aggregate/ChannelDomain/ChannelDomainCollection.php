<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Channel\Aggregate\ChannelDomain;

use HeyPanel\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<ChannelDomainEntity>
 */
class ChannelDomainCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'channel_domain_collection';
    }

    protected function getExpectedClass(): string
    {
        return ChannelDomainEntity::class;
    }
}
