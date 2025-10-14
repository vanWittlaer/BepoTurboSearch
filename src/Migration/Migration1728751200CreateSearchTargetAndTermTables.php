<?php declare(strict_types=1);

namespace Bepo\TurboSuggest\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1728751200CreateSearchTargetAndTermTables extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1728751200;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `bepo_turbo_suggest_target` (
    `id` BINARY(16) NOT NULL,
    `category_id` BINARY(16) NULL,
    `cms_page_id` BINARY(16) NULL,
    `sales_channel_id` BINARY(16) NOT NULL,
    `priority` INT(11) NOT NULL DEFAULT 0,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`),
    KEY `fk.bepo_turbo_suggest_target.category_id` (`category_id`),
    KEY `fk.bepo_turbo_suggest_target.cms_page_id` (`cms_page_id`),
    KEY `fk.bepo_turbo_suggest_target.sales_channel_id` (`sales_channel_id`),
    CONSTRAINT `fk.bepo_turbo_suggest_target.sales_channel_id`
        FOREIGN KEY (`sales_channel_id`) REFERENCES `sales_channel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;
        $connection->executeStatement($sql);

        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `bepo_turbo_suggest_target_translation` (
    `bepo_turbo_suggest_target_id` BINARY(16) NOT NULL,
    `language_id` BINARY(16) NOT NULL,
    `title` VARCHAR(255) NULL,
    `teaser_text` TEXT NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`bepo_turbo_suggest_target_id`, `language_id`),
    CONSTRAINT `fk.bepo_turbo_suggest_target_translation.target_id`
        FOREIGN KEY (`bepo_turbo_suggest_target_id`) REFERENCES `bepo_turbo_suggest_target` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk.bepo_turbo_suggest_target_translation.language_id`
        FOREIGN KEY (`language_id`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;
        $connection->executeStatement($sql);

        // Add teaser_text column if table already exists
        $columnExists = $connection->fetchOne(
            "SHOW COLUMNS FROM `bepo_turbo_suggest_target_translation` LIKE 'teaser_text'"
        );

        if (!$columnExists) {
            $connection->executeStatement(
                'ALTER TABLE `bepo_turbo_suggest_target_translation` ADD COLUMN `teaser_text` TEXT NULL AFTER `title`'
            );
        }

        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `bepo_turbo_suggest_term` (
    `id` BINARY(16) NOT NULL,
    `search_target_id` BINARY(16) NOT NULL,
    `term` VARCHAR(255) NOT NULL,
    `language_id` BINARY(16) NOT NULL,
    `active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq.bepo_turbo_suggest_term.term_language_target` (`term`, `language_id`, `search_target_id`),
    CONSTRAINT `fk.bepo_turbo_suggest_term.search_target_id`
        FOREIGN KEY (`search_target_id`) REFERENCES `bepo_turbo_suggest_target` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk.bepo_turbo_suggest_term.language_id`
        FOREIGN KEY (`language_id`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;
        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
        $connection->executeStatement('DROP TABLE IF EXISTS `bepo_turbo_suggest_term`');
        $connection->executeStatement('DROP TABLE IF EXISTS `bepo_turbo_suggest_target_translation`');
        $connection->executeStatement('DROP TABLE IF EXISTS `bepo_turbo_suggest_target`');
    }
}
