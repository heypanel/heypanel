<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Search\Grouping;

use HeyPanel\Core\Framework\DataAbstractionLayer\Search\CriteriaPartInterface;
use HeyPanel\Core\Framework\Struct\Struct;

/**
 * @final
 */
class FieldGrouping extends Struct implements CriteriaPartInterface
{
    public function __construct(protected readonly string $field)
    {
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getFields(): array
    {
        return [$this->field];
    }
}
