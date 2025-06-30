<?php declare(strict_types=1);

namespace HeyPanel\Core\System\StateMachine\Exception;

use HeyPanel\Core\System\StateMachine\StateMachineException;
use Symfony\Component\HttpFoundation\Response;

class IllegalTransitionException extends StateMachineException
{
    /**
     * @param array<mixed> $possibleTransitions
     */
    public function __construct(
        string $currentState,
        string $transition,
        array $possibleTransitions
    ) {
        parent::__construct(
            Response::HTTP_BAD_REQUEST,
            self::ILLEGAL_STATE_TRANSITION,
            'Illegal transition "{{ transition }}" from state "{{ currentState }}". Possible transitions are: {{ possibleTransitionsString }}',
            [
                'transition' => $transition,
                'currentState' => $currentState,
                'possibleTransitionsString' => implode(', ', $possibleTransitions),
                'possibleTransitions' => $possibleTransitions,
            ]
        );
    }
}
