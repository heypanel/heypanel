<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Country\Aggregate\CountryStateTranslation;

use HeyPanel\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use HeyPanel\Core\Framework\DataAbstractionLayer\TranslationEntity;
use HeyPanel\Core\System\Country\Aggregate\CountryState\CountryStateEntity;

class CountryStateTranslationEntity extends TranslationEntity
{
    use EntityCustomFieldsTrait;

    protected string $countryStateId;

    protected ?string $name = null;

    protected ?CountryStateEntity $countryState = null;

    public function getCountryStateId(): string
    {
        return $this->countryStateId;
    }

    public function setCountryStateId(string $countryStateId): void
    {
        $this->countryStateId = $countryStateId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getCountryState(): ?CountryStateEntity
    {
        return $this->countryState;
    }

    public function setCountryState(CountryStateEntity $countryState): void
    {
        $this->countryState = $countryState;
    }
}
