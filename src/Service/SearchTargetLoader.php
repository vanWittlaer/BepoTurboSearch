<?php declare(strict_types=1);

namespace Bepo\TurboSuggest\Service;

use Bepo\TurboSuggest\Core\Content\SearchTarget\SearchTargetCollection;
use Bepo\TurboSuggest\Core\Content\SearchTarget\SearchTargetEntity;
use Bepo\TurboSuggest\Core\Content\SearchTerm\SearchTermEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\PrefixFilter;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class SearchTargetLoader
{
    public function __construct(
        private readonly EntityRepository $bepoTurboSuggestTermRepository,
    ) {
    }

    public function loadMatchingTargets(
        string $searchTerm,
        SalesChannelContext $context
    ): SearchTargetCollection {
        if (empty($searchTerm)) {
            return new SearchTargetCollection();
        }

        // Priority 1: Exact match
        $exactCriteria = new Criteria();
        $exactCriteria->addFilter(new EqualsFilter('term', $searchTerm));
        $exactCriteria->addFilter(new EqualsFilter('languageId', $context->getContext()->getLanguageId()));
        $exactCriteria->addFilter(new EqualsFilter('active', true));
        $exactCriteria->addFilter(new EqualsFilter('searchTarget.salesChannelId', $context->getSalesChannelId()));

        $exactMatches = $this->bepoTurboSuggestTermRepository->search($exactCriteria, $context->getContext());

        if ($exactMatches->count() > 0) {
            $targets = new SearchTargetCollection();
            foreach ($exactMatches as $exactMatch) {
                if ($exactMatch instanceof SearchTermEntity) {
                    $target = $exactMatch->getSearchTarget();
                    if ($target instanceof SearchTargetEntity) {
                        $targets->add($target);
                    }
                }
            }
            if ($targets->count() > 0) {
                $targets->sort(function (SearchTargetEntity $a, SearchTargetEntity $b) {
                    return $b->getPriority() <=> $a->getPriority();
                });
                return $targets;
            }
        }

        // Priority 2: Prefix match
        $criteria = new Criteria();
        $criteria->addFilter(new PrefixFilter('term', $searchTerm));
        $criteria->addFilter(new EqualsFilter('languageId', $context->getContext()->getLanguageId()));
        $criteria->addFilter(new EqualsFilter('active', true));
        $criteria->addFilter(new EqualsFilter('searchTarget.salesChannelId', $context->getSalesChannelId()));

        $result = $this->bepoTurboSuggestTermRepository->search($criteria, $context->getContext());

        // Sort by term length (shortest first)
        $terms = $result->getElements();
        uasort($terms, static function (mixed $a, mixed $b): int {
            if (!$a instanceof SearchTermEntity) {
                return 0;
            }
            if (!$b instanceof SearchTermEntity) {
                return 0;
            }
            $termA = $a->getTerm() ?? '';
            $termB = $b->getTerm() ?? '';
            $lenA = \strlen($termA);
            $lenB = \strlen($termB);
            if ($lenA === $lenB) {
                return strcmp($termA, $termB);
            }

            return $lenA <=> $lenB;
        });

        // Collect all targets with the shortest matching term length
        $targets = new SearchTargetCollection();
        $shortestLength = null;

        foreach ($terms as $term) {
            if (!$term instanceof SearchTermEntity) {
                continue;
            }

            $termLength = \strlen($term->getTerm() ?? '');

            // Set the shortest length on first iteration
            if ($shortestLength === null) {
                $shortestLength = $termLength;
            }

            // Only collect targets with the shortest term length
            if ($termLength === $shortestLength) {
                $target = $term->getSearchTarget();
                if ($target instanceof SearchTargetEntity) {
                    $targets->add($target);
                }
            } else {
                // Since terms are sorted by length, we can break here
                break;
            }
        }

        $targets->sort(function (SearchTargetEntity $a, SearchTargetEntity $b) {
            return $b->getPriority() <=> $a->getPriority();
        });

        return $targets;
    }
}
