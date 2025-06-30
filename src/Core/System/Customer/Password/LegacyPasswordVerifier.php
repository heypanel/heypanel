<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Customer\Password;

use HeyPanel\Core\System\Customer\CustomerEntity;
use HeyPanel\Core\System\Customer\CustomerException;
use HeyPanel\Core\System\Customer\Password\LegacyEncoder\LegacyEncoderInterface;

class LegacyPasswordVerifier
{
    /**
     * @internal
     *
     * @param LegacyEncoderInterface[] $encoder
     */
    public function __construct(private readonly iterable $encoder)
    {
    }

    public function verify(string $password, CustomerEntity $customer): bool
    {
        if (!$customer->getLegacyEncoder() || !$customer->getLegacyPassword()) {
            throw CustomerException::badCredentials();
        }

        foreach ($this->encoder as $encoder) {
            if ($encoder->getName() !== $customer->getLegacyEncoder()) {
                continue;
            }

            return $encoder->isPasswordValid($password, $customer->getLegacyPassword());
        }

        throw CustomerException::legacyPasswordEncoderNotFound($customer->getLegacyEncoder());
    }
}
