<?php declare(strict_types=1);

namespace HeyPanel\Core\Migration\V6_7;

use Doctrine\DBAL\Connection;
use HeyPanel\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 *
 * @codeCoverageIgnore
 */
class Migration1536232960Customer extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1536232960;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<'SQL'
        CREATE TABLE `customer` (
              `id` BINARY(16) NOT NULL,
              `auto_increment` BIGINT unsigned NOT NULL AUTO_INCREMENT,
              `customer_group_id` BINARY(16) NOT NULL,
              `channel_id` BINARY(16) NOT NULL,
              `language_id` BINARY(16) NOT NULL,
              `customer_number` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
              `name` VARCHAR(255) COLLATE utf8mb4_unicode_ci NULL,
              `phoneNumber`     VARCHAR(255) COLLATE utf8mb4_unicode_ci NULL,
              `avatar_id` binary(16) DEFAULT NULL,
              `nickname`     VARCHAR(255) COLLATE utf8mb4_unicode_ci NULL,
              `password` VARCHAR(1024) COLLATE utf8mb4_unicode_ci NULL,
              `legacy_password` VARCHAR(255) COLLATE utf8mb4_unicode_ci NULL,
              `legacy_encoder` VARCHAR(255) COLLATE utf8mb4_unicode_ci NULL,
              `email` VARCHAR(254) COLLATE utf8mb4_unicode_ci NOT NULL,
              `active` TINYINT(1) NOT NULL DEFAULT 1,
              `guest` TINYINT(1) NOT NULL DEFAULT 0,
              `first_login` DATE NULL,
              `last_login` DATETIME(3) NULL,
              `birthday` DATE NULL,
              `custom_fields` JSON NULL,
              `created_at` DATETIME(3) NOT NULL,
              `updated_at` DATETIME(3) NULL,
              PRIMARY KEY (`id`),
              KEY `fk.customer.avatar_id` (`avatar_id`),
              UNIQUE `uniq.auto_increment` (`auto_increment`),
              KEY `idx.firstlogin` (`first_login`),
              KEY `idx.lastlogin` (`last_login`),
              CONSTRAINT `json.customer.custom_fields` CHECK (JSON_VALID(`custom_fields`)),
              CONSTRAINT `fk.customer.customer_group_id` FOREIGN KEY (`customer_group_id`)
                REFERENCES `customer_group` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
              CONSTRAINT `fk.customer.channel_id` FOREIGN KEY (`channel_id`)
                REFERENCES `channel` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
                CONSTRAINT `fk.customer.avatar_id` FOREIGN KEY (`avatar_id`) REFERENCES `media` (`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
