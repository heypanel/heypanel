<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Dbal\FieldAccessorBuilder;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Field;

/**
 * @internal
 */
interface FieldAccessorBuilderInterface
{
    public function buildAccessor(string $root, Field $field, Context $context, string $accessor): ?string;
}
