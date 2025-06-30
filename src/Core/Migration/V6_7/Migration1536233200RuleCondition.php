<?php declare(strict_types=1);

namespace HeyPanel\Core\Migration\V6_7;

use Doctrine\DBAL\Connection;
use HeyPanel\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 *
 * @codeCoverageIgnore
 */
class Migration1536233200RuleCondition extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1536233200;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            CREATE TABLE `rule_condition` (
              `id` binary(16) NOT NULL,
              `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
              `rule_id` binary(16) NOT NULL,
              `parent_id` binary(16) DEFAULT NULL,
              `value` json DEFAULT NULL,
              `position` int NOT NULL DEFAULT 0,
              `custom_fields` json DEFAULT NULL,
              `created_at` datetime(3) NOT NULL,
              `updated_at` datetime(3) DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `fk.rule_condition.rule_id` (`rule_id`),
              KEY `fk.rule_condition.parent_id` (`parent_id`),
              CONSTRAINT `fk.rule_condition.parent_id` FOREIGN KEY (`parent_id`) REFERENCES `rule_condition` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `fk.rule_condition.rule_id` FOREIGN KEY (`rule_id`) REFERENCES `rule` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `json.rule_condition.custom_fields` CHECK (json_valid(`custom_fields`)),
              CONSTRAINT `json.rule_condition.value` CHECK (json_valid(`value`))
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
