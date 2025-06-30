<?php declare(strict_types=1);

namespace HeyPanel\Core\System\SystemConfig\Facade;

use Doctrine\DBAL\Connection;
use HeyPanel\Core\Framework\Script\Execution\Awareness\ChannelContextAware;
use HeyPanel\Core\Framework\Script\Execution\Awareness\HookServiceFactory;
use HeyPanel\Core\Framework\Script\Execution\Hook;
use HeyPanel\Core\Framework\Script\Execution\Script;
use HeyPanel\Core\System\SystemConfig\SystemConfigService;

/**
 * @internal
 */
class SystemConfigFacadeHookFactory extends HookServiceFactory
{
    /**
     * @internal
     */
    public function __construct(
        private readonly SystemConfigService $systemConfigService,
        private readonly Connection $connection
    ) {
    }

    public function getName(): string
    {
        return 'config';
    }

    public function factory(Hook $hook, Script $script): SystemConfigFacade
    {
        $channelId = null;

        if ($hook instanceof ChannelContextAware) {
            $channelId = $hook->getChannelContext()->getChannelId();
        }

        return new SystemConfigFacade($this->systemConfigService, $this->connection, $script->getScriptAppInformation(), $channelId);
    }
}
