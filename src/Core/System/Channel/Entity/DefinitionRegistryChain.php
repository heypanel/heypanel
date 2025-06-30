<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Channel\Entity;

use HeyPanel\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityRepository;
use HeyPanel\Core\Framework\DataAbstractionLayer\Exception\DefinitionNotFoundException;
use HeyPanel\Core\System\Channel\Exception\ChannelRepositoryNotFoundException;

/**
 * @internal
 */
class DefinitionRegistryChain
{
    public function __construct(
        private readonly DefinitionInstanceRegistry $core,
        private readonly ChannelDefinitionInstanceRegistry $channel
    ) {
    }

    public function get(string $class): EntityDefinition
    {
        if ($this->channel->has($class)) {
            return $this->channel->get($class);
        }

        return $this->core->get($class);
    }

    public function getRepository(string $entity): EntityRepository|ChannelRepository
    {
        try {
            return $this->channel->getChannelRepository($entity);
        } catch (ChannelRepositoryNotFoundException) {
            return $this->core->getRepository($entity);
        }
    }

    public function getByEntityName(string $type): EntityDefinition
    {
        try {
            return $this->channel->getByEntityName($type);
        } catch (DefinitionNotFoundException) {
            return $this->core->getByEntityName($type);
        }
    }

    public function has(string $type): bool
    {
        return $this->channel->has($type) || $this->core->has($type);
    }
}
