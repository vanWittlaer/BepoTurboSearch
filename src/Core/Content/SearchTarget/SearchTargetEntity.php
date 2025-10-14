<?php declare(strict_types=1);

namespace Bepo\TurboSuggest\Core\Content\SearchTarget;

use Bepo\TurboSuggest\Core\Content\SearchTarget\Aggregate\SearchTargetTranslation\SearchTargetTranslationCollection;
use Bepo\TurboSuggest\Core\Content\SearchTerm\SearchTermCollection;
use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Content\Cms\CmsPageEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;

class SearchTargetEntity extends Entity
{
    use EntityIdTrait;

    protected ?string $categoryId = null;

    protected ?string $cmsPageId = null;

    protected string $salesChannelId;

    protected int $priority = 0;

    protected ?CategoryEntity $category = null;

    protected ?CmsPageEntity $cmsPage = null;

    protected ?SalesChannelEntity $salesChannel = null;

    protected ?SearchTermCollection $searchTerms = null;

    protected ?SearchTargetTranslationCollection $translations = null;

    protected ?string $title = null;

    public function getTranslations(): ?SearchTargetTranslationCollection
    {
        return $this->translations;
    }

    public function setTranslations(?SearchTargetTranslationCollection $translations): void
    {
        $this->translations = $translations;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getCategoryId(): ?string
    {
        return $this->categoryId;
    }

    public function setCategoryId(?string $categoryId): void
    {
        $this->categoryId = $categoryId;
    }

    public function getCmsPageId(): ?string
    {
        return $this->cmsPageId;
    }

    public function setCmsPageId(?string $cmsPageId): void
    {
        $this->cmsPageId = $cmsPageId;
    }

    public function getSalesChannelId(): string
    {
        return $this->salesChannelId;
    }

    public function setSalesChannelId(string $salesChannelId): void
    {
        $this->salesChannelId = $salesChannelId;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

    public function getCategory(): ?CategoryEntity
    {
        return $this->category;
    }

    public function setCategory(?CategoryEntity $category): void
    {
        $this->category = $category;
    }

    public function getCmsPage(): ?CmsPageEntity
    {
        return $this->cmsPage;
    }

    public function setCmsPage(?CmsPageEntity $cmsPage): void
    {
        $this->cmsPage = $cmsPage;
    }

    public function getSalesChannel(): ?SalesChannelEntity
    {
        return $this->salesChannel;
    }

    public function setSalesChannel(?SalesChannelEntity $salesChannel): void
    {
        $this->salesChannel = $salesChannel;
    }

    public function getSearchTerms(): ?SearchTermCollection
    {
        return $this->searchTerms;
    }

    public function setSearchTerms(?SearchTermCollection $searchTerms): void
    {
        $this->searchTerms = $searchTerms;
    }
}
