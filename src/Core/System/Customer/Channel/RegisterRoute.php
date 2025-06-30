<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Customer\Channel;

use Doctrine\DBAL\Connection;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityRepository;
use HeyPanel\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexerRegistry;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Criteria;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use HeyPanel\Core\Framework\DataAbstractionLayer\Validation\EntityExists;
use HeyPanel\Core\Framework\Event\DataMappingEvent;
use HeyPanel\Core\Framework\Plugin\Exception\DecorationPatternException;
use HeyPanel\Core\Framework\Uuid\Uuid;
use HeyPanel\Core\Framework\Validation\BuildValidationEvent;
use HeyPanel\Core\Framework\Validation\DataBag\DataBag;
use HeyPanel\Core\Framework\Validation\DataBag\RequestDataBag;
use HeyPanel\Core\Framework\Validation\DataValidationDefinition;
use HeyPanel\Core\Framework\Validation\DataValidationFactoryInterface;
use HeyPanel\Core\Framework\Validation\DataValidator;
use HeyPanel\Core\Framework\Validation\Exception\ConstraintViolationException;
use HeyPanel\Core\PlatformRequest;
use HeyPanel\Core\System\Channel\Aggregate\ChannelDomain\ChannelDomainCollection;
use HeyPanel\Core\System\Channel\Aggregate\ChannelDomain\ChannelDomainEntity;
use HeyPanel\Core\System\Channel\ChannelContext;
use HeyPanel\Core\System\Channel\ClientApiCustomFieldMapper;
use HeyPanel\Core\System\Channel\Context\ChannelContextPersister;
use HeyPanel\Core\System\Channel\Context\ChannelContextServiceInterface;
use HeyPanel\Core\System\Channel\Context\ChannelContextServiceParameters;
use HeyPanel\Core\System\Customer\Event\CustomerLoginEvent;
use HeyPanel\Core\System\Customer\Event\CustomerRegisterEvent;
use HeyPanel\Core\System\Customer\CustomerCollection;
use HeyPanel\Core\System\Customer\CustomerDefinition;
use HeyPanel\Core\System\Customer\CustomerEvents;
use HeyPanel\Core\System\Customer\Service\EmailIdnConverter;
use HeyPanel\Core\System\Customer\Validation\Constraint\CustomerEmailUnique;
use HeyPanel\Core\System\NumberRange\ValueGenerator\NumberRangeValueGeneratorInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Route(defaults: ['_routeScope' => ['client-api']])]
class RegisterRoute extends AbstractRegisterRoute
{
    /**
     * @param EntityRepository<CustomerCollection> $customerRepository
     *
     * @internal
     */
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly NumberRangeValueGeneratorInterface $numberRangeValueGenerator,
        private readonly DataValidator $validator,
        private readonly DataValidationFactoryInterface $accountValidationFactory,
        private readonly EntityRepository $customerRepository,
        private readonly ClientApiCustomFieldMapper $customFieldMapper,
        private readonly ChannelContextPersister $contextPersister,
        protected Connection $connection,
        private readonly ChannelContextServiceInterface $contextService,
        private readonly DataValidationFactoryInterface $passwordValidationFactory,
    ) {
    }

    public function getDecorated(): AbstractRegisterRoute
    {
        throw new DecorationPatternException(self::class);
    }

    #[Route(path: '/client-api/account/register', name: 'client-api.account.register', methods: ['POST'])]
    public function register(RequestDataBag $data, ChannelContext $context, bool $validateStorefrontUrl = true, ?DataValidationDefinition $additionalValidationDefinitions = null): CustomerResponse
    {
        EmailIdnConverter::encodeDataBag($data);

        $this->validateRegistrationData($data, $context, $additionalValidationDefinitions, $validateStorefrontUrl);

        $customer = $this->mapCustomerData($data, $context);

        $customer['boundChannelId'] = $this->getBoundChannelId($customer['email'], $context);

        if ($data->get('customFields') instanceof RequestDataBag) {
            $customer['customFields'] = $this->customFieldMapper->map(CustomerDefinition::ENTITY_NAME, $data->get('customFields'));
        }
        // Convert all DataBags to array
        $customer = array_map(static function (mixed $value) {
            if ($value instanceof DataBag) {
                return $value->all();
            }

            return $value;
        }, $customer);

        $writeContext = clone $context->getContext();
        $writeContext->addState(EntityIndexerRegistry::USE_INDEXING_QUEUE);

        $this->customerRepository->create([$customer], $writeContext);

        $criteria = new Criteria([$customer['id']]);

        $customerEntity = $this->customerRepository->search($criteria, $context->getContext())->getEntities()->first();
        \assert(assertion: $customerEntity !== null);

        $response = new CustomerResponse($customerEntity);
        $newToken = $this->contextPersister->replace($context->getToken(), $context);
        $this->contextPersister->save(
            $newToken,
            [
                'customerId' => $customerEntity->getId(),
                'domainId' => $context->getDomainId(),
            ],
            $context->getChannelId(),
            $customerEntity->getId()
        );
        $new = $this->contextService->get(
            new ChannelContextServiceParameters(
                $context->getChannelId(),
                $newToken,
                $context->getLanguageId(),
                $context->getCurrencyId(),
                $context->getDomainId(),
                null,
                $customerEntity->getId()
            )
        );
        $new->addState(...$context->getStates());

        $this->eventDispatcher->dispatch(new CustomerRegisterEvent($new, $customerEntity));

        $event = new CustomerLoginEvent($new, $customerEntity, $newToken);
        $this->eventDispatcher->dispatch($event);

        $response->headers->set(PlatformRequest::HEADER_CONTEXT_TOKEN, $newToken);

        // We don't want to leak the hash in store-api
        $customerEntity->setHash('');

        return $response;
    }

    /**
     * @return array<string, mixed>
     */
    private function mapCustomerData(DataBag $data, ChannelContext $context): array
    {
        $customer = [
            'customerNumber' => $this->numberRangeValueGenerator->getValue(
                $this->customerRepository->getDefinition()->getEntityName(),
                $context->getContext(),
                $context->getChannelId(),
            ),
            'channelId' => $context->getChannelId(),
            'languageId' => $context->getLanguageId(),
            'groupId' => $context->getCustomerGroupId(),
            'email' => $data->get('email'),
            'firstLogin' => new \DateTimeImmutable(),
            'active' => true,
            'password' => $data->get('password'),
        ];
        $event = new DataMappingEvent($data, $customer, $context->getContext());
        $this->eventDispatcher->dispatch($event, CustomerEvents::MAPPING_REGISTER_CUSTOMER);

        $customer = $event->getOutput();
        $customer['id'] = Uuid::randomHex();

        return $customer;
    }

    private function validateRegistrationData(
        DataBag $data,
        ChannelContext $context,
        ?DataValidationDefinition $additionalValidations,
        bool $validateStorefrontUrl
    ): void {
        $definition = $this->getCustomerCreateValidationDefinition($data, $context);
        if ($additionalValidations) {
            $definition->merge($additionalValidations);
        }
        if ($validateStorefrontUrl) {
            $definition
                ->add('frontendUrl', new NotBlank(), new Choice($this->getDomainUrls($context)));
        }
        $violations = $this->validator->getViolations($data->all(), $definition);

        if (!$violations->count()) {
            return;
        }

        throw new ConstraintViolationException($violations, $data->all());
    }

    private function getCustomerCreateValidationDefinition(DataBag $data, ChannelContext $context): DataValidationDefinition
    {
        $validation = $this->accountValidationFactory->create($context);
        $criteria = (new Criteria())
            ->addFilter(new EqualsFilter('registrationChannels.id', $context->getChannelId()));
        $validation->add('requestedGroupId', new EntityExists([
            'entity' => 'customer_group',
            'context' => $context->getContext(),
            'criteria' => $criteria,
        ]));

        $validation->merge(
            $this->passwordValidationFactory->create($context)
        );
        $options = ['context' => $context->getContext(), 'channelContext' => $context];
        $validation->add('email', new CustomerEmailUnique($options));

        $validationEvent = new BuildValidationEvent($validation, $data, $context->getContext());
        $this->eventDispatcher->dispatch($validationEvent, $validationEvent->getName());

        return $validation;
    }

    /**
     * @return list<string>
     */
    private function getDomainUrls(ChannelContext $context): array
    {
        $channelDomainCollection = $context->getChannel()->getDomains();
        \assert($channelDomainCollection instanceof ChannelDomainCollection);

        return array_values(array_map(static fn (ChannelDomainEntity $domainEntity) => rtrim($domainEntity->getUrl(), '/'), $channelDomainCollection->getElements()));
    }

    private function getBoundChannelId(string $email, ChannelContext $context): ?string
    {
        $bindCustomers = $this->systemConfigService->get('core.systemWideLoginRegistration.isCustomerBoundToChannel');
        $salesChannelId = $context->getChannelId();

        if ($bindCustomers) {
            return $salesChannelId;
        }

        if ($this->hasBoundAccount($email)) {
            return $salesChannelId;
        }

        return null;
    }

    private function hasBoundAccount(string $email): bool
    {
        $query = $this->connection->createQueryBuilder();

        $results = $query
            ->select('LOWER(HEX(bound_channel_id)) as bound_channel_id')
            ->from('customer')
            ->where($query->expr()->eq('email', $query->createPositionalParameter($email)))
            ->executeQuery()
            ->fetchAllAssociative();

        foreach ($results as $result) {
            if ($result['bound_channel_id']) {
                return true;
            }
        }

        return false;
    }
}
