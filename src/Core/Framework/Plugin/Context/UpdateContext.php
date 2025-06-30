<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Plugin\Context;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\Migration\MigrationCollection;
use HeyPanel\Core\Framework\Plugin;

class UpdateContext extends InstallContext
{
    public function __construct(
        Plugin $plugin,
        Context $context,
        string $currentHeyPanelVersion,
        string $currentPluginVersion,
        MigrationCollection $migrationCollection,
        private readonly string $updatePluginVersion
    ) {
        parent::__construct($plugin, $context, $currentHeyPanelVersion, $currentPluginVersion, $migrationCollection);
    }

    public function getUpdatePluginVersion(): string
    {
        return $this->updatePluginVersion;
    }
}
