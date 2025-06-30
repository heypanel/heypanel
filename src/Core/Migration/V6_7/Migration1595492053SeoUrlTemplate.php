<?php declare(strict_types=1);

namespace HeyPanel\Core\Migration\V6_7;

use Doctrine\DBAL\Connection;
use HeyPanel\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 *
 * @codeCoverageIgnore
 */
class Migration1595492053SeoUrlTemplate extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1595492053;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `seo_url_template` (
                `id` BINARY(16) NOT NULL PRIMARY KEY,
                `channel_id` BINARY(16) NULL,
                `route_name` VARCHAR(255) NOT NULL,
                `entity_name` VARCHAR(64) NOT NULL,
                `template` VARCHAR(750) NOT NULL,
                `is_valid` TINYINT(1) NOT NULL DEFAULT 1,
                `custom_fields` JSON NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                CONSTRAINT `uniq.seo_url_template.route_name`
                    UNIQUE (`channel_id`, `route_name`),
                CONSTRAINT `fk.seo_url_template.channel_id` FOREIGN KEY (`channel_id`)
                    REFERENCES `channel` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
                CONSTRAINT `json.seo_url_template.custom_fields` CHECK (JSON_VALID(`custom_fields`))
            )
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
