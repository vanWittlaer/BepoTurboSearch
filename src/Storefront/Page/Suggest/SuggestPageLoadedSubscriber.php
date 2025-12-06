<?php declare(strict_types=1);

namespace Bepo\TurboSuggest\Storefront\Page\Suggest;

use Bepo\TurboSuggest\Service\ProductLikeMatchLoader;
use Bepo\TurboSuggest\Service\SearchTargetLoader;
use Shopware\Storefront\Page\Suggest\SuggestPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class SuggestPageLoadedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private SearchTargetLoader $searchTargetLoader,
        private ProductLikeMatchLoader $productLikeMatchLoader
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SuggestPageLoadedEvent::class => 'onSuggestPageLoaded',
        ];
    }

    public function onSuggestPageLoaded(SuggestPageLoadedEvent $event): void
    {
        $searchTerm = $event->getRequest()->query->get('search', '');

        if (empty($searchTerm)) {
            return;
        }

        $searchTermString = (string) $searchTerm;
        $context = $event->getSalesChannelContext();
        $page = $event->getPage();

        $targets = $this->searchTargetLoader->loadMatchingTargets($searchTermString, $context);
        $page->addExtension('turboSuggestTargets', $targets);

        $likeMatchedProducts = $this->productLikeMatchLoader->loadMatchingProducts($searchTermString, $context);
        $page->addExtension('turboLikeMatchedProducts', $likeMatchedProducts);

        // Remove LIKE-matched products (and their variants) from default search results to avoid duplicates
        if ($likeMatchedProducts->count() > 0) {
            $searchResult = $page->getSearchResult();
            $likeMatchedIds = $likeMatchedProducts->getIds();

            foreach ($searchResult->getEntities() as $product) {
                $productId = $product->getUniqueIdentifier();
                $parentId = $product->getParentId();

                $isDuplicate = isset($likeMatchedIds[$productId]);
                $isVariantOfLikeMatched = $parentId !== null && isset($likeMatchedIds[$parentId]);

                if ($isDuplicate || $isVariantOfLikeMatched) {
                    $searchResult->remove($productId);
                    $searchResult->getEntities()->remove($productId);
                }
            }
        }
    }
}
