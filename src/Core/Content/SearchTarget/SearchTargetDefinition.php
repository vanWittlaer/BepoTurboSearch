<?php declare(strict_types=1);

namespace Bepo\TurboSuggest\Core\Content\SearchTarget;

use Bepo\TurboSuggest\Core\Content\SearchTarget\Aggregate\SearchTargetTranslation\SearchTargetTranslationDefinition;
use Bepo\TurboSuggest\Core\Content\SearchTerm\SearchTermDefinition;
use Shopware\Core\Content\Category\CategoryDefinition;
use Shopware\Core\Content\LandingPage\LandingPageDefinition;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\SalesChannel\SalesChannelDefinition;

class SearchTargetDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'bepo_turbo_suggest_target';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return SearchTargetEntity::class;
    }

    public function getCollectionClass(): string
    {
        return SearchTargetCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required(), new ApiAware()),

            (new FkField('category_id', 'categoryId', CategoryDefinition::class))->addFlags(new ApiAware()),
            (new FkField('landing_page_id', 'landingPageId', LandingPageDefinition::class))->addFlags(new ApiAware()),
            (new FkField('product_id', 'productId', ProductDefinition::class))->addFlags(new ApiAware()),
            (new FkField('media_id', 'mediaId', MediaDefinition::class))->addFlags(new ApiAware()),

            (new FkField('sales_channel_id', 'salesChannelId', SalesChannelDefinition::class))->addFlags(new Required(), new ApiAware()),

            (new IntField('priority', 'priority'))->addFlags(new ApiAware()),

            (new TranslatedField('title'))->addFlags(new ApiAware()),
            (new TranslatedField('teaserText'))->addFlags(new ApiAware()),

            (new ManyToOneAssociationField('category', 'category_id', CategoryDefinition::class, 'id', true))->addFlags(new ApiAware()),
            (new ManyToOneAssociationField('landingPage', 'landing_page_id', LandingPageDefinition::class, 'id', true))->addFlags(new ApiAware()),
            (new ManyToOneAssociationField('product', 'product_id', ProductDefinition::class, 'id', true))->addFlags(new ApiAware()),
            (new ManyToOneAssociationField('media', 'media_id', MediaDefinition::class, 'id', true))->addFlags(new ApiAware()),
            (new ManyToOneAssociationField('salesChannel', 'sales_channel_id', SalesChannelDefinition::class, 'id', false))->addFlags(new ApiAware()),

            (new OneToManyAssociationField('searchTerms', SearchTermDefinition::class, 'search_target_id'))->addFlags(new ApiAware()),

            (new TranslationsAssociationField(SearchTargetTranslationDefinition::class, 'bepo_turbo_suggest_target_id'))->addFlags(new ApiAware()),
        ]);
    }
}
