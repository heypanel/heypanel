<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Store\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;

class StoreNotAvailableException extends HeyPanelHttpException
{
    public function __construct()
    {
        parent::__construct('Store is not available');
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__STORE_NOT_AVAILABLE';
    }
}
