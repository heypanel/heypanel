<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Flow\Dispatching\Aware;

use HeyPanel\Core\Framework\Event\IsFlowEventAware;

#[IsFlowEventAware]
interface ScalarValuesAware
{
    public const STORE_VALUES = 'store_values';

    /**
     * @return array<string, scalar|array<mixed>|null>
     */
    public function getValues(): array;
}
