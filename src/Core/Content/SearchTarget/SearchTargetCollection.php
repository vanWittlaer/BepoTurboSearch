<?php declare(strict_types=1);

namespace Bepo\TurboSuggest\Core\Content\SearchTarget;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                      add(SearchTargetEntity $entity)
 * @method void                      set(string $key, SearchTargetEntity $entity)
 * @method SearchTargetEntity[]      getIterator()
 * @method SearchTargetEntity[]      getElements()
 * @method SearchTargetEntity|null   get(string $key)
 * @method SearchTargetEntity|null   first()
 * @method SearchTargetEntity|null   last()
 */
class SearchTargetCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return SearchTargetEntity::class;
    }
}
