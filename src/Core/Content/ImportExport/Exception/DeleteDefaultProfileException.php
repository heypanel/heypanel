<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\ImportExport\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;
use Symfony\Component\HttpFoundation\Response;

class DeleteDefaultProfileException extends HeyPanelHttpException
{
    public function __construct(array $ids)
    {
        parent::__construct('Cannot delete system default import_export_profile', ['ids' => $ids]);
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__IMPORT_EXPORT_DELETE_DEFAULT_PROFILE';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
