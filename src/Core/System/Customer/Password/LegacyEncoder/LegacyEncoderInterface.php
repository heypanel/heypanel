<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Customer\Password\LegacyEncoder;

interface LegacyEncoderInterface
{
    public function getName(): string;

    public function isPasswordValid(string $password, string $hash): bool;
}
