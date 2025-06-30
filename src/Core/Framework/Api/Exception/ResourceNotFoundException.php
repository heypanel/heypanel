<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Api\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;
use Symfony\Component\HttpFoundation\Response;

class ResourceNotFoundException extends HeyPanelHttpException
{
    public function __construct(
        string $resourceType,
        array $primaryKey
    ) {
        $resourceIds = [];
        foreach ($primaryKey as $key => $value) {
            $resourceIds[] = $key . '(' . $value . ')';
        }

        parent::__construct(
            'The {{ type }} resource with the following primary key was not found: {{ primaryKeyString }}',
            ['type' => $resourceType, 'primaryKey' => $primaryKey, 'primaryKeyString' => implode(' ', $resourceIds)]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__RESOURCE_NOT_FOUND';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}
