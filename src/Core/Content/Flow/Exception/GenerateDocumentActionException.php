<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Flow\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;

class GenerateDocumentActionException extends HeyPanelHttpException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    public function getErrorCode(): string
    {
        return 'FLOW_BUILDER__DOCUMENT_GENERATION_ERROR';
    }
}
