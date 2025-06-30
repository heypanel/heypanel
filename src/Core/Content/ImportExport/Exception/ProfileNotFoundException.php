<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\ImportExport\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;
use Symfony\Component\HttpFoundation\Response;

class ProfileNotFoundException extends HeyPanelHttpException
{
    public function __construct(string $profileId)
    {
        parent::__construct('Cannot find import/export profile with id {{ profileId }}', ['profileId' => $profileId]);
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__IMPORT_EXPORT_PROFILE_NOT_FOUND';
    }
}
