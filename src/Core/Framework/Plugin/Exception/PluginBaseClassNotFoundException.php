<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Plugin\Exception;

use HeyPanel\Core\Framework\Plugin\PluginException;
use Symfony\Component\HttpFoundation\Response;

/**
 * @codeCoverageIgnore
 */
class PluginBaseClassNotFoundException extends PluginException
{
    public function __construct(string $baseClass)
    {
        parent::__construct(
            Response::HTTP_BAD_REQUEST,
            'FRAMEWORK__PLUGIN_BASE_CLASS_NOT_FOUND',
            'The class "{{ baseClass }}" is not found. Probably a class loader error. Check your plugin composer.json',
            ['baseClass' => $baseClass]
        );
    }
}
