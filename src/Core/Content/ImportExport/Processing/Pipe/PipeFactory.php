<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\ImportExport\Processing\Pipe;

use HeyPanel\Core\Content\ImportExport\Aggregate\ImportExportLog\ImportExportLogEntity;
use HeyPanel\Core\Content\ImportExport\DataAbstractionLayer\Serializer\PrimaryKeyResolver;
use HeyPanel\Core\Content\ImportExport\DataAbstractionLayer\Serializer\SerializerRegistry;
use HeyPanel\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;

/**
 * @internal
 */
class PipeFactory extends AbstractPipeFactory
{
    public function __construct(
        private readonly DefinitionInstanceRegistry $definitionInstanceRegistry,
        private readonly SerializerRegistry $serializerRegistry,
        private readonly PrimaryKeyResolver $primaryKeyResolver
    ) {
    }

    public function create(ImportExportLogEntity $logEntity): AbstractPipe
    {
        $pipe = new ChainPipe([
            new EntityPipe(
                $this->definitionInstanceRegistry,
                $this->serializerRegistry,
                null,
                null,
                $this->primaryKeyResolver
            ),
            new KeyMappingPipe(),
        ]);

        return $pipe;
    }

    public function supports(ImportExportLogEntity $logEntity): bool
    {
        return true;
    }
}
