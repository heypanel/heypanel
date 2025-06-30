<?php declare(strict_types=1);

namespace HeyPanel\Core\Migration\V6_7;

use Doctrine\DBAL\Connection;
use HeyPanel\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 *
 * @codeCoverageIgnore
 */
class Migration1595492052SeoUrl extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1595492052;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<'SQL'
            CREATE TABLE IF NOT EXISTS `seo_url` (
                `id` BINARY(16) NOT NULL,
                `language_id` BINARY(16) NOT NULL,
                `channel_id` BINARY(16) NULL,
                `foreign_key` BINARY(16) NOT NULL,
                `route_name` VARCHAR(50) COLLATE utf8mb4_unicode_ci NOT NULL,
                `path_info` VARCHAR(750) COLLATE 'utf8mb4_unicode_ci' NOT NULL,
                `seo_path_info` VARCHAR(750) COLLATE 'utf8mb4_unicode_ci' NOT NULL,
                `is_canonical` TINYINT(1) NULL DEFAULT NULL,
                `is_modified` TINYINT(1) NOT NULL DEFAULT 0,
                `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
                `custom_fields` JSON NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`),
                CONSTRAINT `json.seo_url.custom_fields` CHECK (JSON_VALID(`custom_fields`)),
                CONSTRAINT `fk.seo_url.channel_id` FOREIGN KEY (`channel_id`)
                    REFERENCES `channel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk.seo_url.language_id` FOREIGN KEY (`language_id`)
                    REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `uniq.seo_url.seo_path_info` UNIQUE (`language_id`, `channel_id`, `seo_path_info`),
                CONSTRAINT `uniq.seo_url.foreign_key` UNIQUE (`language_id`, `channel_id`, `foreign_key`, `route_name`, `is_canonical`),
                INDEX `idx.foreign_key` (`language_id`, `foreign_key`, `channel_id`, `is_canonical`),
                INDEX `idx.path_info` (`language_id`, `channel_id`, `is_canonical`, `path_info`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL;

        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
