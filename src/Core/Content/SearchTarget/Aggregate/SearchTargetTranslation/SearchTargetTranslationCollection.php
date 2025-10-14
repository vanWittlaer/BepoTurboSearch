<?php declare(strict_types=1);

namespace Bepo\TurboSuggest\Core\Content\SearchTarget\Aggregate\SearchTargetTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                                add(SearchTargetTranslationEntity $entity)
 * @method void                                set(string $key, SearchTargetTranslationEntity $entity)
 * @method SearchTargetTranslationEntity[]     getIterator()
 * @method SearchTargetTranslationEntity[]     getElements()
 * @method SearchTargetTranslationEntity|null  get(string $key)
 * @method SearchTargetTranslationEntity|null  first()
 * @method SearchTargetTranslationEntity|null  last()
 */
class SearchTargetTranslationCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return SearchTargetTranslationEntity::class;
    }
}
