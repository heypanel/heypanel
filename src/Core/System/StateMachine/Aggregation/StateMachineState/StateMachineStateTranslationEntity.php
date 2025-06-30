<?php declare(strict_types=1);

namespace HeyPanel\Core\System\StateMachine\Aggregation\StateMachineState;

use HeyPanel\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use HeyPanel\Core\Framework\DataAbstractionLayer\TranslationEntity;

class StateMachineStateTranslationEntity extends TranslationEntity
{
    use EntityCustomFieldsTrait;

    protected ?string $name = null;

    protected string $stateMachineStateId;

    protected ?StateMachineStateEntity $stateMachineState = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getStateMachineStateId(): string
    {
        return $this->stateMachineStateId;
    }

    public function setStateMachineStateId(string $stateMachineStateId): void
    {
        $this->stateMachineStateId = $stateMachineStateId;
    }

    public function getStateMachineState(): ?StateMachineStateEntity
    {
        return $this->stateMachineState;
    }

    public function setStateMachineState(StateMachineStateEntity $stateMachineState): void
    {
        $this->stateMachineState = $stateMachineState;
    }
}
