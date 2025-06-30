<?php declare(strict_types=1);

namespace HeyPanel\Core\Migration\V6_7;

use Doctrine\DBAL\Connection;
use HeyPanel\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 *
 * @codeCoverageIgnore
 */
class Migration1621845370AddFlowSequence extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1621845370;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            CREATE TABLE `flow_sequence` (
              `id` binary(16) NOT NULL,
              `flow_id` binary(16) NOT NULL,
              `parent_id` binary(16) DEFAULT NULL,
              `rule_id` binary(16) DEFAULT NULL,
              `action_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              `config` json DEFAULT NULL,
              `position` int NOT NULL DEFAULT 1,
              `display_group` int NOT NULL DEFAULT 1,
              `true_case` tinyint(1) NOT NULL DEFAULT 0,
              `custom_fields` json DEFAULT NULL,
              `created_at` datetime(3) NOT NULL,
              `updated_at` datetime(3) DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `fk.flow_sequence.flow_id` (`flow_id`),
              KEY `fk.flow_sequence.rule_id` (`rule_id`),
              KEY `fk.flow_sequence.parent_id` (`parent_id`),
              CONSTRAINT `fk.flow_sequence.flow_id` FOREIGN KEY (`flow_id`) REFERENCES `flow` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `fk.flow_sequence.parent_id` FOREIGN KEY (`parent_id`) REFERENCES `flow_sequence` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `fk.flow_sequence.rule_id` FOREIGN KEY (`rule_id`) REFERENCES `rule` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
              CONSTRAINT `json.flow_sequence.config` CHECK (json_valid(`config`)),
              CONSTRAINT `json.flow_sequence.custom_fields` CHECK (json_valid(`custom_fields`))
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
