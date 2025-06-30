<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\ImportExport\Service;

use HeyPanel\Core\Content\ImportExport\Processing\Mapping\MappingCollection;
use HeyPanel\Core\Framework\Context;
use Symfony\Component\HttpFoundation\File\UploadedFile;

abstract class AbstractMappingService
{
    abstract public function getDecorated(): AbstractMappingService;

    abstract public function createTemplate(Context $context, string $profileId): string;

    abstract public function getMappingFromTemplate(
        Context $context,
        UploadedFile $file,
        string $sourceEntity,
        string $delimiter = ';',
        string $enclosure = '"',
        string $escape = '\\'
    ): MappingCollection;
}
