<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Contract;

interface IdAware
{
    public function getId(): string;
}
