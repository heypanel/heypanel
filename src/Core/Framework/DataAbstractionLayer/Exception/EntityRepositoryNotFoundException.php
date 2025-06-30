<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;
use Symfony\Component\HttpFoundation\Response;

class EntityRepositoryNotFoundException extends HeyPanelHttpException
{
    public function __construct(string $entity)
    {
        parent::__construct(
            'EntityRepository for entity "{{ entityName }}" does not exist.',
            ['entityName' => $entity]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__EntityRepository_NOT_FOUND';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
