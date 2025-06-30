<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\ImportExport\Strategy\Import;

use HeyPanel\Core\Content\ImportExport\Struct\Config;
use HeyPanel\Core\Content\ImportExport\Struct\ImportResult;
use HeyPanel\Core\Content\ImportExport\Struct\Progress;
use HeyPanel\Core\Framework\Context;

/**
 * @internal
 */
interface ImportStrategyService
{
    /**
     * @param array<string, mixed> $record
     * @param array<string, mixed> $row
     */
    public function import(
        array $record,
        array $row,
        Config $config,
        Progress $progress,
        Context $context,
    ): ImportResult;

    public function commit(Config $config, Progress $progress, Context $context): ImportResult;
}
