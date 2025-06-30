<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\ImportExport\Processing\Reader;

use HeyPanel\Core\Content\ImportExport\Aggregate\ImportExportLog\ImportExportLogEntity;

class CsvReaderFactory extends AbstractReaderFactory
{
    public function create(ImportExportLogEntity $logEntity): AbstractReader
    {
        return new CsvReader();
    }

    public function supports(ImportExportLogEntity $logEntity): bool
    {
        return $logEntity->getProfile()->getFileType() === 'text/csv';
    }
}
