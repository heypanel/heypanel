<?php declare(strict_types=1);

namespace HeyPanel\Core\System\StateMachine\Event;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\Event\EventData\MailRecipientStruct;
use HeyPanel\Core\System\StateMachine\Aggregation\StateMachineState\StateMachineStateEntity;
use HeyPanel\Core\System\StateMachine\StateMachineEntity;
use HeyPanel\Core\System\StateMachine\Transition;
use Symfony\Contracts\EventDispatcher\Event;

class StateMachineStateChangeEvent extends Event
{
    final public const STATE_MACHINE_TRANSITION_SIDE_ENTER = 'state_enter';
    final public const STATE_MACHINE_TRANSITION_SIDE_LEAVE = 'state_leave';

    protected Context $context;

    protected string $channelId;

    protected StateMachineEntity $stateMachine;

    protected string $transitionSide;

    protected string $stateName;

    protected StateMachineStateEntity $previousState;

    protected StateMachineStateEntity $nextState;

    protected Transition $transition;

    public function __construct(
        Context $context,
        string $transitionSide,
        Transition $transition,
        StateMachineEntity $stateMachine,
        StateMachineStateEntity $previousState,
        StateMachineStateEntity $nextState,
        private readonly ?MailRecipientStruct $mailRecipientStruct = null
    ) {
        $this->context = $context;
        $this->stateMachine = $stateMachine;
        $this->transitionSide = $transitionSide;
        $this->previousState = $previousState;
        $this->nextState = $nextState;
        $this->transition = $transition;

        if ($this->transitionSide === static::STATE_MACHINE_TRANSITION_SIDE_ENTER) {
            $this->stateName = $this->nextState->getTechnicalName();
        } else {
            $this->stateName = $this->previousState->getTechnicalName();
        }
    }

    public function getName(): string
    {
        return 'state_machine.' . $this->stateMachine->getTechnicalName() . '_changed';
    }

    public function getStateEventName(): string
    {
        return $this->transitionSide . '.' . $this->stateMachine->getTechnicalName() . '.' . $this->stateName;
    }

    public function getChannelId(): ?string
    {
        return $this->channelId;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function getTransition(): Transition
    {
        return $this->transition;
    }

    public function getNextState(): StateMachineStateEntity
    {
        return $this->nextState;
    }

    public function getPreviousState(): StateMachineStateEntity
    {
        return $this->previousState;
    }

    public function getStateName(): string
    {
        return $this->stateName;
    }

    public function getTransitionSide(): string
    {
        return $this->transitionSide;
    }

    public function getStateMachine(): StateMachineEntity
    {
        return $this->stateMachine;
    }

    public function getMailRecipientStruct(): ?MailRecipientStruct
    {
        return $this->mailRecipientStruct;
    }
}
