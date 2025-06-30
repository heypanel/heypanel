<?php declare(strict_types=1);

namespace HeyPanel\Core\System\DependencyInjection\CompilerPass;

use HeyPanel\Core\System\DependencyInjection\DependencyInjectionException;
use HeyPanel\Core\System\NumberRange\ValueGenerator\Pattern\IncrementStorage\IncrementRedisStorage;
use HeyPanel\Core\System\NumberRange\ValueGenerator\Pattern\IncrementStorage\IncrementSqlStorage;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class NumberRangeIncrementerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $storage = $container->getParameter('heypanel.number_range.increment_storage');

        switch ($storage) {
            case 'mysql':
                $container->removeDefinition('heypanel.number_range.redis');
                $container->removeDefinition(IncrementRedisStorage::class);
                break;
            case 'redis':
                if ($container->getParameter('heypanel.number_range.config.connection') === null) {
                    throw DependencyInjectionException::redisNotConfiguredForNumberRangeIncrementer();
                }

                $container->removeDefinition(IncrementSqlStorage::class);
                break;
        }
    }
}
