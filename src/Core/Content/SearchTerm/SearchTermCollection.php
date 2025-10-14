<?php declare(strict_types=1);

namespace Bepo\TurboSuggest\Core\Content\SearchTerm;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                    add(SearchTermEntity $entity)
 * @method void                    set(string $key, SearchTermEntity $entity)
 * @method SearchTermEntity[]      getIterator()
 * @method SearchTermEntity[]      getElements()
 * @method SearchTermEntity|null   get(string $key)
 * @method SearchTermEntity|null   first()
 * @method SearchTermEntity|null   last()
 */
class SearchTermCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return SearchTermEntity::class;
    }
}
