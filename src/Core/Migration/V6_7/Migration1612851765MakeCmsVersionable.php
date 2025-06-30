<?php declare(strict_types=1);

namespace HeyPanel\Core\Migration\V6_7;

use Doctrine\DBAL\Connection;
use HeyPanel\Core\Defaults;
use HeyPanel\Core\Framework\Migration\MakeVersionableMigrationHelper;
use HeyPanel\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 *
 * @codeCoverageIgnore
 */
class Migration1612851765MakeCmsVersionable extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1612851765;
    }

    public function update(Connection $connection): void
    {
        $versionableMigrationHelper = new MakeVersionableMigrationHelper($connection);

        $tables = [
            'cms_page',
            'cms_section',
            'cms_block',
        ];

        foreach ($tables as $table) {
            $hydratedData = $versionableMigrationHelper->getRelationData($table, 'id');
            $playbook = $versionableMigrationHelper->createSql($hydratedData, $table, 'version_id', Defaults::LIVE_VERSION);

            foreach ($playbook as $query) {
                $connection->executeStatement($query);
            }
        }
    }
}
