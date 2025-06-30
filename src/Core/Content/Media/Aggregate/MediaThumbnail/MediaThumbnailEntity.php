<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Media\Aggregate\MediaThumbnail;

use HeyPanel\Core\Content\Media\MediaEntity;
use HeyPanel\Core\Framework\DataAbstractionLayer\Entity;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class MediaThumbnailEntity extends Entity
{
    use EntityCustomFieldsTrait;
    use EntityIdTrait;

    protected ?string $path = null;

    protected int $width;

    protected int $height;

    protected ?string $url = '';

    protected string $mediaId;

    protected ?MediaEntity $media = null;

    public function getWidth(): int
    {
        return $this->width;
    }

    public function setWidth(int $width): void
    {
        $this->width = $width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function setHeight(int $height): void
    {
        $this->height = $height;
    }

    /**
     * @deprecated tag:v6.8.0 - reason:return-type-change - return type will be nullable and condition will be removed
     */
    public function getUrl(): string
    {
        if ($this->url === null) {
            return '';
        }

        return $this->url;
    }

    /**
     * @deprecated tag:v6.8.0 - reason:parameter-type-extension - parameter $url will be nullable
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getMediaId(): string
    {
        return $this->mediaId;
    }

    public function setMediaId(string $mediaId): void
    {
        $this->mediaId = $mediaId;
    }

    public function getMedia(): ?MediaEntity
    {
        return $this->media;
    }

    public function setMedia(MediaEntity $media): void
    {
        $this->media = $media;
    }

    public function getIdentifier(): string
    {
        return \sprintf('%dx%d', $this->getWidth(), $this->getHeight());
    }

    public function getPath(): string
    {
        return $this->path ?? '';
    }

    public function setPath(?string $path): void
    {
        $this->path = $path;
    }
}
