<?php declare(strict_types=1);

namespace Bepo\TurboSuggest\Core\Content\SearchTarget\Aggregate\SearchTargetTranslation;

use Bepo\TurboSuggest\Core\Content\SearchTarget\SearchTargetEntity;
use Shopware\Core\Framework\DataAbstractionLayer\TranslationEntity;

class SearchTargetTranslationEntity extends TranslationEntity
{
    protected string $searchTargetId;

    protected ?string $title = null;

    protected ?string $teaserText = null;

    protected ?SearchTargetEntity $searchTarget = null;

    public function getSearchTargetId(): string
    {
        return $this->searchTargetId;
    }

    public function setSearchTargetId(string $searchTargetId): void
    {
        $this->searchTargetId = $searchTargetId;
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

    public function getSearchTarget(): ?SearchTargetEntity
    {
        return $this->searchTarget;
    }

    public function setSearchTarget(?SearchTargetEntity $searchTarget): void
    {
        $this->searchTarget = $searchTarget;
    }
}
