<?php declare(strict_types=1);

namespace HeyPanel\Core\System\CustomField\Aggregate\CustomFieldSet;

use HeyPanel\Core\Framework\DataAbstractionLayer\Entity;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use HeyPanel\Core\System\CustomField\Aggregate\CustomFieldSetRelation\CustomFieldSetRelationCollection;
use HeyPanel\Core\System\CustomField\CustomFieldCollection;

class CustomFieldSetEntity extends Entity
{
    use EntityIdTrait;

    protected string $name;

    /**
     * @var array<string, mixed>|null
     */
    protected ?array $config = null;

    protected bool $active;

    protected bool $global;

    protected int $position;

    protected ?CustomFieldCollection $customFields = null;

    protected ?CustomFieldSetRelationCollection $relations = null;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getConfig(): ?array
    {
        return $this->config;
    }

    /**
     * @param array<string, mixed>|null $config
     */
    public function setConfig(?array $config): void
    {
        $this->config = $config;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getCustomFields(): ?CustomFieldCollection
    {
        return $this->customFields;
    }

    public function setCustomFields(CustomFieldCollection $customFields): void
    {
        $this->customFields = $customFields;
    }

    public function getRelations(): ?CustomFieldSetRelationCollection
    {
        return $this->relations;
    }

    public function setRelations(CustomFieldSetRelationCollection $relations): void
    {
        $this->relations = $relations;
    }

    public function isGlobal(): bool
    {
        return $this->global;
    }

    public function setGlobal(bool $global): void
    {
        $this->global = $global;
    }
}
