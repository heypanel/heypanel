<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Flow\Api;

use HeyPanel\Core\Framework\Struct\Collection;

/**
 * @extends Collection<FlowActionDefinition>
 */
class FlowActionCollectorResponse extends Collection
{
    protected function getExpectedClass(): ?string
    {
        return FlowActionDefinition::class;
    }
}
