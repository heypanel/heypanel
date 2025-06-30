<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\ImportExport;

use HeyPanel\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<ImportExportProfileTranslationEntity>
 */
class ImportExportProfileTranslationCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return ImportExportProfileTranslationEntity::class;
    }
}
