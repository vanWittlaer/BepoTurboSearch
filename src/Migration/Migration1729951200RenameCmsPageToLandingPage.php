<?php declare(strict_types=1);

namespace Bepo\TurboSuggest\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1729951200RenameCmsPageToLandingPage extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1729951200;
    }

    public function update(Connection $connection): void
    {
        $columnExists = $connection->fetchOne(
            "SELECT COUNT(*) FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'bepo_turbo_suggest_target'
            AND COLUMN_NAME = 'cms_page_id'"
        );

        if ($columnExists) {
            $sql = <<<SQL
ALTER TABLE `bepo_turbo_suggest_target`
    CHANGE COLUMN `cms_page_id` `landing_page_id` BINARY(16) NULL;
SQL;
            $connection->executeStatement($sql);

            $keyExists = $connection->fetchOne(
                "SELECT COUNT(*) FROM information_schema.STATISTICS
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'bepo_turbo_suggest_target'
                AND INDEX_NAME = 'fk.bepo_turbo_suggest_target.cms_page_id'"
            );

            if ($keyExists) {
                $sql = <<<SQL
ALTER TABLE `bepo_turbo_suggest_target`
    DROP KEY `fk.bepo_turbo_suggest_target.cms_page_id`,
    ADD KEY `fk.bepo_turbo_suggest_target.landing_page_id` (`landing_page_id`);
SQL;
                $connection->executeStatement($sql);
            }
        }
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
