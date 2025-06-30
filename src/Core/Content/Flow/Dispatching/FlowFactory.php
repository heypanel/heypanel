<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Flow\Dispatching;

use HeyPanel\Core\Content\Flow\Dispatching\Storer\FlowStorer;
use HeyPanel\Core\Framework\Api\Context\SystemSource;
use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\Event\FlowEventAware;

/**
 * @internal
 */
class FlowFactory
{
    /**
     * @param iterable<FlowStorer> $storer
     */
    public function __construct(private iterable $storer)
    {
    }

    public function create(FlowEventAware $event): StorableFlow
    {
        $stored = $this->getStored($event);

        return $this->restore($event->getName(), $event->getContext(), $stored);
    }

    /**
     * @param array<string, mixed> $stored
     * @param array<string, mixed> $data
     */
    public function restore(string $name, Context $context, array $stored = [], array $data = []): StorableFlow
    {
        $systemContext = new Context(
            new SystemSource(),
            $context->getRuleIds(),
            $context->getCurrencyId(),
            $context->getLanguageIdChain(),
            $context->getVersionId(),
            $context->getCurrencyFactor(),
            $context->considerInheritance(),
            $context->getTaxState(),
            $context->getRounding(),
        );
        $systemContext->setExtensions($context->getExtensions());

        $flow = new StorableFlow($name, $systemContext, $stored, $data);

        foreach ($this->storer as $storer) {
            $storer->restore($flow);
        }

        return $flow;
    }

    /**
     * @return array<string, mixed>
     */
    private function getStored(FlowEventAware $event): array
    {
        $stored = [];
        foreach ($this->storer as $storer) {
            $stored = $storer->store($event, $stored);
        }

        return $stored;
    }
}
