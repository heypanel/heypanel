<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Channel;

use HeyPanel\Core\Framework\DataAbstractionLayer\EntityCollection;
use HeyPanel\Core\System\Channel\Aggregate\ChannelType\ChannelTypeCollection;
use HeyPanel\Core\System\Currency\CurrencyCollection;
use HeyPanel\Core\System\Language\LanguageCollection;

/**
 * @extends EntityCollection<ChannelEntity>
 */
class ChannelCollection extends EntityCollection
{
    /**
     * @return array<string>
     */
    public function getLanguageIds(): array
    {
        return $this->fmap(fn (ChannelEntity $channel) => $channel->getLanguageId());
    }

    public function filterByLanguageId(string $id): ChannelCollection
    {
        return $this->filter(fn (ChannelEntity $channel) => $channel->getLanguageId() === $id);
    }

    /**
     * @return array<string>
     */
    public function getCurrencyIds(): array
    {
        return $this->fmap(fn (ChannelEntity $channel) => $channel->getCurrencyId());
    }

    public function filterByCurrencyId(string $id): ChannelCollection
    {
        return $this->filter(fn (ChannelEntity $channel) => $channel->getCurrencyId() === $id);
    }

    /**
     * @return array<string>
     */
    public function getCountryIds(): array
    {
        return $this->fmap(fn (ChannelEntity $channel) => $channel->getCountryId());
    }

    public function filterByCountryId(string $id): ChannelCollection
    {
        return $this->filter(fn (ChannelEntity $channel) => $channel->getCountryId() === $id);
    }

    /**
     * @return array<string>
     */
    public function getTypeIds(): array
    {
        return $this->fmap(fn (ChannelEntity $channel) => $channel->getTypeId());
    }

    public function filterByTypeId(string $id): ChannelCollection
    {
        return $this->filter(fn (ChannelEntity $channel) => $channel->getTypeId() === $id);
    }

    public function getLanguages(): LanguageCollection
    {
        return new LanguageCollection(
            $this->fmap(fn (ChannelEntity $channel) => $channel->getLanguage())
        );
    }

    public function getCurrencies(): CurrencyCollection
    {
        return new CurrencyCollection(
            $this->fmap(fn (ChannelEntity $channel) => $channel->getCurrency())
        );
    }

    public function getTypes(): ChannelTypeCollection
    {
        return new ChannelTypeCollection(
            $this->fmap(fn (ChannelEntity $channel) => $channel->getType())
        );
    }

    public function getApiAlias(): string
    {
        return 'channel_collection';
    }

    protected function getExpectedClass(): string
    {
        return ChannelEntity::class;
    }
}
