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

    public function testLoadMatchingTargetsWithPrefixMatch(): void
    {
        $searchTerm = 'shi';
        $salesChannelContext = $this->createMock(SalesChannelContext::class);
        $salesChannelContext->method('getSalesChannelId')->willReturn('sales-channel-id');

        $context = $this->createMock(Context::class);
        $context->method('getLanguageId')->willReturn('language-id');
        $salesChannelContext->method('getContext')->willReturn($context);

        $target1 = new SearchTargetEntity();
        $target1->setId('target-1');
        $target1->setPriority(20);

        $target2 = new SearchTargetEntity();
        $target2->setId('target-2');
        $target2->setPriority(10);

        $term1 = new SearchTermEntity();
        $term1->setId('term-1');
        $term1->setTerm('shirt');
        $term1->setSearchTarget($target1);

        $term2 = new SearchTermEntity();
        $term2->setId('term-2');
        $term2->setTerm('shine');
        $term2->setSearchTarget($target2);

        $searchResult = $this->createMock(EntitySearchResult::class);
        $searchResult->method('getIterator')->willReturn(new \ArrayIterator([$term1, $term2]));

        $this->termRepository
            ->expects(static::once())
            ->method('search')
            ->with(
                static::callback(function (Criteria $criteria) {
                    $filters = $criteria->getFilters();
                    $hasPrefixFilter = false;
                    $hasActiveFilter = false;

                    foreach ($filters as $filter) {
                        if ($filter instanceof PrefixFilter && $filter->getField() === 'term') {
                            $hasPrefixFilter = true;
                        }
                        if ($filter instanceof EqualsFilter && $filter->getField() === 'active') {
                            $hasActiveFilter = true;
                        }
                    }

                    return $hasPrefixFilter && $hasActiveFilter;
                }),
                static::anything()
            )
            ->willReturn($searchResult);

        $result = $this->loader->loadMatchingTargets($searchTerm, $salesChannelContext);

        static::assertInstanceOf(SearchTargetCollection::class, $result);
        static::assertCount(2, $result);

        $firstTarget = $result->first();
        static::assertNotNull($firstTarget);
        static::assertEquals(20, $firstTarget->getPriority());
    }

    public function testLoadMatchingTargetsSortsByPriority(): void
    {
        $searchTerm = 'test';
        $salesChannelContext = $this->createMock(SalesChannelContext::class);
        $salesChannelContext->method('getSalesChannelId')->willReturn('sales-channel-id');

        $context = $this->createMock(Context::class);
        $context->method('getLanguageId')->willReturn('language-id');
        $salesChannelContext->method('getContext')->willReturn($context);

        $lowPriorityTarget = new SearchTargetEntity();
        $lowPriorityTarget->setId('target-1');
        $lowPriorityTarget->setPriority(5);

        $highPriorityTarget = new SearchTargetEntity();
        $highPriorityTarget->setId('target-2');
        $highPriorityTarget->setPriority(20);

        $term1 = new SearchTermEntity();
        $term1->setId('term-1');
        $term1->setTerm('test1');
        $term1->setSearchTarget($lowPriorityTarget);

        $term2 = new SearchTermEntity();
        $term2->setId('term-2');
        $term2->setTerm('test2');
        $term2->setSearchTarget($highPriorityTarget);

        $searchResult = $this->createMock(EntitySearchResult::class);
        $searchResult->method('getIterator')->willReturn(new \ArrayIterator([$term1, $term2]));

        $this->termRepository
            ->method('search')
            ->willReturn($searchResult);

        $result = $this->loader->loadMatchingTargets($searchTerm, $salesChannelContext);

        static::assertInstanceOf(SearchTargetCollection::class, $result);
        static::assertCount(2, $result);

        $firstTarget = $result->first();
        static::assertNotNull($firstTarget);
        static::assertEquals(20, $firstTarget->getPriority());
    }

    public function testLoadMatchingTargetsWithNoMatches(): void
    {
        $searchTerm = 'nonexistent';
        $salesChannelContext = $this->createMock(SalesChannelContext::class);
        $salesChannelContext->method('getSalesChannelId')->willReturn('sales-channel-id');

        $context = $this->createMock(Context::class);
        $context->method('getLanguageId')->willReturn('language-id');
        $salesChannelContext->method('getContext')->willReturn($context);

        $searchResult = $this->createMock(EntitySearchResult::class);
        $searchResult->method('getIterator')->willReturn(new \ArrayIterator([]));

        $this->termRepository
            ->expects(static::once())
            ->method('search')
            ->willReturn($searchResult);

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

        $searchResult = $this->createMock(EntitySearchResult::class);
        $searchResult->method('getIterator')->willReturn(new \ArrayIterator([]));

        $this->termRepository
            ->expects(static::once())
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
