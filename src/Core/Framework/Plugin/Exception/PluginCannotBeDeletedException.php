<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Plugin\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;

class PluginCannotBeDeletedException extends HeyPanelHttpException
{
    public function __construct(string $reason)
    {
        parent::__construct(
            'Cannot delete plugin. Error: {{ error }}',
            ['error' => $reason]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__PLUGIN_CANNOT_BE_DELETED';
    }
}
