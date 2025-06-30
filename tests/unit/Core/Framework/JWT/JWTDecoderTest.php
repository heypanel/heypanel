<?php declare(strict_types=1);

namespace HeyPanel\Tests\Unit\Core\Framework\JWT;

use HeyPanel\Core\Framework\JWT\JWTDecoder;
use HeyPanel\Core\Framework\JWT\JWTException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(JWTDecoder::class)]
class JWTDecoderTest extends TestCase
{
    private JWTDecoder $decoder;

    protected function setUp(): void
    {
        $this->decoder = new JWTDecoder();
    }

    public function testDecodeWithValidToken(): void
    {
        $claims = $this->decoder->decode($this->getJwt());
        static::assertSame([
            ['identifier' => 'Purchase1', 'nextBookingDate' => '2099-12-13 11:44:31', 'quantity' => 1, 'sub' => 'example.com'],
            ['identifier' => 'Purchase2', 'nextBookingDate' => '2099-12-13 11:44:31', 'quantity' => 1, 'sub' => 'example.com'],
        ], $claims);
    }

    public function testDecodeWithInvalidTokenThrowsException(): void
    {
        $this->expectException(JWTException::class);
        $this->expectExceptionMessage('Invalid JWT: Error while decoding from Base64Url, invalid base64 characters detected');
        $this->decoder->decode('invalid.jwt.token');
    }

    private function getJwt(): string
    {
        $jwt = \file_get_contents(__DIR__ . '/_fixtures/valid-jwt.txt');
        static::assertIsString($jwt);
        $jwt = \trim($jwt);

        return $jwt;
    }
}
