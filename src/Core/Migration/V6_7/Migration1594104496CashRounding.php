<?php declare(strict_types=1);

namespace HeyPanel\Core\Migration\V6_7;

use Doctrine\DBAL\Connection;
use HeyPanel\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 *
 * @codeCoverageIgnore
 */
class Migration1594104496CashRounding extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1594104496;
    }

    public function update(Connection $connection): void
    {
        $this->updateSchema($connection);
        $this->migrateData($connection);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }

    private function updateSchema(Connection $connection): void
    {
        $connection->executeStatement('ALTER TABLE `currency` CHANGE `decimal_precision` `decimal_precision` int NULL AFTER `position`;');
        $connection->executeStatement('ALTER TABLE currency ADD COLUMN `item_rounding` JSON NULL;');
        $connection->executeStatement('ALTER TABLE currency ADD COLUMN `total_rounding` JSON NULL;');

        $connection->executeStatement('
CREATE TABLE IF NOT EXISTS `currency_country_rounding` (
  `id` binary(16) NOT NULL,
  `currency_id` binary(16) NOT NULL,
  `country_id` binary(16) NOT NULL,
  `item_rounding` json NOT NULL,
  `total_rounding` json NOT NULL,
  `created_at` datetime(3) NOT NULL,
  `updated_at` datetime(3) NULL,
  PRIMARY KEY (`id`),
  KEY `currency_id` (`currency_id`),
  KEY `country_id` (`country_id`),
  CONSTRAINT `currency_country_rounding_ibfk_2` FOREIGN KEY (`currency_id`) REFERENCES `currency` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `currency_country_rounding_ibfk_3` FOREIGN KEY (`country_id`) REFERENCES `country` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
    }

    private function migrateData(Connection $connection): void
    {
        $currencies = $connection->fetchAllAssociative('SELECT id, decimal_precision FROM currency');

        foreach ($currencies as $currency) {
            $rounding = [
                'decimals' => $currency['decimal_precision'],
                'roundForNet' => true,
                'interval' => 0.01,
            ];

            $connection->executeStatement(
                'UPDATE currency SET item_rounding = :rounding, total_rounding = :rounding WHERE id = :id',
                ['id' => $currency['id'], 'rounding' => json_encode($rounding, \JSON_THROW_ON_ERROR)]
            );
        }
    }
}
