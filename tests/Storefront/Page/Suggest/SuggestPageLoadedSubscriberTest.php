<?php declare(strict_types=1);

namespace Bepo\TurboSuggest\Tests\Storefront\Page\Suggest;

use Bepo\TurboSuggest\Core\Content\SearchTarget\SearchTargetCollection;
use Bepo\TurboSuggest\Core\Content\SearchTarget\SearchTargetEntity;
use Bepo\TurboSuggest\Service\SearchTargetLoader;
use Bepo\TurboSuggest\Storefront\Page\Suggest\SuggestPageLoadedSubscriber;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Page\Suggest\SuggestPage;
use Shopware\Storefront\Page\Suggest\SuggestPageLoadedEvent;
use Symfony\Component\HttpFoundation\Request;

class SuggestPageLoadedSubscriberTest extends TestCase
{
    private SearchTargetLoader $searchTargetLoader;
    private SuggestPageLoadedSubscriber $subscriber;

    protected function setUp(): void
    {
        $this->searchTargetLoader = $this->createMock(SearchTargetLoader::class);
        $this->subscriber = new SuggestPageLoadedSubscriber($this->searchTargetLoader);
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

        $page = $this->createMock(SuggestPage::class);
        $page->expects(static::once())
            ->method('addExtension')
            ->with('turboSuggestTargets', static::isInstanceOf(SearchTargetCollection::class));

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

        $this->subscriber->onSuggestPageLoaded($event);
    }

    public function testOnSuggestPageLoadedWithEmptyCollection(): void
    {
        $searchTerm = 'nomatch';
        $request = new Request(['search' => $searchTerm]);

        $salesChannelContext = $this->createMock(SalesChannelContext::class);

        $page = $this->createMock(SuggestPage::class);
        $page->expects(static::once())
            ->method('addExtension')
            ->with('turboSuggestTargets', static::isInstanceOf(SearchTargetCollection::class));

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

        $this->subscriber->onSuggestPageLoaded($event);
    }
}
