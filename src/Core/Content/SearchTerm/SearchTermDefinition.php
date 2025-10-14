<?php declare(strict_types=1);

namespace Bepo\TurboSuggest\Core\Content\SearchTerm;

use Bepo\TurboSuggest\Core\Content\SearchTarget\SearchTargetDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\Language\LanguageDefinition;

class SearchTermDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'bepo_turbo_suggest_term';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return SearchTermEntity::class;
    }

    public function getCollectionClass(): string
    {
        return SearchTermCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required(), new ApiAware()),

            (new FkField('search_target_id', 'searchTargetId', SearchTargetDefinition::class))->addFlags(new Required(), new ApiAware()),
            (new StringField('term', 'term'))->addFlags(new Required(), new ApiAware()),
            (new FkField('language_id', 'languageId', LanguageDefinition::class))->addFlags(new Required(), new ApiAware()),

            (new BoolField('active', 'active'))->addFlags(new ApiAware()),

            (new ManyToOneAssociationField('searchTarget', 'search_target_id', SearchTargetDefinition::class, 'id', true))->addFlags(new ApiAware()),
            (new ManyToOneAssociationField('language', 'language_id', LanguageDefinition::class, 'id', false))->addFlags(new ApiAware()),
        ]);
    }
}
