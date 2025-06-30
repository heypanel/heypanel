<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Api\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;

class NoEntityClonedException extends HeyPanelHttpException
{
    public function __construct(
        string $entity,
        string $id
    ) {
        parent::__construct(
            'Could not clone entity {{ entity }} with id {{ id }}.',
            ['entity' => $entity, 'id' => $id]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__NO_ENTITIY_CLONED_ERROR';
    }
}
