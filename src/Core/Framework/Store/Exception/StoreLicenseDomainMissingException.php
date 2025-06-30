<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Store\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;

class StoreLicenseDomainMissingException extends HeyPanelHttpException
{
    public function __construct()
    {
        parent::__construct('Store license domain is missing');
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__STORE_LICENSE_DOMAIN_IS_MISSING';
    }
}
