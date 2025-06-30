<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Store\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;
use Symfony\Component\HttpFoundation\Response;

class StoreSessionExpiredException extends HeyPanelHttpException
{
    public function __construct()
    {
        parent::__construct('Store session has expired');
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__STORE_SESSION_EXPIRED';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_FORBIDDEN;
    }
}
