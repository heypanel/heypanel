<?php declare(strict_types=1);

namespace HeyPanel\Core\Migration\V6_7;

use Doctrine\DBAL\Connection;
use HeyPanel\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 *
 * @codeCoverageIgnore
 */
class Migration1554900301AddAnswerTable extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1554900301;
    }

    public function update(Connection $connection): void
    {
        // implement update

        $connection->executeStatement('
            DROP TABLE IF EXISTS `post_review`;
        ');
        $connection->executeStatement('
            CREATE TABLE `post_review` (
                `id` BINARY(16) NOT NULL,
                `post_id` BINARY(16) NOT NULL,
                `customer_id` BINARY(16) NULL,
                `channel_id` BINARY(16) NULL,
                `language_id` BINARY(16) NULL,
                `external_user` VARCHAR(255) NULL,
                `external_email` VARCHAR(255) NULL,
                `content` LONGTEXT NULL,
                `points` DOUBLE NULL,
                `status` TINYINT(1) NULL DEFAULT \'0\',
                `comment` LONGTEXT NULL,
                `updated_at` DATETIME(3) NULL,
                `created_at` DATETIME(3) NOT NULL,
                PRIMARY KEY (`id`),
                KEY `fk.post_review.post_id` (`post_id`),
                KEY `fk.post_review.customer_id` (`customer_id`),
                KEY `fk.post_review.channel_id` (`channel_id`),
                KEY `fk.post_review.language_id` (`language_id`),
                CONSTRAINT `fk.post_review.post_id` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`)  ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk.post_review.customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`),
                CONSTRAINT `fk.post_review.channel_id` FOREIGN KEY (`channel_id`) REFERENCES `channel` (`id`),
                CONSTRAINT `fk.post_review.language_id` FOREIGN KEY (`language_id`) REFERENCES `language` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
