<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Rule\Container;

use HeyPanel\Core\Framework\Rule\Rule;

interface ContainerInterface
{
    /**
     * @param Rule[] $rules
     */
    public function setRules(array $rules): void;

    public function addRule(Rule $rule): void;
}
