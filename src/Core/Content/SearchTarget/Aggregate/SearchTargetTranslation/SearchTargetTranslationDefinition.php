<?php declare(strict_types=1);

namespace Bepo\TurboSuggest\Core\Content\SearchTarget\Aggregate\SearchTargetTranslation;

use Bepo\TurboSuggest\Core\Content\SearchTarget\SearchTargetDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class SearchTargetTranslationDefinition extends EntityTranslationDefinition
{
    public const ENTITY_NAME = 'bepo_turbo_suggest_target_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return SearchTargetTranslationEntity::class;
    }

    public function getCollectionClass(): string
    {
        return SearchTargetTranslationCollection::class;
    }

    protected function getParentDefinitionClass(): string
    {
        return SearchTargetDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new StringField('title', 'title'))->addFlags(new ApiAware()),
            (new LongTextField('teaser_text', 'teaserText'))->addFlags(new ApiAware()),
        ]);
    }
}
