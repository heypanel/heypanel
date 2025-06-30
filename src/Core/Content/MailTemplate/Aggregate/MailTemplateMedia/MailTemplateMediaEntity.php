<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\MailTemplate\Aggregate\MailTemplateMedia;

use HeyPanel\Core\Content\MailTemplate\MailTemplateEntity;
use HeyPanel\Core\Content\Media\MediaEntity;
use HeyPanel\Core\Framework\DataAbstractionLayer\Entity;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class MailTemplateMediaEntity extends Entity
{
    use EntityIdTrait;

    protected string $mailTemplateId;

    protected ?string $languageId = null;

    protected string $mediaId;

    protected ?MediaEntity $media = null;

    protected ?MailTemplateEntity $mailTemplate = null;

    public function getMailTemplateId(): string
    {
        return $this->mailTemplateId;
    }

    public function setMailTemplateId(string $mailTemplateId): void
    {
        $this->mailTemplateId = $mailTemplateId;
    }

    public function getLanguageId(): ?string
    {
        return $this->languageId;
    }

    public function setLanguageId(?string $languageId): void
    {
        $this->languageId = $languageId;
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

    public function getMailTemplate(): ?MailTemplateEntity
    {
        return $this->mailTemplate;
    }

    public function setMailTemplate(MailTemplateEntity $mailTemplate): void
    {
        $this->mailTemplate = $mailTemplate;
    }
}
