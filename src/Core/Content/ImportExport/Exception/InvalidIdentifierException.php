<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\ImportExport\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;
use Symfony\Component\HttpFoundation\Response;

class InvalidIdentifierException extends HeyPanelHttpException
{
    public function __construct(string $fieldName)
    {
        parent::__construct('The identifier of {{ fieldName }} should not contain pipe character.', ['fieldName' => $fieldName]);
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__IMPORT_EXPORT_INVALID_IDENTIFIER';
    }
}
