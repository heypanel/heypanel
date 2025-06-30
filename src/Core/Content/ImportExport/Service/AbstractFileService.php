<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\ImportExport\Service;

use HeyPanel\Core\Content\ImportExport\Aggregate\ImportExportFile\ImportExportFileEntity;
use HeyPanel\Core\Content\ImportExport\ImportExportProfileEntity;
use HeyPanel\Core\Content\ImportExport\Processing\Writer\AbstractWriter;
use HeyPanel\Core\Framework\Context;
use Symfony\Component\HttpFoundation\File\UploadedFile;

abstract class AbstractFileService
{
    abstract public function getDecorated(): AbstractFileService;

    abstract public function storeFile(
        Context $context,
        \DateTimeInterface $expireDate,
        ?string $sourcePath,
        ?string $originalFileName,
        string $activity,
        ?string $path = null
    ): ImportExportFileEntity;

    abstract public function detectType(UploadedFile $file): string;

    abstract public function getWriter(): AbstractWriter;

    abstract public function generateFilename(ImportExportProfileEntity $profile): string;

    abstract public function updateFile(Context $context, string $fileId, array $data): void;
}
