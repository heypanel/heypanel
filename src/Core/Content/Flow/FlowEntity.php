<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Flow;

use HeyPanel\Core\Content\Flow\Aggregate\FlowSequence\FlowSequenceCollection;
use HeyPanel\Core\Content\Flow\Dispatching\Struct\Flow;
use HeyPanel\Core\Framework\DataAbstractionLayer\Entity;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class FlowEntity extends Entity
{
    use EntityCustomFieldsTrait;
    use EntityIdTrait;

    protected string $name;

    protected string $eventName;

    protected string $description;

    protected bool $active;

    protected int $priority;

    /**
     * @internal
     */
    protected string|Flow|null $payload = null;

    protected bool $invalid;

    protected ?FlowSequenceCollection $sequences = null;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEventName(): string
    {
        return $this->eventName;
    }

    public function setEventName(string $eventName): void
    {
        $this->eventName = $eventName;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

    /**
     * @internal
     *
     * @return string|Flow|null
     */
    public function getPayload()
    {
        $this->checkIfPropertyAccessIsAllowed('payload');

        return $this->payload;
    }

    /**
     * @internal
     *
     * @param string|Flow|null $payload
     */
    public function setPayload($payload): void
    {
        $this->payload = $payload;
    }

    public function isInvalid(): bool
    {
        return $this->invalid;
    }

    public function setInvalid(bool $invalid): void
    {
        $this->invalid = $invalid;
    }

    public function getSequences(): ?FlowSequenceCollection
    {
        return $this->sequences;
    }

    public function setSequences(FlowSequenceCollection $sequences): void
    {
        $this->sequences = $sequences;
    }
}
