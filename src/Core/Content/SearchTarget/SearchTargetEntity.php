<?php declare(strict_types=1);

namespace Bepo\TurboSuggest\Core\Content\SearchTarget;

use Bepo\TurboSuggest\Core\Content\SearchTarget\Aggregate\SearchTargetTranslation\SearchTargetTranslationCollection;
use Bepo\TurboSuggest\Core\Content\SearchTerm\SearchTermCollection;
use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Content\LandingPage\LandingPageEntity;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;

class SearchTargetEntity extends Entity
{
    use EntityIdTrait;

    protected ?string $categoryId = null;

    protected ?string $landingPageId = null;

    protected ?string $mediaId = null;

    protected string $salesChannelId;

    protected int $priority = 0;

    protected ?CategoryEntity $category = null;

    protected ?LandingPageEntity $landingPage = null;

    protected ?MediaEntity $media = null;

    protected ?SalesChannelEntity $salesChannel = null;

    protected ?SearchTermCollection $searchTerms = null;

    protected ?SearchTargetTranslationCollection $translations = null;

    protected ?string $title = null;

    protected ?string $teaserText = null;

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

    public function getTeaserText(): ?string
    {
        return $this->teaserText;
    }

    public function setTeaserText(?string $teaserText): void
    {
        $this->teaserText = $teaserText;
    }

    public function getCategoryId(): ?string
    {
        return $this->categoryId;
    }

    public function setCategoryId(?string $categoryId): void
    {
        $this->categoryId = $categoryId;
    }

    public function getLandingPageId(): ?string
    {
        return $this->landingPageId;
    }

    public function setLandingPageId(?string $landingPageId): void
    {
        $this->landingPageId = $landingPageId;
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

    public function getLandingPage(): ?LandingPageEntity
    {
        return $this->landingPage;
    }

    public function setLandingPage(?LandingPageEntity $landingPage): void
    {
        $this->landingPage = $landingPage;
    }

    public function getMediaId(): ?string
    {
        return $this->mediaId;
    }

    public function setMediaId(?string $mediaId): void
    {
        $this->mediaId = $mediaId;
    }

    public function getMedia(): ?MediaEntity
    {
        return $this->media;
    }

    public function setMedia(?MediaEntity $media): void
    {
        $this->media = $media;
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
