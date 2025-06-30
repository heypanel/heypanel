<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Channel;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\DataAbstractionLayer\Pricing\CashRoundingConfig;
use HeyPanel\Core\System\Channel\Context\LanguageInfo;
use HeyPanel\Core\System\Currency\CurrencyEntity;
use HeyPanel\Core\System\Customer\Aggregate\CustomerGroup\CustomerGroupEntity;

/**
 * Contains basic customer-independent information of the current channel.
 *
 * @internal Use ChannelContext for extensions
 *
 * @codeCoverageIgnore
 */
class BaseChannelContext
{
    public function __construct(
        protected Context $context,
        protected ChannelEntity $channel,
        protected CurrencyEntity $currency,
        protected CustomerGroupEntity $currentCustomerGroup,
        private readonly CashRoundingConfig $itemRounding,
        private readonly CashRoundingConfig $totalRounding,
        private readonly LanguageInfo $languageInfo
    ) {
    }

    public function getCurrencyId(): string
    {
        return $this->currency->getId();
    }

    public function getCurrency(): CurrencyEntity
    {
        return $this->currency;
    }

    public function getTotalRounding(): CashRoundingConfig
    {
        return $this->totalRounding;
    }

    public function getItemRounding(): CashRoundingConfig
    {
        return $this->itemRounding;
    }

    public function getCurrentCustomerGroup(): CustomerGroupEntity
    {
        return $this->currentCustomerGroup;
    }

    public function getChannelId(): string
    {
        return $this->channel->getId();
    }

    public function getChannel(): ChannelEntity
    {
        return $this->channel;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function getLanguageInfo(): LanguageInfo
    {
        return $this->languageInfo;
    }

    public function getApiAlias(): string
    {
        return 'base_channel_context';
    }
}
