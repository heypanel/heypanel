<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\ImportExport\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;
use Symfony\Component\HttpFoundation\Response;

class FileNotFoundException extends HeyPanelHttpException
{
    public function __construct(string $fileId)
    {
        parent::__construct('Cannot find import/export file with id {{ fileId }}', ['fileId' => $fileId]);
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__IMPORT_EXPORT_FILE_NOT_FOUND';
    }
}
