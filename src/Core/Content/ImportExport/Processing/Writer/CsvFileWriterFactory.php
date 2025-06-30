<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\ImportExport\Processing\Writer;

use HeyPanel\Core\Content\ImportExport\Aggregate\ImportExportLog\ImportExportLogEntity;
use League\Flysystem\FilesystemOperator;

class CsvFileWriterFactory extends AbstractWriterFactory
{
    /**
     * @internal
     */
    public function __construct(private readonly FilesystemOperator $filesystem)
    {
    }

    public function create(ImportExportLogEntity $logEntity): AbstractWriter
    {
        return new CsvFileWriter($this->filesystem);
    }

    public function supports(ImportExportLogEntity $logEntity): bool
    {
        return $logEntity->getProfile()?->getFileType() === 'text/csv';
    }
}
