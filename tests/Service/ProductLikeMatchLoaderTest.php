<?php declare(strict_types=1);

namespace Bepo\TurboSuggest\Tests\Service;

use Bepo\TurboSuggest\Service\ProductLikeMatchLoader;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Grouping\FieldGrouping;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepository;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class ProductLikeMatchLoaderTest extends TestCase
{
    private SalesChannelRepository $productRepository;
    private SystemConfigService $systemConfigService;
    private ProductLikeMatchLoader $loader;

    protected function setUp(): void
    {
        $this->productRepository = $this->createMock(SalesChannelRepository::class);
        $this->systemConfigService = $this->createMock(SystemConfigService::class);
        $this->loader = new ProductLikeMatchLoader(
            $this->productRepository,
            $this->systemConfigService
        );
    }

    public function testLoadMatchingProductsWhenDisabled(): void
    {
        $context = $this->createSalesChannelContext();

        $this->systemConfigService
            ->method('getBool')
            ->with('BepoTurboSuggest.config.likeMatchEnabled', 'sales-channel-id')
            ->willReturn(false);

        $this->productRepository
            ->expects(static::never())
            ->method('search');

        $result = $this->loader->loadMatchingProducts('test', $context);

        static::assertInstanceOf(ProductCollection::class, $result);
        static::assertCount(0, $result);
    }

    public function testLoadMatchingProductsWithSearchTermBelowMinLength(): void
    {
        $context = $this->createSalesChannelContext();

        $this->systemConfigService
            ->method('getBool')
            ->willReturn(true);

        $this->systemConfigService
            ->method('getInt')
            ->willReturnCallback(function (string $key) {
                if (str_contains($key, 'likeMatchMinLength')) {
                    return 5;
                }
                return 10;
            });

        $this->productRepository
            ->expects(static::never())
            ->method('search');

        $result = $this->loader->loadMatchingProducts('ab', $context);

        static::assertInstanceOf(ProductCollection::class, $result);
        static::assertCount(0, $result);
    }

    public function testLoadMatchingProductsWithValidSearchTerm(): void
    {
        $searchTerm = 'ABC123';
        $context = $this->createSalesChannelContext();

        $this->systemConfigService
            ->method('getBool')
            ->willReturn(true);

        $this->systemConfigService
            ->method('getInt')
            ->willReturnCallback(function (string $key) {
                if (str_contains($key, 'likeMatchMinLength')) {
                    return 3;
                }
                return 10;
            });

        $product = new ProductEntity();
        $product->setId('product-id');
        $product->setProductNumber('ABC123-456');
        $products = new ProductCollection([$product]);

        $searchResult = $this->createMock(EntitySearchResult::class);
        $searchResult->method('getEntities')->willReturn($products);

        $this->productRepository
            ->expects(static::once())
            ->method('search')
            ->with(
                static::callback(function (Criteria $criteria) use ($searchTerm) {
                    $filters = $criteria->getFilters();
                    $hasContainsFilter = false;
                    $hasNotFilter = false;

                    foreach ($filters as $filter) {
                        if ($filter instanceof ContainsFilter
                            && $filter->getField() === 'productNumber'
                            && $filter->getValue() === $searchTerm
                        ) {
                            $hasContainsFilter = true;
                        }
                        if ($filter instanceof NotFilter) {
                            $hasNotFilter = true;
                        }
                    }

                    return $hasContainsFilter && $hasNotFilter;
                }),
                $context
            )
            ->willReturn($searchResult);

        $result = $this->loader->loadMatchingProducts($searchTerm, $context);

        static::assertInstanceOf(ProductCollection::class, $result);
        static::assertCount(1, $result);
        static::assertEquals('ABC123-456', $result->first()->getProductNumber());
    }

    public function testLoadMatchingProductsAppliesLimit(): void
    {
        $context = $this->createSalesChannelContext();

        $this->systemConfigService
            ->method('getBool')
            ->willReturn(true);

        $this->systemConfigService
            ->method('getInt')
            ->willReturnCallback(function (string $key) {
                if (str_contains($key, 'likeMatchMinLength')) {
                    return 2;
                }
                if (str_contains($key, 'likeMatchLimit')) {
                    return 5;
                }
                return 10;
            });

        $searchResult = $this->createMock(EntitySearchResult::class);
        $searchResult->method('getEntities')->willReturn(new ProductCollection());

        $this->productRepository
            ->expects(static::once())
            ->method('search')
            ->with(
                static::callback(function (Criteria $criteria) {
                    return $criteria->getLimit() === 5;
                }),
                $context
            )
            ->willReturn($searchResult);

        $this->loader->loadMatchingProducts('test', $context);
    }

    public function testLoadMatchingProductsAppliesDefaultLimitWhenZero(): void
    {
        $context = $this->createSalesChannelContext();

        $this->systemConfigService
            ->method('getBool')
            ->willReturn(true);

        $this->systemConfigService
            ->method('getInt')
            ->willReturnCallback(function (string $key) {
                if (str_contains($key, 'likeMatchMinLength')) {
                    return 2;
                }
                if (str_contains($key, 'likeMatchLimit')) {
                    return 0;
                }
                return 10;
            });

        $searchResult = $this->createMock(EntitySearchResult::class);
        $searchResult->method('getEntities')->willReturn(new ProductCollection());

        $this->productRepository
            ->expects(static::once())
            ->method('search')
            ->with(
                static::callback(function (Criteria $criteria) {
                    return $criteria->getLimit() === 10;
                }),
                $context
            )
            ->willReturn($searchResult);

        $this->loader->loadMatchingProducts('test', $context);
    }

    public function testLoadMatchingProductsAppliesGroupingAndSorting(): void
    {
        $context = $this->createSalesChannelContext();

        $this->systemConfigService
            ->method('getBool')
            ->willReturn(true);

        $this->systemConfigService
            ->method('getInt')
            ->willReturn(2);

        $searchResult = $this->createMock(EntitySearchResult::class);
        $searchResult->method('getEntities')->willReturn(new ProductCollection());

        $this->productRepository
            ->expects(static::once())
            ->method('search')
            ->with(
                static::callback(function (Criteria $criteria) {
                    $groupFields = $criteria->getGroupFields();
                    $sortings = $criteria->getSorting();

                    $hasDisplayGroupGrouping = false;
                    foreach ($groupFields as $groupField) {
                        if ($groupField instanceof FieldGrouping && $groupField->getField() === 'displayGroup') {
                            $hasDisplayGroupGrouping = true;
                            break;
                        }
                    }

                    $hasProductNumberSorting = false;
                    foreach ($sortings as $sorting) {
                        if ($sorting instanceof FieldSorting
                            && $sorting->getField() === 'productNumber'
                            && $sorting->getDirection() === FieldSorting::ASCENDING
                        ) {
                            $hasProductNumberSorting = true;
                            break;
                        }
                    }

                    return $hasDisplayGroupGrouping && $hasProductNumberSorting;
                }),
                $context
            )
            ->willReturn($searchResult);

        $this->loader->loadMatchingProducts('test', $context);
    }

    public function testLoadMatchingProductsIncludesCoverAssociation(): void
    {
        $context = $this->createSalesChannelContext();

        $this->systemConfigService
            ->method('getBool')
            ->willReturn(true);

        $this->systemConfigService
            ->method('getInt')
            ->willReturn(2);

        $searchResult = $this->createMock(EntitySearchResult::class);
        $searchResult->method('getEntities')->willReturn(new ProductCollection());

        $this->productRepository
            ->expects(static::once())
            ->method('search')
            ->with(
                static::callback(function (Criteria $criteria) {
                    return $criteria->hasAssociation('cover');
                }),
                $context
            )
            ->willReturn($searchResult);

        $this->loader->loadMatchingProducts('test', $context);
    }

    public function testLoadMatchingProductsWithNoMatches(): void
    {
        $context = $this->createSalesChannelContext();

        $this->systemConfigService
            ->method('getBool')
            ->willReturn(true);

        $this->systemConfigService
            ->method('getInt')
            ->willReturn(2);

        $searchResult = $this->createMock(EntitySearchResult::class);
        $searchResult->method('getEntities')->willReturn(new ProductCollection());

        $this->productRepository
            ->method('search')
            ->willReturn($searchResult);

        $result = $this->loader->loadMatchingProducts('nonexistent', $context);

        static::assertInstanceOf(ProductCollection::class, $result);
        static::assertCount(0, $result);
    }

    private function createSalesChannelContext(): SalesChannelContext
    {
        $context = $this->createMock(SalesChannelContext::class);
        $context->method('getSalesChannelId')->willReturn('sales-channel-id');

        return $context;
    }
}
