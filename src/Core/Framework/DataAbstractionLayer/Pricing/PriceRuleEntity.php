<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Pricing;

use HeyPanel\Core\Framework\DataAbstractionLayer\Contract\IdAware;
use HeyPanel\Core\Framework\DataAbstractionLayer\Entity;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class PriceRuleEntity extends Entity implements IdAware
{
    use EntityIdTrait;

    protected string $ruleId;

    protected PriceCollection $price;

    public function getRuleId(): string
    {
        return $this->ruleId;
    }

    public function setRuleId(string $ruleId): void
    {
        $this->ruleId = $ruleId;
    }

    public function getPrice(): PriceCollection
    {
        return $this->price;
    }

    public function setPrice(PriceCollection $price): void
    {
        $this->price = $price;
    }
}
