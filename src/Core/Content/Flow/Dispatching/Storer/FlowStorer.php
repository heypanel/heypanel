<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Flow\Dispatching\Storer;

use HeyPanel\Core\Content\Flow\Dispatching\StorableFlow;
use HeyPanel\Core\Framework\Event\FlowEventAware;

abstract class FlowStorer
{
    /**
     * @param array<string, mixed> $stored
     *
     * @return array<string, mixed>
     */
    abstract public function store(FlowEventAware $event, array $stored): array;

    abstract public function restore(StorableFlow $storable): void;
}
