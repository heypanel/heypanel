<?php declare(strict_types=1);

namespace HeyPanel\Core\Migration\V6_7;

use Doctrine\DBAL\Connection;
use HeyPanel\Core\Framework\Log\Package;
use HeyPanel\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 *
 * @codeCoverageIgnore
 */
#[Package('framework')]
class Migration1613665555AddHomeCmsVersionToChannel extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1613665555;
    }

    public function update(Connection $connection): void
    {
        $this->dropForeignKeyIfExists($connection, 'channel', 'fk.channel.home_cms_page_id');

        $sql = <<<'SQL'
ALTER TABLE `channel`
    ADD COLUMN `home_cms_page_version_id` BINARY(16)     NULL                AFTER `home_cms_page_id`;
SQL;
        $connection->executeStatement($sql);

        $sql = <<<'SQL'
ALTER TABLE `channel`
    ADD CONSTRAINT `fk.channel.home_cms_page`
            FOREIGN KEY (`home_cms_page_id`, `home_cms_page_version_id`)
            REFERENCES `cms_page` (`id`, `version_id`)
            ON DELETE RESTRICT
            ON UPDATE CASCADE;
SQL;
        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
