<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Api\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;
use Symfony\Component\HttpFoundation\Response;

class IncompletePrimaryKeyException extends HeyPanelHttpException
{
    public function __construct(array $primaryKeyFields)
    {
        parent::__construct(
            'The primary key consists of {{ fieldCount }} fields. Please provide values for the following fields: {{ fieldsString }}',
            ['fieldCount' => \count($primaryKeyFields), 'fields' => $primaryKeyFields, 'fieldsString' => implode(', ', $primaryKeyFields)]
        );
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__INCOMPLETE_PRIMARY_KEY';
    }
}
