<?php declare(strict_types=1);

namespace HeyPanel\Core\System\NumberRange\Aggregate\NumberRangeChannel;

use HeyPanel\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<NumberRangeChannelEntity>
 */
class NumberRangeChannelCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'number_range_channel_collection';
    }

    protected function getExpectedClass(): string
    {
        return NumberRangeChannelEntity::class;
    }
}
