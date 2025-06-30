<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Contract;

interface RuleIdAware
{
    public function getAvailabilityRuleId(): ?string;
}
