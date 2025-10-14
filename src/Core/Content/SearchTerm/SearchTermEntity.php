<?php declare(strict_types=1);

namespace Bepo\TurboSuggest\Core\Content\SearchTerm;

use Bepo\TurboSuggest\Core\Content\SearchTarget\SearchTargetEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\System\Language\LanguageEntity;

class SearchTermEntity extends Entity
{
    use EntityIdTrait;

    protected string $searchTargetId;

    protected string $term;

    protected string $languageId;

    protected bool $active = true;

    protected ?SearchTargetEntity $searchTarget = null;

    protected ?LanguageEntity $language = null;

    public function getSearchTargetId(): string
    {
        return $this->searchTargetId;
    }

    public function setSearchTargetId(string $searchTargetId): void
    {
        $this->searchTargetId = $searchTargetId;
    }

    public function getTerm(): string
    {
        return $this->term;
    }

    public function setTerm(string $term): void
    {
        $this->term = $term;
    }

    public function getLanguageId(): string
    {
        return $this->languageId;
    }

    public function setLanguageId(string $languageId): void
    {
        $this->languageId = $languageId;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getSearchTarget(): ?SearchTargetEntity
    {
        return $this->searchTarget;
    }

    public function setSearchTarget(?SearchTargetEntity $searchTarget): void
    {
        $this->searchTarget = $searchTarget;
    }

    public function getLanguage(): ?LanguageEntity
    {
        return $this->language;
    }

    public function setLanguage(?LanguageEntity $language): void
    {
        $this->language = $language;
    }
}
