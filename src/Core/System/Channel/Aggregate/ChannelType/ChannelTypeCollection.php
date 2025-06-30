<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Channel\Aggregate\ChannelType;

use HeyPanel\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<ChannelTypeEntity>
 */
class ChannelTypeCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'channel_type_collection';
    }

    protected function getExpectedClass(): string
    {
        return ChannelTypeEntity::class;
    }
}
