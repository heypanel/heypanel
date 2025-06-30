<?php declare(strict_types=1);

namespace HeyPanel\Core\DevOps\StaticAnalyze\PHPStan\Rules;

use HeyPanel\Core\Checkout\Cart\Rule\AlwaysValidRule;
use HeyPanel\Core\Checkout\Cart\Rule\GoodsCountRule;
use HeyPanel\Core\Checkout\Cart\Rule\GoodsPriceRule;
use HeyPanel\Core\Checkout\Cart\Rule\LineItemCustomFieldRule;
use HeyPanel\Core\Checkout\Cart\Rule\LineItemGoodsTotalRule;
use HeyPanel\Core\Checkout\Cart\Rule\LineItemGroupRule;
use HeyPanel\Core\Checkout\Cart\Rule\LineItemInCategoryRule;
use HeyPanel\Core\Checkout\Cart\Rule\LineItemPropertyRule;
use HeyPanel\Core\Checkout\Cart\Rule\LineItemPurchasePriceRule;
use HeyPanel\Core\Checkout\Cart\Rule\LineItemRule;
use HeyPanel\Core\Checkout\Cart\Rule\LineItemWithQuantityRule;
use HeyPanel\Core\Checkout\Cart\Rule\LineItemWrapperRule;
use HeyPanel\Core\Checkout\Customer\Rule\BillingZipCodeRule;
use HeyPanel\Core\Checkout\Customer\Rule\CustomerCustomFieldRule;
use HeyPanel\Core\Checkout\Customer\Rule\ShippingZipCodeRule;
use HeyPanel\Core\Framework\Rule\Container\AndRule;
use HeyPanel\Core\Framework\Rule\Container\Container;
use HeyPanel\Core\Framework\Rule\Container\FilterRule;
use HeyPanel\Core\Framework\Rule\Container\MatchAllLineItemsRule;
use HeyPanel\Core\Framework\Rule\Container\NotRule;
use HeyPanel\Core\Framework\Rule\Container\OrRule;
use HeyPanel\Core\Framework\Rule\Container\XorRule;
use HeyPanel\Core\Framework\Rule\Container\ZipCodeRule;
use HeyPanel\Core\Framework\Rule\DateRangeRule;
use HeyPanel\Core\Framework\Rule\Rule as HeyPanelRule;
use HeyPanel\Core\Framework\Rule\ScriptRule;
use HeyPanel\Core\Framework\Rule\SimpleRule;
use HeyPanel\Core\Framework\Rule\TimeRangeRule;
use HeyPanel\Core\Test\Stub\Rule\FalseRule;
use HeyPanel\Core\Test\Stub\Rule\TrueRule;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<InClassNode>
 *
 * @internal
 */
class RuleConditionHasRuleConfigRule implements Rule
{
    /**
     * @var list<string>
     */
    private array $rulesAllowedToBeWithoutConfig = [
        ZipCodeRule::class,
        FilterRule::class,
        Container::class,
        AndRule::class,
        NotRule::class,
        OrRule::class,
        XorRule::class,
        MatchAllLineItemsRule::class,
        ScriptRule::class,
        DateRangeRule::class,
        SimpleRule::class,
        TimeRangeRule::class,
        GoodsCountRule::class,
        GoodsPriceRule::class,
        LineItemRule::class,
        LineItemWithQuantityRule::class,
        LineItemWrapperRule::class,
        BillingZipCodeRule::class,
        ShippingZipCodeRule::class,
        AlwaysValidRule::class,
        LineItemPropertyRule::class,
        LineItemPurchasePriceRule::class,
        LineItemInCategoryRule::class,
        LineItemCustomFieldRule::class,
        LineItemGoodsTotalRule::class,
        CustomerCustomFieldRule::class,
        LineItemGroupRule::class,
        FalseRule::class,
        TrueRule::class,
    ];

    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     *
     * @return array<array-key, RuleError|string>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (!$this->isRuleClass($scope) || $this->isAllowed($scope) || $this->isValid($scope)) {
            if ($this->isAllowed($scope) && $this->isValid($scope)) {
                return [
                    RuleErrorBuilder::message('This class is implementing the getConfig function and has a own admin component. Remove getConfig or the component.')
                        ->identifier('heypanel.ruleConfig')
                        ->build(),
                ];
            }

            return [];
        }

        return [
            RuleErrorBuilder::message('This class has to implement getConfig or implement a new admin component.')
                ->identifier('heypanel.ruleConfig')
                ->build(),
        ];
    }

    private function isValid(Scope $scope): bool
    {
        $class = $scope->getClassReflection();
        if ($class === null || !$class->hasMethod('getConfig')) {
            return false;
        }

        $declaringClass = $class->getMethod('getConfig', $scope)->getDeclaringClass();

        return $declaringClass->getName() !== HeyPanelRule::class;
    }

    private function isAllowed(Scope $scope): bool
    {
        $class = $scope->getClassReflection();
        if ($class === null) {
            return false;
        }

        return \in_array($class->getName(), $this->rulesAllowedToBeWithoutConfig, true);
    }

    private function isRuleClass(Scope $scope): bool
    {
        $class = $scope->getClassReflection();
        if ($class === null) {
            return false;
        }

        $namespace = $class->getName();
        if (!\str_contains($namespace, 'HeyPanel\\Tests\\Unit\\') && !\str_contains($namespace, 'HeyPanel\\Tests\\Migration\\')) {
            return false;
        }

        return $class->is(HeyPanelRule::class);
    }
}
