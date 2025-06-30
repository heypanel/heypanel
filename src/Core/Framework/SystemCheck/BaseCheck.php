<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\SystemCheck;

use HeyPanel\Core\Framework\SystemCheck\Check\Category;
use HeyPanel\Core\Framework\SystemCheck\Check\Result;
use HeyPanel\Core\Framework\SystemCheck\Check\SystemCheckExecutionContext;

abstract class BaseCheck
{
    abstract public function run(): Result;

    abstract public function category(): Category;

    abstract public function name(): string;

    public function allowedToRunIn(SystemCheckExecutionContext $context): bool
    {
        return \in_array($context, $this->allowedSystemCheckExecutionContexts(), true);
    }

    /**
     * @return array<SystemCheckExecutionContext>
     */
    abstract protected function allowedSystemCheckExecutionContexts(): array;
}
