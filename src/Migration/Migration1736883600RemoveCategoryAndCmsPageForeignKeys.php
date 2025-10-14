<?php declare(strict_types=1);

namespace Bepo\TurboSuggest\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1736883600RemoveCategoryAndCmsPageForeignKeys extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1736883600;
    }

    public function update(Connection $connection): void
    {
        // Drop foreign key constraints for category_id and cms_page_id
        // These tables use versioning with composite primary keys which causes issues in MariaDB

        // Check if foreign key exists before dropping
        $foreignKeys = $connection->fetchAllAssociative(
            "SELECT CONSTRAINT_NAME
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'bepo_turbo_suggest_target'
            AND CONSTRAINT_TYPE = 'FOREIGN KEY'"
        );

        foreach ($foreignKeys as $fk) {
            $constraintName = $fk['CONSTRAINT_NAME'];

            if ($constraintName === 'fk.bepo_turbo_suggest_target.category_id') {
                $connection->executeStatement(
                    'ALTER TABLE `bepo_turbo_suggest_target` DROP FOREIGN KEY `fk.bepo_turbo_suggest_target.category_id`'
                );
            }

            if ($constraintName === 'fk.bepo_turbo_suggest_target.cms_page_id') {
                $connection->executeStatement(
                    'ALTER TABLE `bepo_turbo_suggest_target` DROP FOREIGN KEY `fk.bepo_turbo_suggest_target.cms_page_id`'
                );
            }
        }

        // Ensure indexes exist for performance (may already exist from previous migration)
        $indexes = $connection->fetchAllAssociative(
            "SHOW INDEX FROM `bepo_turbo_suggest_target` WHERE Key_name = 'fk.bepo_turbo_suggest_target.category_id'"
        );

        if (empty($indexes)) {
            $connection->executeStatement(
                'ALTER TABLE `bepo_turbo_suggest_target` ADD KEY `fk.bepo_turbo_suggest_target.category_id` (`category_id`)'
            );
        }

        $indexes = $connection->fetchAllAssociative(
            "SHOW INDEX FROM `bepo_turbo_suggest_target` WHERE Key_name = 'fk.bepo_turbo_suggest_target.cms_page_id'"
        );

        if (empty($indexes)) {
            $connection->executeStatement(
                'ALTER TABLE `bepo_turbo_suggest_target` ADD KEY `fk.bepo_turbo_suggest_target.cms_page_id` (`cms_page_id`)'
            );
        }
    }

    public function updateDestructive(Connection $connection): void
    {
        // No destructive changes needed
    }
}
