<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer;

use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Field;

abstract class BulkEntityExtension
{
    /**
     * Constructor is final to ensure the extensions can be built without any dependencies
     */
    final public function __construct()
    {
    }

    /**
     * @return \Generator<string, list<Field>>
     */
    abstract public function collect(): \Generator;
}
