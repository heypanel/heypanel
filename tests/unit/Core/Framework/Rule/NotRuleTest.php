<?php declare(strict_types=1);

namespace HeyPanel\Tests\Unit\Core\Framework\Rule;

use HeyPanel\Core\Framework\Rule\Container\NotRule;
use HeyPanel\Core\Framework\Rule\SimpleRule;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(NotRule::class)]
class NotRuleTest extends TestCase
{
    public function testAddRuleOnlyAllowsOneRule(): void
    {
        $this->expectException(\RuntimeException::class);

        $rule = new NotRule();
        $rule->addRule(new SimpleRule());
        $rule->addRule(new SimpleRule());
    }
}
