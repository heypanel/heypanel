<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Category\Channel;

use HeyPanel\Core\Content\Category\CategoryEntity;

class ChannelCategoryEntity extends CategoryEntity
{
    protected ?string $seoUrl = null;

    public function getSeoUrl(): ?string
    {
        return $this->seoUrl;
    }

    public function setSeoUrl(string $seoUrl): void
    {
        $this->seoUrl = $seoUrl;
    }
}
