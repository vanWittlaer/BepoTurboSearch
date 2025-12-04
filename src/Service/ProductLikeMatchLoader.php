<?php declare(strict_types=1);

namespace Bepo\TurboSuggest\Service;

use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepository;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class ProductLikeMatchLoader
{
    private const CONFIG_PREFIX = 'BepoTurboSuggest.config.';

    public function __construct(
        #[Autowire(service: 'sales_channel.product.repository')]
        private SalesChannelRepository $productRepository,
        private SystemConfigService $systemConfigService
    ) {
    }

    public function loadMatchingProducts(string $searchTerm, SalesChannelContext $context): ProductCollection
    {
        $salesChannelId = $context->getSalesChannelId();

        $enabled = $this->systemConfigService->getBool(
            self::CONFIG_PREFIX . 'likeMatchEnabled',
            $salesChannelId
        );

        if (!$enabled) {
            return new ProductCollection();
        }

        $minLength = $this->systemConfigService->getInt(
            self::CONFIG_PREFIX . 'likeMatchMinLength',
            $salesChannelId
        );

        if (mb_strlen($searchTerm) < $minLength) {
            return new ProductCollection();
        }

        $limit = $this->systemConfigService->getInt(
            self::CONFIG_PREFIX . 'likeMatchLimit',
            $salesChannelId
        ) ?: 10;

        $criteria = new Criteria();
        $criteria->addFilter(new ContainsFilter('productNumber', $searchTerm));
        $criteria->addAssociation('cover.media');
        $criteria->setLimit($limit);

        $mainProductsOnly = $this->systemConfigService->getBool(
            self::CONFIG_PREFIX . 'likeMatchMainProductsOnly',
            $salesChannelId
        );

        if ($mainProductsOnly) {
            $criteria->addFilter(new EqualsFilter('parentId', null));
        }

        /** @var ProductCollection $products */
        $products = $this->productRepository->search($criteria, $context)->getEntities();

        return $products;
    }
}
