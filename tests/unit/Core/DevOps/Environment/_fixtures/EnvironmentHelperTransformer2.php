<?php declare(strict_types=1);

namespace HeyPanel\Tests\Unit\Core\DevOps\Environment\_fixtures;

use HeyPanel\Core\DevOps\Environment\EnvironmentHelperTransformerData;
use HeyPanel\Core\DevOps\Environment\EnvironmentHelperTransformerInterface;

/**
 * @internal
 */
class EnvironmentHelperTransformer2 implements EnvironmentHelperTransformerInterface
{
    public static function transform(EnvironmentHelperTransformerData $data): void
    {
        $data->setValue($data->getValue() !== null ? $data->getValue() . ' baz' : null);
    }
}
