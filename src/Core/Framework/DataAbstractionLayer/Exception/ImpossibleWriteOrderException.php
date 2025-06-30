<?php
declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;

class ImpossibleWriteOrderException extends HeyPanelHttpException
{
    public function __construct(array $remaining)
    {
        parent::__construct(
            'Can not resolve write order for provided data. Remaining write order classes: {{ classesString }}',
            ['classes' => $remaining, 'classesString' => implode(', ', $remaining)]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__IMPOSSIBLE_WRITE_ORDER';
    }
}
