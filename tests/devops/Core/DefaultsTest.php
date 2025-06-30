<?php
declare(strict_types=1);

namespace HeyPanel\Tests\DevOps\Core;

use HeyPanel\Core\Defaults;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class DefaultsTest extends TestCase
{
    private const CURRENT_AMOUNT_OF_DEFAULT_CONSTANTS = 8;

    public function testValues(): void
    {
        $defaults = new \ReflectionClass(Defaults::class);
        static::assertCount(self::CURRENT_AMOUNT_OF_DEFAULT_CONSTANTS, $defaults->getConstants(), 'Ensure, that every default value is checked here');

        static::assertSame('2fbb5fe2e29a4d70aa5854ce7ce3e20b', Defaults::LANGUAGE_SYSTEM);
        static::assertSame('0fa91ce3e96a4bc2be4bd9ce752c3425', Defaults::LIVE_VERSION);
        static::assertSame('b7d2554b0ce847cd82f3ac9bd1c0dfca', Defaults::CURRENCY);
        static::assertSame('f183ee5650cf4bdb8a774337575067a6', Defaults::CHANNEL_TYPE_API);
        static::assertSame('8a243080f92e4c719546314b577cf82b', Defaults::CHANNEL_TYPE_FRONTEND);
        static::assertSame('Y-m-d H:i:s.v', Defaults::STORAGE_DATE_TIME_FORMAT);
        static::assertSame('Y-m-d', Defaults::STORAGE_DATE_FORMAT);
    }
}
