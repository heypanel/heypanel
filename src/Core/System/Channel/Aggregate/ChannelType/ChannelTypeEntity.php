<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Channel\Aggregate\ChannelType;

use HeyPanel\Core\Framework\DataAbstractionLayer\Entity;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use HeyPanel\Core\System\Channel\Aggregate\ChannelTypeTranslation\ChannelTypeTranslationCollection;
use HeyPanel\Core\System\Channel\ChannelCollection;

class ChannelTypeEntity extends Entity
{
    use EntityCustomFieldsTrait;
    use EntityIdTrait;

    protected ?string $name = null;

    protected ?string $description = null;

    protected ?string $descriptionLong = null;

    protected ?string $iconName = null;

    protected ?ChannelCollection $channels = null;

    protected ?ChannelTypeTranslationCollection $translations = null;

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

    public function getIconName(): ?string
    {
        return $this->iconName;
    }

    public function setIconName(?string $iconName): void
    {
        $this->iconName = $iconName;
    }

    public function getChannels(): ?ChannelCollection
    {
        return $this->channels;
    }

    public function setChannels(ChannelCollection $channels): void
    {
        $this->channels = $channels;
    }

    public function getTranslations(): ?ChannelTypeTranslationCollection
    {
        return $this->translations;
    }

    public function setTranslations(ChannelTypeTranslationCollection $translations): void
    {
        $this->translations = $translations;
    }
}
