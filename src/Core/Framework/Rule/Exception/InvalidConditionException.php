<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Rule\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;

class InvalidConditionException extends HeyPanelHttpException
{
    public function __construct(string $conditionName)
    {
        parent::__construct('The condition "{{ condition }}" is invalid.', ['condition' => $conditionName]);
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__INVALID_CONDITION_ERROR';
    }
}
