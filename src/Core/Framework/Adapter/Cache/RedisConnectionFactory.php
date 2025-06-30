<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Adapter\Cache;

use HeyPanel\Core\Framework\Util\Hasher;
use Predis\ClientInterface;
use RedisArray as RedisTypeHint;
use Relay\Relay;
use Symfony\Component\Cache\Adapter\RedisAdapter;

/**
 * Used to create new Redis connection based on a connection dsn.
 * Existing connections are reused if there are any.
 *
 * @final
 *
 * @phpstan-type RedisTypeHint \Redis|\RedisArray|\RedisCluster|ClientInterface|Relay
 */
class RedisConnectionFactory
{
    /**
     * This static variable is not reset on purpose, as we may reuse existing redis connections over multiple requests
     *
     * @var array<string, RedisTypeHint>
     */
    private static array $connections = [];

    /**
     * @internal
     */
    public function __construct(private readonly ?string $prefix = null)
    {
    }

    /**
     * Don't type hint the native return types, as symfony might change them in the future
     *
     * @param array<string, mixed> $options
     *
     * @return RedisTypeHint
     */
    public function create(string $dsn, array $options = [])
    {
        $configHash = Hasher::hash($options);
        $key = $dsn . $configHash . $this->prefix;

        if (!isset(self::$connections[$key]) || (
            \method_exists(self::$connections[$key], 'isConnected') && self::$connections[$key]->isConnected() === false
        )) {
            /** @var RedisTypeHint $redis */
            $redis = RedisAdapter::createConnection($dsn, $options);

            if ($this->prefix && \method_exists($redis, 'setOption')) {
                $redis->setOption(\Redis::OPT_PREFIX, $this->prefix);
            }

            self::$connections[$key] = $redis;
        }

        return self::$connections[$key];
    }
}
