<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Channel\Aggregate\ChannelTypeTranslation;

use HeyPanel\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<ChannelTypeTranslationEntity>
 */
class ChannelTypeTranslationCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'channel_type_translation_collection';
    }

    protected function getExpectedClass(): string
    {
        return ChannelTypeTranslationEntity::class;
    }
}
