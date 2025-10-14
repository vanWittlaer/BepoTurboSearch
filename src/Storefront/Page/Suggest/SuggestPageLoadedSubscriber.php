<?php declare(strict_types=1);

namespace Bepo\TurboSuggest\Storefront\Page\Suggest;

use Bepo\TurboSuggest\Service\SearchTargetLoader;
use Shopware\Storefront\Page\Suggest\SuggestPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class SuggestPageLoadedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private SearchTargetLoader $searchTargetLoader
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

        $targets = $this->searchTargetLoader->loadMatchingTargets(
            (string) $searchTerm,
            $event->getSalesChannelContext()
        );

        $event->getPage()->addExtension('turboSuggestTargets', $targets);
    }
}
