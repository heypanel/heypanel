<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Flow\Dispatching;

use HeyPanel\Core\Framework\Event\FlowEventAware;

class BufferedFlowQueue
{
    /**
     * @var array<FlowEventAware>
     */
    private array $bufferedFlows = [];

    public function queueFlow(FlowEventAware $flowEvent): void
    {
        $this->bufferedFlows[] = $flowEvent;
    }

    /**
     * @return array<FlowEventAware>
     */
    public function dequeueFlows(): array
    {
        $flows = $this->bufferedFlows;
        $this->bufferedFlows = [];

        return $flows;
    }

    public function isEmpty(): bool
    {
        return empty($this->bufferedFlows);
    }
}
