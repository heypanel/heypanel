<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Cms\Channel\Struct;

use HeyPanel\Core\Content\Media\MediaEntity;
use HeyPanel\Core\Framework\Struct\Struct;

class ImageSliderItemStruct extends Struct
{
    protected ?string $url = null;

    protected ?bool $newTab = null;

    protected ?MediaEntity $media = null;

    public function getMedia(): ?MediaEntity
    {
        return $this->media;
    }

    public function setMedia(?MediaEntity $media): void
    {
        $this->media = $media;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    public function getNewTab(): ?bool
    {
        return $this->newTab;
    }

    public function setNewTab(?bool $newTab): void
    {
        $this->newTab = $newTab;
    }

    public function getApiAlias(): string
    {
        return 'cms_image_slider_item';
    }
}
