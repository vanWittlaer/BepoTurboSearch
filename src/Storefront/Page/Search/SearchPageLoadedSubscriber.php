<?php declare(strict_types=1);

namespace Bepo\TurboSuggest\Storefront\Page\Search;

use Bepo\TurboSuggest\Service\ProductLikeMatchLoader;
use Shopware\Storefront\Page\Search\SearchPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class SearchPageLoadedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ProductLikeMatchLoader $productLikeMatchLoader
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SearchPageLoadedEvent::class => 'onSearchPageLoaded',
        ];
    }

    public function onSearchPageLoaded(SearchPageLoadedEvent $event): void
    {
        $page = $event->getPage();
        $searchTerm = $page->getSearchTerm();

        if (empty($searchTerm)) {
            return;
        }

        $context = $event->getSalesChannelContext();

        $likeMatchedProducts = $this->productLikeMatchLoader->loadMatchingProducts($searchTerm, $context);

        if ($likeMatchedProducts->count() === 0) {
            return;
        }

        $listing = $page->getListing();

        // Collect displayGroups from LIKE-matched products
        $likeMatchedDisplayGroups = [];
        foreach ($likeMatchedProducts as $product) {
            $displayGroup = $product->getDisplayGroup();
            if ($displayGroup !== null) {
                $likeMatchedDisplayGroups[$displayGroup] = true;
            }
        }

        // Remove products from listing that share displayGroup with LIKE-matched products
        foreach ($listing->getEntities() as $product) {
            $displayGroup = $product->getDisplayGroup();
            if ($displayGroup !== null && isset($likeMatchedDisplayGroups[$displayGroup])) {
                $listing->remove($product->getUniqueIdentifier());
                $listing->getEntities()->remove($product->getUniqueIdentifier());
            }
        }

        // Add LIKE-matched products
        foreach ($likeMatchedProducts as $product) {
            $listing->add($product);
        }
    }
}
