<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Plugin\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;

class PluginExtractionException extends HeyPanelHttpException
{
    public function __construct(string $reason)
    {
        parent::__construct(
            'Plugin extraction failed. Error: {{ error }}',
            ['error' => $reason]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__PLUGIN_EXTRACTION_FAILED';
    }
}
