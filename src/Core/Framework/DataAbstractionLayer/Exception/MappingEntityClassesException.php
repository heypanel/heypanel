<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;

class MappingEntityClassesException extends HeyPanelHttpException
{
    public function __construct()
    {
        parent::__construct('Mapping definition neither have entities nor collection.');
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__MAPPING_ENTITY_DEFINITION_CLASSES';
    }
}
