<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;

class InconsistentCriteriaIdsException extends HeyPanelHttpException
{
    public function __construct()
    {
        parent::__construct('Inconsistent argument for Criteria. Please filter all invalid values first.');
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__INCONSISTENT_CRITERIA_IDS';
    }
}
