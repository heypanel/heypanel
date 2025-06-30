<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Plugin\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;

class NoPluginFoundInZipException extends HeyPanelHttpException
{
    public function __construct(string $archive)
    {
        parent::__construct(
            'No plugin was found in the zip archive: {{ archive }}',
            ['archive' => $archive]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__PLUGIN_NO_PLUGIN_FOUND_IN_ZIP';
    }
}
