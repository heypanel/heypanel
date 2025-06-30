<?php declare(strict_types=1);

namespace HeyPanel\Core\System\NumberRange\Aggregate\NumberRangeChannel;

use HeyPanel\Core\Framework\DataAbstractionLayer\Entity;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use HeyPanel\Core\System\NumberRange\Aggregate\NumberRangeType\NumberRangeTypeEntity;
use HeyPanel\Core\System\NumberRange\NumberRangeEntity;

class NumberRangeChannelEntity extends Entity
{
    use EntityIdTrait;

    protected string $numberRangeId;

    protected string $numberRangeTypeId;

    protected ?NumberRangeEntity $numberRange = null;

    protected ?NumberRangeTypeEntity $numberRangeType = null;

    public function getNumberRangeId(): string
    {
        return $this->numberRangeId;
    }

    public function setNumberRangeId(string $numberRangeId): void
    {
        $this->numberRangeId = $numberRangeId;
    }

    public function getNumberRangeTypeId(): string
    {
        return $this->numberRangeTypeId;
    }

    public function setNumberRangeTypeId(string $numberRangeTypeId): void
    {
        $this->numberRangeTypeId = $numberRangeTypeId;
    }

    public function getNumberRange(): ?NumberRangeEntity
    {
        return $this->numberRange;
    }

    public function setNumberRange(NumberRangeEntity $numberRange): void
    {
        $this->numberRange = $numberRange;
    }

    public function getNumberRangeType(): ?NumberRangeTypeEntity
    {
        return $this->numberRangeType;
    }

    public function setNumberRangeType(NumberRangeTypeEntity $numberRangeType): void
    {
        $this->numberRangeType = $numberRangeType;
    }
}
