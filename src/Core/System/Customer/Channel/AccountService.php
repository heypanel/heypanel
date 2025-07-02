<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Customer\Channel;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityRepository;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Criteria;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use HeyPanel\Core\Framework\DataAbstractionLayer\Write\WriteException;
use HeyPanel\Core\Framework\Uuid\Uuid;
use HeyPanel\Core\Framework\Validation\WriteConstraintViolationException;
use HeyPanel\Core\System\Channel\ChannelContext;
use HeyPanel\Core\System\Customer\CustomerCollection;
use HeyPanel\Core\System\Customer\CustomerEntity;
use HeyPanel\Core\System\Customer\CustomerException;
use HeyPanel\Core\System\Customer\Event\CustomerBeforeLoginEvent;
use HeyPanel\Core\System\Customer\Event\CustomerLoginEvent;
use HeyPanel\Core\System\Customer\Exception\BadCredentialsException;
use HeyPanel\Core\System\Customer\Exception\CustomerNotFoundByIdException;
use HeyPanel\Core\System\Customer\Exception\CustomerNotFoundException;
use HeyPanel\Core\System\Customer\Password\LegacyPasswordVerifier;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PasswordHasher\Hasher\CheckPasswordLengthTrait;
use Symfony\Component\Validator\ConstraintViolation;

class AccountService
{
    use CheckPasswordLengthTrait;

    /**
     * @internal
     *
     * @param EntityRepository<CustomerCollection> $customerRepository
     */
    public function __construct(
        private readonly EntityRepository $customerRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly LegacyPasswordVerifier $legacyPasswordVerifier,
    ) {
    }

    /**
     * @throws BadCredentialsException
     * @throws CustomerNotFoundByIdException
     */
    public function loginById(string $id, ChannelContext $context): string
    {
        if (!Uuid::isValid($id)) {
            throw CustomerException::badCredentials();
        }

        $customer = $this->fetchCustomer(new Criteria([$id]), $context, true);
        if ($customer === null) {
            throw CustomerException::customerNotFoundByIdException($id);
        }

        $event = new CustomerBeforeLoginEvent($context, $customer->getEmail());
        $this->eventDispatcher->dispatch($event);

        return $this->loginByCustomer($customer, $context);
    }

    /**
     * @throws CustomerNotFoundException
     * @throws BadCredentialsException
     */
    public function loginByCredentials(string $email, string $password, ChannelContext $context): string
    {
        if ($email === '' || $password === '') {
            throw CustomerException::badCredentials();
        }

        $event = new CustomerBeforeLoginEvent($context, $email);
        $this->eventDispatcher->dispatch($event);

        $customer = $this->getCustomerByLogin($email, $password, $context);

        return $this->loginByCustomer($customer, $context);
    }

    /**
     * @throws CustomerNotFoundException
     * @throws BadCredentialsException
     */
    public function getCustomerByLogin(string $email, string $password, ChannelContext $context): CustomerEntity
    {
        if ($this->isPasswordTooLong($password)) {
            throw CustomerException::badCredentials();
        }

        $customer = $this->getCustomerByEmail($email, $context);

        if ($customer->hasLegacyPassword()) {
            if (!$this->legacyPasswordVerifier->verify($password, $customer)) {
                throw CustomerException::badCredentials();
            }

            $this->updatePasswordHash($password, $customer, $context->getContext());

            return $customer;
        }

        if ($customer->getPassword() === null
            || !password_verify($password, $customer->getPassword())) {
            throw CustomerException::badCredentials();
        }

        return $customer;
    }

    /**
     * @throws CustomerNotFoundException
     */
    public function getCustomerByEmail(string $email, ChannelContext $context): CustomerEntity
    {
        $criteria = (new Criteria())
            ->addFilter(new EqualsFilter('email', $email));

        $customer = $this->fetchCustomer($criteria, $context);
        if ($customer === null) {
            throw CustomerException::customerNotFound($email);
        }

        return $customer;
    }

    private function loginByCustomer(CustomerEntity $customer, ChannelContext $context): string
    {
        $this->customerRepository->update([
            [
                'id' => $customer->getId(),
                'lastLogin' => new \DateTimeImmutable(),
            ],
        ], $context->getContext());

        $newToken = $context->getToken();

        $event = new CustomerLoginEvent($context, $customer, $newToken);
        $this->eventDispatcher->dispatch($event);

        return $newToken;
    }

    /**
     * This method filters for the standard customer related constraints like active or the sales channel
     * assignment.
     * Add only filters to the $criteria for values which have an index in the database, e.g. id, or email. The rest
     * should be done via PHP because it's a lot faster to filter a few entities on PHP side with the same email
     * address, than to filter a huge numbers of rows in the DB on a not indexed column.
     */
    private function fetchCustomer(Criteria $criteria, ChannelContext $context, bool $includeGuest = false): ?CustomerEntity
    {
        $criteria->setTitle('account-service::fetchCustomer');

        $result = $this->customerRepository->search($criteria, $context->getContext())->getEntities();
        $result = $result->filter(function (CustomerEntity $customer) use ($context): ?bool {
            // Skip not active users
            if (!$customer->getActive()) {
                return null;
            }

            // If not bound, we still need to consider it
            if ($customer->getBoundChannelId() === null) {
                return true;
            }

            // It is bound, but not to the current one. Skip it
            if ($customer->getBoundChannelId() !== $context->getChannelId()) {
                return null;
            }

            return true;
        });

        // If there is more than one account we want to return the latest, this is important
        // for guest accounts, real customer accounts should only occur once, otherwise the
        // wrong password will be validated
        if ($result->count() > 1) {
            $result->sort(fn (CustomerEntity $a, CustomerEntity $b) => ($a->getCreatedAt() <=> $b->getCreatedAt()) * -1);
        }

        return $result->first();
    }

    private function updatePasswordHash(string $password, CustomerEntity $customer, Context $context): void
    {
        try {
            $this->customerRepository->update([
                [
                    'id' => $customer->getId(),
                    'password' => $password,
                    'legacyPassword' => null,
                    'legacyEncoder' => null,
                ],
            ], $context);
        } catch (WriteException $writeException) {
            $this->handleWriteExceptionForUpdatingPasswordHash($writeException);
        }
    }

    private function handleWriteExceptionForUpdatingPasswordHash(WriteException $writeException): void
    {
        foreach ($writeException->getExceptions() as $exception) {
            if (!$exception instanceof WriteConstraintViolationException) {
                continue;
            }

            /** @var ConstraintViolation $constraintViolation */
            foreach ($exception->getViolations() as $constraintViolation) {
                if ($constraintViolation->getPropertyPath() === '/password') {
                    throw CustomerException::passwordPoliciesUpdated();
                }
            }
        }

        throw $writeException;
    }
}
