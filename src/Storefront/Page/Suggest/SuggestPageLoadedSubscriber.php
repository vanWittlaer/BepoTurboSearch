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

        $targets = $this->searchTargetLoader->loadMatchingTargets($searchTermString, $context);
        $event->getPage()->addExtension('turboSuggestTargets', $targets);

        $likeMatchedProducts = $this->productLikeMatchLoader->loadMatchingProducts($searchTermString, $context);
        $event->getPage()->addExtension('turboLikeMatchedProducts', $likeMatchedProducts);
    }
}
