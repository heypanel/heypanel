<?php declare(strict_types=1);

namespace HeyPanel\Core\DevOps\StaticAnalyze\PHPStan\Rules;

use HeyPanel\Core\DevOps\StaticAnalyze\PHPStan\Configuration;
use HeyPanel\Core\Framework\Framework;
use HeyPanel\Core\Framework\FrameworkException;
use HeyPanel\Core\Framework\HttpException;
use HeyPanel\Core\Kernel;
use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Throw_;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symfony\Component\Console\Command\Command;

/**
 * @internal
 *
 * @implements Rule<Throw_>
 */
class DomainExceptionRule implements Rule
{
    use InTestClassTrait;

    /**
     * @var list<string>
     */
    private const EXCLUDED_NAMESPACES = [
        'HeyPanel\Core\DevOps\StaticAnalyze\\',
    ];

    /**
     * @var array<string, string>
     */
    private const REMAPPED_DOMAINS = [
        Kernel::class => FrameworkException::class,
        Framework::class => FrameworkException::class,
    ];

    /**
     * @var array<string>
     */
    private array $validExceptionClasses;

    public function __construct(
        private readonly ReflectionProvider $reflectionProvider,
        private readonly Configuration $configuration,
    ) {
        // see src/Core/DevOps/StaticAnalyze/PHPStan/extension.neon for the default config
        $this->validExceptionClasses = $this->configuration->getAllowedNonDomainExceptions();
    }

    public function getNodeType(): string
    {
        return Throw_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if ($this->isInTestClass($scope) || !$scope->isInClass()) {
            return [];
        }

        if (!$node instanceof Throw_) {
            return [];
        }

        if ($node->expr instanceof StaticCall) {
            return $this->validateDomainExceptionClass($node->expr, $scope);
        }

        if (!$node->expr instanceof New_) {
            return [];
        }

        $namespace = $scope->getNamespace();
        if (\is_string($namespace)) {
            foreach (self::EXCLUDED_NAMESPACES as $excludedNamespace) {
                if (\str_starts_with($namespace, $excludedNamespace)) {
                    return [];
                }
            }
        }

        \assert($node->expr->class instanceof Name);
        $exceptionClass = $node->expr->class->toString();

        if (\in_array($exceptionClass, $this->validExceptionClasses, true)) {
            return [];
        }

        // Allow InvalidArgumentException in commands to validate user input
        if ($scope->getClassReflection()->is(Command::class) && $exceptionClass === 'InvalidArgumentException') {
            return [];
        }

        return [
            RuleErrorBuilder::message('Throwing new exceptions within classes are not allowed. Please use domain exception pattern.')
                ->identifier('heypanel.domainException')
                ->build(),
        ];
    }

    /**
     * @return list<IdentifierRuleError>
     */
    private function validateDomainExceptionClass(StaticCall $node, Scope $scope): array
    {
        \assert($node->class instanceof Name);
        $exceptionClass = $node->class->toString();

        if (!\str_starts_with($exceptionClass, 'HeyPanel\\Core\\')) {
            return [];
        }

        $exception = $this->reflectionProvider->getClass($exceptionClass);
        if (!$exception->is(HttpException::class)) {
            return [
                RuleErrorBuilder::message(\sprintf('Domain exception class %s has to extend the \HeyPanel\Core\Framework\HttpException class', $exceptionClass))
                    ->identifier('heypanel.domainException')
                    ->build(),
            ];
        }

        $reflection = $scope->getClassReflection();
        \assert($reflection !== null);
        if (!\str_starts_with($reflection->getName(), 'HeyPanel\\Core\\')) {
            return [];
        }

        if ($this->isRemapped($reflection->getName(), $exceptionClass)) {
            return [];
        }

        $parts = \explode('\\', $reflection->getName());

        $domain = $parts[2] ?? '';
        $sub = $parts[3] ?? '';

        $acceptedClasses = [
            \sprintf('HeyPanel\\Core\\%s\\%s\\%sException', $domain, $sub, $sub),
            \sprintf('HeyPanel\\Core\\%s\\%sException', $domain, $domain),
        ];

        foreach ($acceptedClasses as $expected) {
            if ($exceptionClass === $expected || $exception->is($expected)) {
                return [];
            }
        }

        return [
            RuleErrorBuilder::message(\sprintf('Expected domain exception class %s, got %s', $acceptedClasses[0], $exceptionClass))
                ->identifier('heypanel.domainException')
                ->build(),
        ];
    }

    private function isRemapped(string $source, string $exceptionClass): bool
    {
        if (!\array_key_exists($source, self::REMAPPED_DOMAINS)) {
            return false;
        }

        return self::REMAPPED_DOMAINS[$source] === $exceptionClass;
    }
}
