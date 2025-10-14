<?php declare(strict_types=1);

namespace Bepo\TurboSuggest\Tests\Service;

use Bepo\TurboSuggest\Core\Content\SearchTarget\SearchTargetCollection;
use Bepo\TurboSuggest\Core\Content\SearchTarget\SearchTargetEntity;
use Bepo\TurboSuggest\Core\Content\SearchTerm\SearchTermEntity;
use Bepo\TurboSuggest\Service\SearchTargetLoader;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\PrefixFilter;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class SearchTargetLoaderTest extends TestCase
{
    private EntityRepository $termRepository;
    private SearchTargetLoader $loader;

    protected function setUp(): void
    {
        $this->termRepository = $this->createMock(EntityRepository::class);
        $this->loader = new SearchTargetLoader($this->termRepository);
    }

    public function testLoadMatchingTargetsWithEmptySearchTerm(): void
    {
        $context = $this->createMock(SalesChannelContext::class);

        $result = $this->loader->loadMatchingTargets('', $context);

        static::assertInstanceOf(SearchTargetCollection::class, $result);
        static::assertCount(0, $result);
    }

    public function testLoadMatchingTargetsWithExactMatch(): void
    {
        $searchTerm = 'shirts';
        $salesChannelContext = $this->createMock(SalesChannelContext::class);
        $salesChannelContext->method('getSalesChannelId')->willReturn('sales-channel-id');

        $context = $this->createMock(Context::class);
        $context->method('getLanguageId')->willReturn('language-id');
        $salesChannelContext->method('getContext')->willReturn($context);

        // Create target entity
        $target = new SearchTargetEntity();
        $target->setId('target-id');
        $target->setPriority(10);

        // Create search term with target association
        $term = new SearchTermEntity();
        $term->setId('term-id');
        $term->setTerm('shirts');
        $term->setSearchTarget($target);

        // Mock search result for exact match with count() > 0
        $searchResult = $this->createMock(EntitySearchResult::class);
        $searchResult->method('first')->willReturn($term);
        $searchResult->method('count')->willReturn(1);
        $searchResult->method('getIterator')->willReturn(new \ArrayIterator([$term]));

        // Service calls search() once for exact match, returns early because valid target found
        $this->termRepository
            ->expects(static::once())
            ->method('search')
            ->with(
                static::callback(function (Criteria $criteria) {
                    $filters = $criteria->getFilters();
                    foreach ($filters as $filter) {
                        if ($filter instanceof EqualsFilter && $filter->getField() === 'term') {
                            return true;
                        }
                    }
                    return false;
                }),
                static::anything()
            )
            ->willReturn($searchResult);

        $result = $this->loader->loadMatchingTargets($searchTerm, $salesChannelContext);

        static::assertInstanceOf(SearchTargetCollection::class, $result);
        static::assertCount(1, $result);
        static::assertSame($target, $result->first());
    }

    public function testLoadMatchingTargetsWithPrefixMatch(): void
    {
        $searchTerm = 'shi';
        $salesChannelContext = $this->createMock(SalesChannelContext::class);
        $salesChannelContext->method('getSalesChannelId')->willReturn('sales-channel-id');

        $context = $this->createMock(Context::class);
        $context->method('getLanguageId')->willReturn('language-id');
        $salesChannelContext->method('getContext')->willReturn($context);

        // Create target entities
        $target1 = new SearchTargetEntity();
        $target1->setId('target-1');
        $target1->setPriority(20);

        $target2 = new SearchTargetEntity();
        $target2->setId('target-2');
        $target2->setPriority(10);

        // Create search terms with same length to ensure both are returned
        $term1 = new SearchTermEntity();
        $term1->setId('term-1');
        $term1->setTerm('shirt');
        $term1->setSearchTarget($target1);

        $term2 = new SearchTermEntity();
        $term2->setId('term-2');
        $term2->setTerm('shine');
        $term2->setSearchTarget($target2);

        // Mock empty result for exact match
        $exactSearchResult = $this->createMock(EntitySearchResult::class);
        $exactSearchResult->method('first')->willReturn(null);
        $exactSearchResult->method('count')->willReturn(0);

        // Mock prefix search result
        $prefixSearchResult = $this->createMock(EntitySearchResult::class);
        $prefixSearchResult->method('getElements')->willReturn([$term1, $term2]);

        // Service calls search() twice: first for exact match (returns empty), then for prefix match
        $this->termRepository
            ->expects(static::exactly(2))
            ->method('search')
            ->with(
                static::callback(function (Criteria $criteria) {
                    $filters = $criteria->getFilters();
                    // Verify either EqualsFilter or PrefixFilter exists
                    foreach ($filters as $filter) {
                        if ($filter instanceof EqualsFilter && $filter->getField() === 'term') {
                            return true;
                        }
                        if ($filter instanceof PrefixFilter && $filter->getField() === 'term') {
                            return true;
                        }
                    }
                    return false;
                }),
                static::anything()
            )
            ->willReturnOnConsecutiveCalls($exactSearchResult, $prefixSearchResult);

        $result = $this->loader->loadMatchingTargets($searchTerm, $salesChannelContext);

        static::assertInstanceOf(SearchTargetCollection::class, $result);
        // Should return both targets (same term length), sorted by priority (target1 first)
        static::assertCount(2, $result);

        $firstTarget = $result->first();
        static::assertNotNull($firstTarget);
        static::assertEquals(20, $firstTarget->getPriority());
    }

    public function testLoadMatchingTargetsReturnsShortestTermFirst(): void
    {
        $searchTerm = 'sh';
        $salesChannelContext = $this->createMock(SalesChannelContext::class);
        $salesChannelContext->method('getSalesChannelId')->willReturn('sales-channel-id');

        $context = $this->createMock(Context::class);
        $context->method('getLanguageId')->willReturn('language-id');
        $salesChannelContext->method('getContext')->willReturn($context);

        // Create target
        $target = new SearchTargetEntity();
        $target->setId('target-1');
        $target->setPriority(10);

        // Create terms with different lengths
        $shortTerm = new SearchTermEntity();
        $shortTerm->setId('term-1');
        $shortTerm->setTerm('shi');
        $shortTerm->setSearchTarget($target);

        $longTerm = new SearchTermEntity();
        $longTerm->setId('term-2');
        $longTerm->setTerm('shirts');
        $longTerm->setSearchTarget($target);

        // Mock empty exact match
        $exactSearchResult = $this->createMock(EntitySearchResult::class);
        $exactSearchResult->method('first')->willReturn(null);

        // Mock prefix match - return longer term first (to test sorting)
        $prefixSearchResult = $this->createMock(EntitySearchResult::class);
        $prefixSearchResult->method('getElements')->willReturn([$longTerm, $shortTerm]);

        $this->termRepository
            ->expects(static::exactly(2))
            ->method('search')
            ->willReturnOnConsecutiveCalls($exactSearchResult, $prefixSearchResult);

        $result = $this->loader->loadMatchingTargets($searchTerm, $salesChannelContext);

        static::assertInstanceOf(SearchTargetCollection::class, $result);
        static::assertCount(1, $result);
    }

    public function testLoadMatchingTargetsWithNoMatches(): void
    {
        $searchTerm = 'nonexistent';
        $salesChannelContext = $this->createMock(SalesChannelContext::class);
        $salesChannelContext->method('getSalesChannelId')->willReturn('sales-channel-id');

        $context = $this->createMock(Context::class);
        $context->method('getLanguageId')->willReturn('language-id');
        $salesChannelContext->method('getContext')->willReturn($context);

        // Mock empty results
        $exactSearchResult = $this->createMock(EntitySearchResult::class);
        $exactSearchResult->method('first')->willReturn(null);

        $prefixSearchResult = $this->createMock(EntitySearchResult::class);
        $prefixSearchResult->method('getElements')->willReturn([]);

        $this->termRepository
            ->expects(static::exactly(2))
            ->method('search')
            ->willReturnOnConsecutiveCalls($exactSearchResult, $prefixSearchResult);

        $result = $this->loader->loadMatchingTargets($searchTerm, $salesChannelContext);

        static::assertInstanceOf(SearchTargetCollection::class, $result);
        static::assertCount(0, $result);
    }

    public function testLoadMatchingTargetsFiltersActiveTermsOnly(): void
    {
        $searchTerm = 'test';
        $salesChannelContext = $this->createMock(SalesChannelContext::class);
        $salesChannelContext->method('getSalesChannelId')->willReturn('sales-channel-id');

        $context = $this->createMock(Context::class);
        $context->method('getLanguageId')->willReturn('language-id');
        $salesChannelContext->method('getContext')->willReturn($context);

        // Mock search result
        $searchResult = $this->createMock(EntitySearchResult::class);
        $searchResult->method('first')->willReturn(null);
        $searchResult->method('getElements')->willReturn([]);

        // Service calls search() twice: exact match then prefix match
        $this->termRepository
            ->expects(static::atLeastOnce())
            ->method('search')
            ->with(
                static::callback(function (Criteria $criteria) {
                    $filters = $criteria->getFilters();
                    $hasActiveFilter = false;

                    foreach ($filters as $filter) {
                        if ($filter instanceof EqualsFilter
                            && $filter->getField() === 'active'
                            && $filter->getValue() === true
                        ) {
                            $hasActiveFilter = true;
                            break;
                        }
                    }

                    return $hasActiveFilter;
                }),
                static::anything()
            )
            ->willReturn($searchResult);

        $this->loader->loadMatchingTargets($searchTerm, $salesChannelContext);
    }
}
