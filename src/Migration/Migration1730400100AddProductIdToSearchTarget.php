<?php declare(strict_types=1);

namespace Bepo\TurboSuggest\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1730400100AddProductIdToSearchTarget extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1730400100;
    }

    public function update(Connection $connection): void
    {
        $columnExists = $connection->fetchOne(
            "SELECT COUNT(*) FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
             AND TABLE_NAME = 'bepo_turbo_suggest_target'
             AND COLUMN_NAME = 'product_id'"
        );

        if ($columnExists) {
            return;
        }

        $sql = <<<SQL
ALTER TABLE `bepo_turbo_suggest_target`
ADD COLUMN `product_id` BINARY(16) NULL AFTER `landing_page_id`,
ADD COLUMN `product_version_id` BINARY(16) NULL AFTER `product_id`,
ADD KEY `fk.bepo_turbo_suggest_target.product_id` (`product_id`, `product_version_id`),
ADD CONSTRAINT `fk.bepo_turbo_suggest_target.product_id`
    FOREIGN KEY (`product_id`, `product_version_id`) REFERENCES `product` (`id`, `version_id`) ON DELETE CASCADE ON UPDATE CASCADE;
SQL;
        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
        $sql = <<<SQL
ALTER TABLE `bepo_turbo_suggest_target`
DROP FOREIGN KEY `fk.bepo_turbo_suggest_target.product_id`,
DROP COLUMN `product_id`,
DROP COLUMN `product_version_id`;
SQL;
        $connection->executeStatement($sql);
    }
}
