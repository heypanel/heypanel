<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\RateLimiter;

use HeyPanel\Core\Framework\RateLimiter\Exception\RateLimitExceededException;

class RateLimiter
{
    final public const OAUTH = 'oauth';
    final public const USER_RECOVERY = 'user_recovery';
    final public const LOGIN_ROUTE = 'login';
    final public const RESET_PASSWORD = 'reset_password';

    /**
     * @var array<string, RateLimiterFactory>
     */
    private array $factories;

    public function reset(string $route, string $key): void
    {
        $this->getFactory($route)->create($key)->reset();
    }

    public function ensureAccepted(string $route, string $key): void
    {
        $limiter = $this->getFactory($route)->create($key)->consume();

        if (!$limiter->isAccepted()) {
            throw new RateLimitExceededException($limiter->getRetryAfter()->getTimestamp());
        }
    }

    public function registerLimiterFactory(string $route, RateLimiterFactory $factory): void
    {
        $this->factories[$route] = $factory;
    }

    private function getFactory(string $route): RateLimiterFactory
    {
        $factory = $this->factories[$route] ?? null;

        if ($factory === null) {
            throw new \RuntimeException('Invalid factory.');
        }

        return $factory;
    }
}
