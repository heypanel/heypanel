<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Search\Filter;

/**
 * @final
 */
class NotEqualsFilter extends NotFilter
{
    public function __construct(
        protected readonly string $field,
        protected readonly string|bool|float|int|null $value
    ) {
        parent::__construct(self::CONNECTION_AND, [
            new EqualsFilter($field, $value),
        ]);
    }
}
