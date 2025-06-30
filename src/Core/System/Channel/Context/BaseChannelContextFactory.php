<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Channel\Context;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityRepository;
use HeyPanel\Core\Framework\DataAbstractionLayer\Pricing\CashRoundingConfig;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Criteria;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use HeyPanel\Core\Framework\Uuid\Uuid;
use HeyPanel\Core\System\Channel\BaseChannelContext;
use HeyPanel\Core\System\Channel\ChannelCollection;
use HeyPanel\Core\System\Channel\ChannelEntity;
use HeyPanel\Core\System\Channel\ChannelException;
use HeyPanel\Core\System\Country\CountryCollection;
use HeyPanel\Core\System\Country\CountryEntity;
use HeyPanel\Core\System\Currency\Aggregate\CurrencyCountryRounding\CurrencyCountryRoundingCollection;
use HeyPanel\Core\System\Currency\Aggregate\CurrencyCountryRounding\CurrencyCountryRoundingEntity;
use HeyPanel\Core\System\Currency\CurrencyCollection;
use HeyPanel\Core\System\Currency\CurrencyEntity;
use HeyPanel\Core\System\Language\LanguageCollection;
use HeyPanel\Core\System\Customer\Aggregate\CustomerGroup\CustomerGroupCollection;

/**
 * @internal
 */
class BaseChannelContextFactory extends AbstractBaseChannelContextFactory
{
    /**
     * @param EntityRepository<ChannelCollection> $channelRepository
     * @param EntityRepository<CurrencyCollection> $currencyRepository
     * @param EntityRepository<CustomerGroupCollection> $customerGroupRepository
     * @param EntityRepository<CountryCollection> $countryRepository
     * @param EntityRepository<CurrencyCountryRoundingCollection> $currencyCountryRepository
     */
    public function __construct(
        private readonly EntityRepository $channelRepository,
        private readonly EntityRepository $currencyRepository,
        private readonly EntityRepository $customerGroupRepository,
        private readonly EntityRepository $countryRepository,
        private readonly EntityRepository $currencyCountryRepository,
        private readonly ContextFactory $contextFactory,
    ) {
    }

    public function create(string $channelId, array $options = []): BaseChannelContext
    {
        $context = $this->contextFactory->getContext($channelId, $options);
        $criteria = new Criteria([$channelId]);
        $criteria->setTitle('base-context-factory::channel');
        $criteria->addAssociation('currency');
        $criteria->addAssociation('domains');
        $criteria->getAssociation('languages')
            ->addFilter(new EqualsFilter('id', $context->getLanguageId()))
            ->addAssociation('translationCode')
            ->addAssociation('locale');
        $channel = $this->channelRepository->search($criteria, $context)->getEntities()->get($channelId);
        if (!$channel instanceof ChannelEntity) {
            throw ChannelException::channelNotFound($channelId);
        }
        $currency = $channel->getCurrency();
        if (\array_key_exists(ChannelContextService::CURRENCY_ID, $options)) {
            $currencyId = $options[ChannelContextService::CURRENCY_ID];
            if (!\is_string($currencyId) || !Uuid::isValid($currencyId)) {
                throw ChannelException::invalidCurrencyId();
            }
            $criteria = new Criteria([$currencyId]);
            $criteria->setTitle('base-context-factory::currency');

            $currency = $this->currencyRepository->search($criteria, $context)->get($currencyId);

            if (!$currency instanceof CurrencyEntity) {
                throw ChannelException::currencyNotFound($currencyId);
            }
        }
        if ($currency === null) {
            throw ChannelException::currencyNotFound($channel->getCurrencyId());
        }

        $groupId = $channel->getCustomerGroupId();

        $criteria = new Criteria([$channel->getCustomerGroupId()]);
        $criteria->setTitle('base-context-factory::customer-group');

        $customerGroup = $this->customerGroupRepository->search($criteria, $context)->getEntities()->get($groupId);
        if ($customerGroup === null) {
            throw ChannelException::customerGroupNotFound($groupId);
        }

        $countryId = $options[ChannelContextService::COUNTRY_ID] ?? $channel->getCountryId();
        if (!\is_string($countryId) || !Uuid::isValid($countryId)) {
            throw ChannelException::invalidCountryId();
        }
        $criteria = new Criteria([$countryId]);
        $criteria->setTitle('base-context-factory::country');

        $country = $this->countryRepository->search($criteria, $context)->get($countryId);

        if (!$country instanceof CountryEntity) {
            throw ChannelException::countryNotFound($countryId);
        }

        [$itemRounding, $totalRounding] = $this->getCashRounding($currency, $countryId, $context);

        $context = new Context(
            $context->getSource(),
            $context->getLanguageIdChain(),
            $context->getVersionId(),
            true,
            $currency->getId(),
            $currency->getFactor(),
            $itemRounding
        );

        return new BaseChannelContext(
            $context,
            $channel,
            $currency,
            $customerGroup,
            $itemRounding,
            $totalRounding,
            $this->getLanguageInfo($channel->getLanguages(), $context->getLanguageId()),
        );
    }

    private function getLanguageInfo(?LanguageCollection $languages, string $currentLanguageId): LanguageInfo
    {
        $currentLanguage = $languages?->get($currentLanguageId);
        if ($currentLanguage === null) {
            throw ChannelException::languageNotFound($currentLanguageId);
        }

        $locale = $currentLanguage->getTranslationCode() ?? $currentLanguage->getLocale();
        \assert($locale !== null, 'At least the localeId is required, so the fallback should never be null');

        return new LanguageInfo(
            $currentLanguage->getTranslation('name') ?? $currentLanguage->getName(),
            $locale->getCode(),
        );
    }

    /**
     * @return CashRoundingConfig[]
     */
    private function getCashRounding(CurrencyEntity $currency, string $countryId, Context $context): array
    {
        $criteria = new Criteria();
        $criteria->setTitle('base-context-factory::cash-rounding');
        $criteria->setLimit(1);
        $criteria->addFilter(new EqualsFilter('currencyId', $currency->getId()));
        $criteria->addFilter(new EqualsFilter('countryId', $countryId));

        $countryConfig = $this->currencyCountryRepository->search($criteria, $context)->first();

        if ($countryConfig instanceof CurrencyCountryRoundingEntity) {
            return [$countryConfig->getItemRounding(), $countryConfig->getTotalRounding()];
        }

        return [$currency->getItemRounding(), $currency->getTotalRounding()];
    }
}
