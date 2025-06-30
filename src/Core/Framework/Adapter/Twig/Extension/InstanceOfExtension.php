<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Adapter\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigTest;

/**
 * @internal
 */
class InstanceOfExtension extends AbstractExtension
{
    public function getTests(): array
    {
        return [
            'instanceof' => new TwigTest('instanceof', $this->isInstanceOf(...)),
        ];
    }

    /**
     * @deprecated tag:v6.8.0 - arguments will be type-hinted - reason:becomes-internal
     *
     * @param object $var
     * @param class-string $class
     */
    public function isInstanceOf($var, $class): bool
    {
        return (new \ReflectionClass($class))->isInstance($var);
    }
}
