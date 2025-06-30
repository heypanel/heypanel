<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Channel;

use HeyPanel\Core\Defaults;
use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\DataAbstractionLayer\Pricing\CashRoundingConfig;
use HeyPanel\Core\Framework\Struct\StateAwareTrait;
use HeyPanel\Core\Framework\Struct\Struct;
use HeyPanel\Core\System\Channel\Context\LanguageInfo;
use HeyPanel\Core\System\Currency\CurrencyEntity;
use HeyPanel\Core\System\Customer\Aggregate\CustomerGroup\CustomerGroupEntity;
use HeyPanel\Core\System\Customer\CustomerEntity;

class ChannelContext extends Struct
{
    use StateAwareTrait;

    /**
     * @var array<string, bool>
     */
    protected array $permissions = [];

    protected bool $permisionsLocked = false;

    public function __construct(
        protected Context            $context,
        protected string             $token,
        protected ChannelEntity      $channel,
        protected CurrencyEntity     $currency,
        protected CustomerGroupEntity  $currentCustomerGroup,
        protected LanguageInfo       $languageInfo,
        protected CashRoundingConfig $itemRounding,
        protected CashRoundingConfig $totalRounding,
        protected ?CustomerEntity      $customer = null,
        private ?string              $domainId = null
    )
    {
    }

    /**
     * @return array<string>
     */
    public function getRuleIds(): array
    {
        return $this->context->getRuleIds();
    }

    /**
     * @param array<string> $ruleIds
     */
    public function setRuleIds(array $ruleIds): void
    {
        $this->context->setRuleIds($ruleIds);
    }

    public function lockRules(): void
    {
        $this->context->lockRules();
    }


    /**
     * @return CurrencyEntity
     */
    public function getCurrency(): CurrencyEntity
    {
        return $this->currency;
    }

    /**
     * @template TReturn of mixed
     *
     * @param callable(ChannelContext): TReturn $callback
     *
     * @return TReturn the return value of the provided callback function
     */
    public function live(callable $callback): mixed
    {
        $before = $this->context;

        $this->context = $this->context->createWithVersionId(Defaults::LIVE_VERSION);

        $result = $callback($this);

        $this->context = $before;

        return $result;
    }

    /**
     * Executed the callback function with the given permissions set in the ChannelContext. If the
     * permissions are locked, the callback is called with the original permissions of the ChannelContext.
     *
     * @template TReturn of mixed
     *
     * @param array<string, bool> $permissions
     * @param callable(ChannelContext): TReturn $callback
     *
     * @return TReturn the return value of the provided callback function
     */
    public function withPermissions(array $permissions, callable $callback): mixed
    {
        if ($this->permisionsLocked) {
            return $callback($this);
        }

        $originalPermissions = $this->getPermissions();
        $permissions = array_merge($originalPermissions, $permissions);

        $this->setPermissions($permissions);

        $result = $callback($this);

        $this->setPermissions($originalPermissions);

        return $result;
    }

    /**
     * @return array<string, bool>
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    /**
     * @param array<string, bool> $permissions
     */
    public function setPermissions(array $permissions): void
    {
        if ($this->permisionsLocked) {
            throw ChannelException::contextPermissionsLocked();
        }

        $this->permissions = array_filter($permissions);
    }

    public function getCustomerGroupId(): string
    {
        return $this->currentCustomerGroup->getId();
    }

    public function getItemRounding(): CashRoundingConfig
    {
        return $this->itemRounding;
    }

    public function getCurrencyId(): string
    {
        return $this->currency->getId();
    }

    public function getChannel(): ChannelEntity
    {
        return $this->channel;
    }

    public function getCustomer(): ?CustomerEntity
    {
        return $this->customer;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getApiAlias(): string
    {
        return 'channel_context';
    }

    public function hasState(string ...$states): bool
    {
        return $this->context->hasState(...$states);
    }

    /**
     * @return array<string>
     */
    public function getStates(): array
    {
        return $this->context->getStates();
    }

    public function getDomainId(): ?string
    {
        return $this->domainId;
    }

    public function setDomainId(?string $domainId): void
    {
        $this->domainId = $domainId;
    }

    /**
     * @return non-empty-list<string>
     */
    public function getLanguageIdChain(): array
    {
        return $this->context->getLanguageIdChain();
    }

    public function getLanguageId(): string
    {
        return $this->context->getLanguageId();
    }

    public function getVersionId(): string
    {
        return $this->context->getVersionId();
    }

    public function considerInheritance(): bool
    {
        return $this->context->considerInheritance();
    }

    public function getCustomerId(): ?string
    {
        return $this->customer?->getId();
    }

    public function getLanguageInfo(): LanguageInfo
    {
        return $this->languageInfo;
    }

    public function setLanguageInfo(LanguageInfo $languageInfo): void
    {
        $this->languageInfo = $languageInfo;
    }

    public function getChannelId(): string
    {
        return $this->channel->getId();
    }

    public function getTotalRounding(): CashRoundingConfig
    {
        return $this->totalRounding;
    }

    public function setTotalRounding(CashRoundingConfig $totalRounding): void
    {
        $this->totalRounding = $totalRounding;
    }

}
