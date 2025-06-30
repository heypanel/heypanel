<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Store\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;

class StoreInvalidCredentialsException extends HeyPanelHttpException
{
    public function __construct()
    {
        parent::__construct('Invalid credentials');
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__STORE_INVALID_CREDENTIALS';
    }
}
