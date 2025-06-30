<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\ImportExport\Processing\Pipe;

use HeyPanel\Core\Content\ImportExport\Struct\Config;

/**
 * @internal
 */
abstract class AbstractPipe
{
    abstract public function in(Config $config, iterable $record): iterable;

    abstract public function out(Config $config, iterable $record): iterable;
}
