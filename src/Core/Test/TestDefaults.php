<?php declare(strict_types=1);

namespace HeyPanel\Core\Test;

/**
 * @internal
 * This class contains some defaults for test case
 */
class TestDefaults
{
    // use pre-hashed password, so we don't need to hash in every test, password is `heypanel`
    final public const HASHED_PASSWORD = '$2y$10$8U9/94ulFaD.iiLNeWguWOwf0Z1mqxCXONtsf9gsAyGFliLAG4HjC';
}
