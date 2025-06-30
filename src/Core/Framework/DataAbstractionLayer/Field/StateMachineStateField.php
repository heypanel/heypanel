<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Field;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\DataAbstractionLayer\FieldSerializer\StateMachineStateFieldSerializer;
use HeyPanel\Core\System\StateMachine\Aggregation\StateMachineState\StateMachineStateDefinition;

class StateMachineStateField extends FkField
{
    /**
     * @param array $allowedWriteScopes List of scopes, for which changing the status value is still allowed without
     *                                  using the StateMachine
     */
    public function __construct(
        string $storageName,
        string $propertyName,
        private readonly string $stateMachineName,
        private readonly array $allowedWriteScopes = [Context::SYSTEM_SCOPE]
    ) {
        parent::__construct($storageName, $propertyName, StateMachineStateDefinition::class);
    }

    public function getStateMachineName(): string
    {
        return $this->stateMachineName;
    }

    public function getAllowedWriteScopes(): array
    {
        return $this->allowedWriteScopes;
    }

    protected function getSerializerClass(): string
    {
        return StateMachineStateFieldSerializer::class;
    }
}
