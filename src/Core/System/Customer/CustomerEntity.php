<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Customer;

use HeyPanel\Core\Framework\DataAbstractionLayer\Entity;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use HeyPanel\Core\System\Channel\ChannelEntity;
use HeyPanel\Core\System\Customer\Aggregate\CustomerGroup\CustomerGroupEntity;

class CustomerEntity extends Entity
{
    use EntityCustomFieldsTrait;
    use EntityIdTrait;

    protected string $groupId;

    protected string $email;

    protected string $nickname;

    protected ?string $hash = null;

    protected bool $active;
    protected string $customerNumber;
    /**
     * @internal
     */
    protected ?string $password = null;
    protected ?ChannelEntity $boundChannel = null;

    /**
     * @internal
     */
    protected ?string $legacyEncoder = null;
    /**
     * @internal
     */
    protected ?string $legacyPassword = null;

    protected ?string $boundChannelId = null;

    protected ?CustomerGroupEntity $group = null;

    public function getCustomerNumber(): string
    {
        return $this->customerNumber;
    }

    public function setCustomerNumber(string $customerNumber): void
    {
        $this->customerNumber = $customerNumber;
    }


    public function getBoundChannelId(): ?string
    {
        return $this->boundChannelId;
    }

    public function setBoundChannelId(?string $boundChannelId): void
    {
        $this->boundChannelId = $boundChannelId;
    }

    public function getBoundChannel(): ?ChannelEntity
    {
        return $this->boundChannel;
    }

    public function setBoundChannel(?ChannelEntity $boundChannel): void
    {
        $this->boundChannel = $boundChannel;
    }


    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): void
    {
        $this->hash = $hash;
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    /**
     * @internal
     */
    public function getLegacyEncoder(): ?string
    {
        $this->checkIfPropertyAccessIsAllowed('legacyEncoder');

        return $this->legacyEncoder;
    }

    /**
     * @internal
     */
    public function setLegacyEncoder(?string $legacyEncoder): void
    {
        $this->legacyEncoder = $legacyEncoder;
    }

    /**
     * @internal
     */
    public function getLegacyPassword(): ?string
    {
        $this->checkIfPropertyAccessIsAllowed('legacyPassword');

        return $this->legacyPassword;
    }

    /**
     * @internal
     */
    public function setLegacyPassword(?string $legacyPassword): void
    {
        $this->legacyPassword = $legacyPassword;
    }

    public function hasLegacyPassword(): bool
    {
        return $this->legacyPassword !== null && $this->legacyEncoder !== null;
    }

    public function getNickname(): string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): void
    {
        $this->nickname = $nickname;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @internal
     */
    public function getPassword(): ?string
    {
        $this->checkIfPropertyAccessIsAllowed('password');

        return $this->password;
    }

    /**
     * @internal
     */
    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getGroup(): ?CustomerGroupEntity
    {
        return $this->group;
    }

    public function setGroup(CustomerGroupEntity $group): void
    {
        $this->group = $group;
    }

    public function getGroupId(): string
    {
        return $this->groupId;
    }

    public function setGroupId(string $groupId): void
    {
        $this->groupId = $groupId;
    }
}
