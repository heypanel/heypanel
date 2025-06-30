<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Plugin\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;

/**
 * @deprecated tag:v6.8.0 - reason:remove-exception - Will be removed, use StoreException::pluginNotAZipFile instead
 */
class PluginNotAZipFileException extends HeyPanelHttpException
{
    public function __construct(string $mimeType)
    {
        parent::__construct(
            'Given file must be a zip file. Given: {{ mimeType }}',
            ['mimeType' => $mimeType]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__PLUGIN_NOT_A_ZIP_FILE';
    }
}
