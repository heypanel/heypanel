<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\ImportExport\Event;

use HeyPanel\Core\Content\ImportExport\Aggregate\ImportExportLog\ImportExportLogEntity;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Symfony\Contracts\EventDispatcher\Event;

class EnrichExportCriteriaEvent extends Event
{
    public function __construct(
        private Criteria $criteria,
        private ImportExportLogEntity $logEntity
    ) {
    }

    public function getCriteria(): Criteria
    {
        return $this->criteria;
    }

    public function setCriteria(Criteria $criteria): void
    {
        $this->criteria = $criteria;
    }

    public function getLogEntity(): ImportExportLogEntity
    {
        return $this->logEntity;
    }

    public function setLogEntity(ImportExportLogEntity $logEntity): void
    {
        $this->logEntity = $logEntity;
    }
}
