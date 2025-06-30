<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Store\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;
use Symfony\Component\HttpFoundation\Response;

class ShopSecretInvalidException extends HeyPanelHttpException
{
    public function __construct()
    {
        parent::__construct('Store shop secret is invalid');
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__STORE_SHOP_SECRET_INVALID';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_FORBIDDEN;
    }
}
