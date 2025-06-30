<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\ImportExport\Event;

use HeyPanel\Core\Content\ImportExport\Aggregate\ImportExportLog\ImportExportLogEntity;
use HeyPanel\Core\Content\ImportExport\Struct\Progress;
use HeyPanel\Core\Framework\Context;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @codeCoverageIgnore
 */
class ImportExportAfterProcessFinishedEvent extends Event
{
    /**
     * @internal
     */
    public function __construct(
        private readonly Context $context,
        private readonly ImportExportLogEntity $logEntity,
        private readonly Progress $progress
    ) {
    }

    public function getLogEntity(): ImportExportLogEntity
    {
        return $this->logEntity;
    }

    public function getProgress(): Progress
    {
        return $this->progress;
    }

    public function getContext(): Context
    {
        return $this->context;
    }
}
