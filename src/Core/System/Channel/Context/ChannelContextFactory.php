<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Channel\Context;

use HeyPanel\Core\Framework\Api\Context\ChannelApiSource;
use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityRepository;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Criteria;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use HeyPanel\Core\Framework\Plugin\Exception\DecorationPatternException;
use HeyPanel\Core\System\Channel\ChannelContext;
use HeyPanel\Core\System\Customer\Aggregate\CustomerGroup\CustomerGroupCollection;
use HeyPanel\Core\System\Customer\CustomerEntity;

class ChannelContextFactory extends AbstractChannelContextFactory
{
    /**
     * @param EntityRepository<CustomerGroupCollection> $customerGroupRepository
     *
     * @internal
     */
    public function __construct(
        private readonly EntityRepository $customerGroupRepository,
        private readonly AbstractBaseChannelContextFactory $baseChannelContextFactory,
    ) {
    }

    public function getDecorated(): AbstractChannelContextFactory
    {
        throw new DecorationPatternException(self::class);
    }

    public function create(string $token, string $channelId, array $options = []): ChannelContext
    {
        $base = $this->baseChannelContextFactory->create($channelId, $options);
        $customer = null;
        if (\is_string($options[ChannelContextService::MEMBER_ID] ?? null)) {
            $customer = $this->loadCustomer($options, $base->getContext());
        }
        $customerGroup = $base->getCurrentCustomerGroup();

        if ($customer) {
            $criteria = new Criteria([$customer->getGroupId()]);
            $criteria->setTitle('context-factory::customer-group');
            $customerGroup = $this->customerGroupRepository->search($criteria, $base->getContext())->getEntities()->first() ?? $customerGroup;
        }

        $context = new Context(
            $base->getContext()->getSource(),
            $base->getContext()->getLanguageIdChain(),
            $base->getContext()->getVersionId(),
            true,
            $base->getCurrencyId(),
            $base->getCurrency()->getFactor(),
            $base->getCurrency()->getItemRounding()
        );

        return new ChannelContext(
            $context,
            $token,
            $base->getChannel(),
            $base->getCurrency(),
            $customerGroup,
            $base->getLanguageInfo(),
            $base->getCurrency()->getItemRounding(),
            $base->getCurrency()->getTotalRounding(),
            $customer,
            \is_string($options[ChannelContextService::DOMAIN_ID] ?? null) ? $options[ChannelContextService::DOMAIN_ID] : null,
        );
    }

    /**
     * @param array<string, mixed> $options
     */
    private function loadCustomer(array $options, Context $context): ?CustomerEntity
    {
        $customerId = $options[ChannelContextService::MEMBER_ID];
        $criteria = new Criteria([$customerId]);
        $criteria->setTitle('context-factory::customer');
        $criteria->addAssociation('salutation');
        $source = $context->getSource();
        \assert($source instanceof ChannelApiSource);

        $criteria->addFilter(new MultiFilter(MultiFilter::CONNECTION_OR, [
            new EqualsFilter('customer.boundChannelId', null),
            new EqualsFilter('customer.boundChannelId', $source->getChannelId()),
        ]));

        return $this->customerGroupRepository->search($criteria, $context)->getEntities()->get($customerId);
    }
}
