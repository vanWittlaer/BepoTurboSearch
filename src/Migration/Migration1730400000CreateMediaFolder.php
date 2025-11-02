<?php declare(strict_types=1);

namespace Bepo\TurboSuggest\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Content\Media\Aggregate\MediaDefaultFolder\MediaDefaultFolderDefinition;
use Shopware\Core\Content\Media\Aggregate\MediaThumbnailSize\MediaThumbnailSizeDefinition;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;

class Migration1730400000CreateMediaFolder extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1730400000;
    }

    public function update(Connection $connection): void
    {
        $existingDefaultFolder = $connection->fetchOne(
            'SELECT id FROM media_default_folder WHERE entity = :entity',
            ['entity' => 'bepo_turbo_suggest_target']
        );

        if ($existingDefaultFolder) {
            return;
        }

        $defaultFolderId = Uuid::randomBytes();
        $configurationId = Uuid::randomBytes();

        $connection->insert('media_folder_configuration', [
            'id' => $configurationId,
            'thumbnail_quality' => 80,
            'create_thumbnails' => 1,
            'private' => 0,
            'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
        ]);

        $this->addThumbnailSizes($connection, $configurationId);

        $connection->insert('media_default_folder', [
            'id' => $defaultFolderId,
            'entity' => 'bepo_turbo_suggest_target',
            'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
        ]);

        $connection->insert('media_folder', [
            'id' => Uuid::randomBytes(),
            'name' => 'Turbo Suggest Targets',
            'default_folder_id' => $defaultFolderId,
            'media_folder_configuration_id' => $configurationId,
            'use_parent_configuration' => 0,
            'child_count' => 0,
            'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
        ]);
    }

    public function updateDestructive(Connection $connection): void
    {
    }

    private function addThumbnailSizes(Connection $connection, string $configurationId): void
    {
        $thumbnailSizes = [
            ['width' => 400, 'height' => 400],
            ['width' => 800, 'height' => 800],
            ['width' => 1920, 'height' => 1920],
        ];

        foreach ($thumbnailSizes as $size) {
            $sizeId = $this->getThumbnailSizeId($connection, $size['width'], $size['height']);

            if (!$sizeId) {
                continue;
            }

            $connection->insert('media_folder_configuration_media_thumbnail_size', [
                'media_folder_configuration_id' => $configurationId,
                'media_thumbnail_size_id' => $sizeId,
            ]);
        }
    }

    private function getThumbnailSizeId(Connection $connection, int $width, int $height): ?string
    {
        $result = $connection->fetchOne(
            'SELECT id FROM media_thumbnail_size WHERE width = :width AND height = :height',
            ['width' => $width, 'height' => $height]
        );

        return $result ?: null;
    }
}
