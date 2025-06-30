<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\ImportExport\Processing\Mapping;

use HeyPanel\Core\Framework\Struct\Struct;

class UpdateBy extends Struct
{
    public function __construct(
        protected string $entityName,
        protected ?string $mappedKey = null
    ) {
    }

    public function getEntityName(): string
    {
        return $this->entityName;
    }

    public function getMappedKey(): ?string
    {
        return $this->mappedKey;
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['entityName'])) {
            throw new \InvalidArgumentException('entityName is required in mapping');
        }

        $mapping = new self($data['entityName']);
        $mapping->assign($data);

        return $mapping;
    }
}
