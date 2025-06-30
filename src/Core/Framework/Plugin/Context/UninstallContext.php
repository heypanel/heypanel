<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Plugin\Context;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\Migration\MigrationCollection;
use HeyPanel\Core\Framework\Plugin;

class UninstallContext extends InstallContext
{
    public function __construct(
        Plugin $plugin,
        Context $context,
        string $currentHeyPanelVersion,
        string $currentPluginVersion,
        MigrationCollection $migrationCollection,
        private readonly bool $keepUserData
    ) {
        parent::__construct($plugin, $context, $currentHeyPanelVersion, $currentPluginVersion, $migrationCollection);
    }

    /**
     * If true is returned, migrations of the plugin will also be removed
     */
    public function keepUserData(): bool
    {
        return $this->keepUserData;
    }
}
