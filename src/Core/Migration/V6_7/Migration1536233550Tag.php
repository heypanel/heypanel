<?php declare(strict_types=1);

namespace HeyPanel\Core\Migration\V6_7;

use Doctrine\DBAL\Connection;
use HeyPanel\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 *
 * @codeCoverageIgnore
 */
class Migration1536233550Tag extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1536233550;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            CREATE TABLE `tag` (
              `id` BINARY(16) NOT NULL,
              `name` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
              `created_at` DATETIME(3) NOT NULL,
              `updated_at` DATETIME(3) NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');

        $connection->executeStatement('
            CREATE TABLE `post_tag` (
              `post_id` BINARY(16) NOT NULL,
              `tag_id` BINARY(16) NOT NULL,
              PRIMARY KEY (`post_id`, `tag_id`),
              CONSTRAINT `fk.post_tag.post_version_id__post_id` FOREIGN KEY (`post_id`)
                REFERENCES `post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `fk.post_tag.tag_id` FOREIGN KEY (`tag_id`)
                REFERENCES `tag` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');

        $connection->executeStatement('
            CREATE TABLE `media_tag` (
              `media_id` BINARY(16) NOT NULL,
              `tag_id` BINARY(16) NOT NULL,
              PRIMARY KEY (`media_id`, `tag_id`),
              CONSTRAINT `fk.media_tag.id` FOREIGN KEY (`media_id`)
                REFERENCES `media` (`id`),
              CONSTRAINT `fk.media_tag.tag_id` FOREIGN KEY (`tag_id`)
                REFERENCES `tag` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');

        $connection->executeStatement('
            CREATE TABLE `category_tag` (
              `category_id` BINARY(16) NOT NULL,
              `category_version_id` BINARY(16) NOT NULL,
              `tag_id` BINARY(16) NOT NULL,
              PRIMARY KEY (`category_id`, `category_version_id`, `tag_id`),
              CONSTRAINT `fk.category_tag.category_tag__category_version_id` FOREIGN KEY (`category_id`, `category_version_id`)
                REFERENCES `category` (`id`, `version_id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `fk.category_tag.tag_id` FOREIGN KEY (`tag_id`)
                REFERENCES `tag` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');

        $connection->executeStatement('
            CREATE TABLE `customer_tag` (
              `customer_id` BINARY(16) NOT NULL,
              `tag_id` BINARY(16) NOT NULL,
              PRIMARY KEY (`customer_id`, `tag_id`),
              CONSTRAINT `fk.customer_tag.customer_id` FOREIGN KEY (`customer_id`)
                REFERENCES `customer` (`id`),
              CONSTRAINT `fk.customer_tag.tag_id` FOREIGN KEY (`tag_id`)
                REFERENCES `tag` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
