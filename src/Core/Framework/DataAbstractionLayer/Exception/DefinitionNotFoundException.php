<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;

class DefinitionNotFoundException extends HeyPanelHttpException
{
    public function __construct(string $entity)
    {
        parent::__construct(
            'Definition for entity "{{ entityName }}" does not exist.',
            ['entityName' => $entity]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__DEFINITION_NOT_FOUND';
    }
}
