<?php declare(strict_types=1);

namespace Bepo\TurboSuggest\Tests\Storefront\Page\Suggest;

use Bepo\TurboSuggest\Core\Content\SearchTarget\SearchTargetCollection;
use Bepo\TurboSuggest\Core\Content\SearchTarget\SearchTargetEntity;
use Bepo\TurboSuggest\Service\ProductLikeMatchLoader;
use Bepo\TurboSuggest\Service\SearchTargetLoader;
use Bepo\TurboSuggest\Storefront\Page\Suggest\SuggestPageLoadedSubscriber;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Content\Product\SalesChannel\Listing\ProductListingResult;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\AggregationResult\AggregationResultCollection;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Page\Suggest\SuggestPage;
use Shopware\Storefront\Page\Suggest\SuggestPageLoadedEvent;
use Symfony\Component\HttpFoundation\Request;

class SuggestPageLoadedSubscriberTest extends TestCase
{
    private SearchTargetLoader $searchTargetLoader;
    private ProductLikeMatchLoader $productLikeMatchLoader;
    private SuggestPageLoadedSubscriber $subscriber;

    protected function setUp(): void
    {
        $this->searchTargetLoader = $this->createMock(SearchTargetLoader::class);
        $this->productLikeMatchLoader = $this->createMock(ProductLikeMatchLoader::class);
        $this->subscriber = new SuggestPageLoadedSubscriber(
            $this->searchTargetLoader,
            $this->productLikeMatchLoader
        );
    }

    public function testGetSubscribedEvents(): void
    {
        $events = SuggestPageLoadedSubscriber::getSubscribedEvents();

        static::assertArrayHasKey(SuggestPageLoadedEvent::class, $events);
        static::assertEquals('onSuggestPageLoaded', $events[SuggestPageLoadedEvent::class]);
    }

    public function testOnSuggestPageLoadedWithSearchTerm(): void
    {
        $searchTerm = 'test';
        $request = new Request(['search' => $searchTerm]);

        $salesChannelContext = $this->createMock(SalesChannelContext::class);

        $searchResult = $this->createSearchResult();

        $page = $this->createMock(SuggestPage::class);
        $page->expects(static::exactly(2))
            ->method('addExtension')
            ->willReturnCallback(function (string $name, $extension) {
                static::assertContains($name, ['turboSuggestTargets', 'turboLikeMatchedProducts']);
            });
        $page->method('getSearchResult')->willReturn($searchResult);

        $event = new SuggestPageLoadedEvent(
            $page,
            $salesChannelContext,
            $request
        );

        $targetCollection = new SearchTargetCollection();
        $target = new SearchTargetEntity();
        $target->setId('test-id');
        $targetCollection->add($target);

        $this->searchTargetLoader
            ->expects(static::once())
            ->method('loadMatchingTargets')
            ->with($searchTerm, $salesChannelContext)
            ->willReturn($targetCollection);

        $this->productLikeMatchLoader
            ->expects(static::once())
            ->method('loadMatchingProducts')
            ->with($searchTerm, $salesChannelContext)
            ->willReturn(new ProductCollection());

        $this->subscriber->onSuggestPageLoaded($event);
    }

    public function testOnSuggestPageLoadedWithoutSearchTerm(): void
    {
        $request = new Request();

        $salesChannelContext = $this->createMock(SalesChannelContext::class);

        $page = $this->createMock(SuggestPage::class);
        $page->expects(static::never())
            ->method('addExtension');

        $event = new SuggestPageLoadedEvent(
            $page,
            $salesChannelContext,
            $request
        );

        $this->searchTargetLoader
            ->expects(static::never())
            ->method('loadMatchingTargets');

        $this->productLikeMatchLoader
            ->expects(static::never())
            ->method('loadMatchingProducts');

        $this->subscriber->onSuggestPageLoaded($event);
    }

    public function testOnSuggestPageLoadedWithEmptySearchTerm(): void
    {
        $request = new Request(['search' => '']);

        $salesChannelContext = $this->createMock(SalesChannelContext::class);

        $page = $this->createMock(SuggestPage::class);
        $page->expects(static::never())
            ->method('addExtension');

        $event = new SuggestPageLoadedEvent(
            $page,
            $salesChannelContext,
            $request
        );

        $this->searchTargetLoader
            ->expects(static::never())
            ->method('loadMatchingTargets');

        $this->productLikeMatchLoader
            ->expects(static::never())
            ->method('loadMatchingProducts');

        $this->subscriber->onSuggestPageLoaded($event);
    }

    public function testOnSuggestPageLoadedWithEmptyCollection(): void
    {
        $searchTerm = 'nomatch';
        $request = new Request(['search' => $searchTerm]);

        $salesChannelContext = $this->createMock(SalesChannelContext::class);

        $searchResult = $this->createSearchResult();

        $page = $this->createMock(SuggestPage::class);
        $page->expects(static::exactly(2))
            ->method('addExtension')
            ->willReturnCallback(function (string $name, $extension) {
                static::assertContains($name, ['turboSuggestTargets', 'turboLikeMatchedProducts']);
            });
        $page->method('getSearchResult')->willReturn($searchResult);

        $event = new SuggestPageLoadedEvent(
            $page,
            $salesChannelContext,
            $request
        );

        $emptyCollection = new SearchTargetCollection();

        $this->searchTargetLoader
            ->expects(static::once())
            ->method('loadMatchingTargets')
            ->with($searchTerm, $salesChannelContext)
            ->willReturn($emptyCollection);

        $this->productLikeMatchLoader
            ->expects(static::once())
            ->method('loadMatchingProducts')
            ->with($searchTerm, $salesChannelContext)
            ->willReturn(new ProductCollection());

        $this->subscriber->onSuggestPageLoaded($event);
    }

    public function testOnSuggestPageLoadedRemovesDuplicateProducts(): void
    {
        $searchTerm = 'test';
        $request = new Request(['search' => $searchTerm]);

        $salesChannelContext = $this->createMock(SalesChannelContext::class);

        $likeMatchedProduct = new ProductEntity();
        $likeMatchedProduct->setId('like-matched-id');
        $likeMatchedProduct->setUniqueIdentifier('like-matched-id');
        $likeMatchedProducts = new ProductCollection([$likeMatchedProduct]);

        $duplicateProduct = new ProductEntity();
        $duplicateProduct->setId('like-matched-id');
        $duplicateProduct->setUniqueIdentifier('like-matched-id');

        $searchResultProducts = new ProductCollection([$duplicateProduct]);
        $searchResult = $this->createSearchResult($searchResultProducts);

        $page = $this->createMock(SuggestPage::class);
        $page->expects(static::exactly(2))->method('addExtension');
        $page->method('getSearchResult')->willReturn($searchResult);

        $event = new SuggestPageLoadedEvent(
            $page,
            $salesChannelContext,
            $request
        );

        $this->searchTargetLoader
            ->method('loadMatchingTargets')
            ->willReturn(new SearchTargetCollection());

        $this->productLikeMatchLoader
            ->method('loadMatchingProducts')
            ->willReturn($likeMatchedProducts);

        $this->subscriber->onSuggestPageLoaded($event);

        static::assertCount(0, $searchResult->getEntities());
    }

    private function createSearchResult(?ProductCollection $products = null): ProductListingResult
    {
        $products = $products ?? new ProductCollection();
        $context = Context::createDefaultContext();

        return new ProductListingResult(
            'product',
            $products->count(),
            $products,
            new AggregationResultCollection(),
            new Criteria(),
            $context
        );
    }
}
