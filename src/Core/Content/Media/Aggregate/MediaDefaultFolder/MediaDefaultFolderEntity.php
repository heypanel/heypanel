<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Media\Aggregate\MediaDefaultFolder;

use HeyPanel\Core\Content\Media\Aggregate\MediaFolder\MediaFolderEntity;
use HeyPanel\Core\Framework\DataAbstractionLayer\Entity;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class MediaDefaultFolderEntity extends Entity
{
    use EntityCustomFieldsTrait;
    use EntityIdTrait;

    protected string $entity;

    protected ?MediaFolderEntity $folder = null;

    public function getEntity(): string
    {
        return $this->entity;
    }

    public function setEntity(string $entity): void
    {
        $this->entity = $entity;
    }

    public function getFolder(): ?MediaFolderEntity
    {
        return $this->folder;
    }

    public function setFolder(?MediaFolderEntity $folder): void
    {
        $this->folder = $folder;
    }
}
