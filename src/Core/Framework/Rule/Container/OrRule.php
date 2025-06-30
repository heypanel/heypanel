<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Rule\Container;

use HeyPanel\Core\Framework\Rule\RuleScope;

class OrRule extends Container
{
    final public const RULE_NAME = 'orContainer';

    public function match(RuleScope $scope): bool
    {
        foreach ($this->rules as $rule) {
            if ($rule->match($scope)) {
                return true;
            }
        }

        return false;
    }
}
