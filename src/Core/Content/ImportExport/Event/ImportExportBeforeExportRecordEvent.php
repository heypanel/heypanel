<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\ImportExport\Event;

use HeyPanel\Core\Content\ImportExport\Struct\Config;
use Symfony\Contracts\EventDispatcher\Event;

class ImportExportBeforeExportRecordEvent extends Event
{
    public function __construct(
        private readonly Config $config,
        private array $record,
        private readonly array $originalRecord
    ) {
    }

    public function getRecord(): array
    {
        return $this->record;
    }

    public function setRecord(array $record): void
    {
        $this->record = $record;
    }

    public function getOriginalRecord(): array
    {
        return $this->originalRecord;
    }

    public function getConfig(): Config
    {
        return $this->config;
    }
}
