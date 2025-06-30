<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Channel\Aggregate\ChannelTranslation;

use HeyPanel\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<ChannelTranslationEntity>
 */
class ChannelTranslationCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'channel_translation_collection';
    }

    protected function getExpectedClass(): string
    {
        return ChannelTranslationEntity::class;
    }
}
