<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Channel\Aggregate\ChannelTypeTranslation;

use HeyPanel\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use HeyPanel\Core\Framework\DataAbstractionLayer\TranslationEntity;
use HeyPanel\Core\System\Channel\Aggregate\ChannelType\ChannelTypeEntity;

class ChannelTypeTranslationEntity extends TranslationEntity
{
    use EntityCustomFieldsTrait;

    protected string $channelTypeId;

    protected ?string $name = null;

    protected ?string $description = null;

    protected ?string $descriptionLong = null;

    protected ?ChannelTypeEntity $channelType = null;

    public function getChannelTypeId(): string
    {
        return $this->channelTypeId;
    }

    public function setChannelTypeId(string $channelTypeId): void
    {
        $this->channelTypeId = $channelTypeId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getDescriptionLong(): ?string
    {
        return $this->descriptionLong;
    }

    public function setDescriptionLong(?string $descriptionLong): void
    {
        $this->descriptionLong = $descriptionLong;
    }

    public function getChannelType(): ?ChannelTypeEntity
    {
        return $this->channelType;
    }

    public function setChannelType(?ChannelTypeEntity $channelType): void
    {
        $this->channelType = $channelType;
    }
}
