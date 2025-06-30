<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Flow\Api;

use HeyPanel\Core\Content\Flow\Dispatching\Action\FlowAction;
use HeyPanel\Core\Content\Flow\Dispatching\DelayableAction;
use HeyPanel\Core\Content\Flow\Events\FlowActionCollectorEvent;
use HeyPanel\Core\Framework\Context;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class FlowActionCollector
{
    /**
     * @internal
     *
     * @param iterable<FlowAction> $actions
     */
    public function __construct(
        protected iterable $actions,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function collect(Context $context): FlowActionCollectorResponse
    {
        $result = new FlowActionCollectorResponse();

        foreach ($this->actions as $service) {
            if (!$service instanceof FlowAction) {
                continue;
            }

            $definition = $this->define($service);

            if (!$result->has($definition->getName())) {
                $result->set($definition->getName(), $definition);
            }
        }

        $this->eventDispatcher->dispatch(new FlowActionCollectorEvent($result, $context));

        return $result;
    }

    private function define(FlowAction $service): FlowActionDefinition
    {
        $requirementsName = [];
        foreach ($service->requirements() as $requirement) {
            $className = explode('\\', $requirement);
            $requirementsName[] = lcfirst(end($className));
        }

        $delayable = false;
        if ($service instanceof DelayableAction) {
            $delayable = true;
        }

        return new FlowActionDefinition(
            $service->getName(),
            $requirementsName,
            $delayable
        );
    }
}
