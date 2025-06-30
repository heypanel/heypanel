<?php declare(strict_types=1);

namespace HeyPanel\Core\Migration\V6_7;

use Doctrine\DBAL\Connection;
use HeyPanel\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 *
 * @codeCoverageIgnore
 */
class Migration1536233350PostVisibility extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1536233350;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            CREATE TABLE `post_visibility` (
              `id` BINARY(16) NOT NULL,
              `post_id` BINARY(16) NOT NULL,
              `post_version_id` BINARY(16) NOT NULL,
              `channel_id` BINARY(16) NOT NULL,
              `visibility` INT(11) NOT NULL,
              `created_at` DATETIME(3) NOT NULL,
              `updated_at` DATETIME(3) NULL,
              PRIMARY KEY (`id`),
              KEY `idx.post_visibility.post_id` (`post_id`),
              KEY `idx.post_visibility.channel_id` (`channel_id`),
              CONSTRAINT `fk.post_visibility.post_id` FOREIGN KEY (`post_id`)
                REFERENCES `post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `fk.post_visibility.channel_id` FOREIGN KEY (`channel_id`)
                REFERENCES `channel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
