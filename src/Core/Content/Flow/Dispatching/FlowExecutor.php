<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Flow\Dispatching;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DBALException;
use HeyPanel\Core\Content\Flow\Dispatching\Action\FlowAction;
use HeyPanel\Core\Content\Flow\Dispatching\Struct\ActionSequence;
use HeyPanel\Core\Content\Flow\Dispatching\Struct\Flow;
use HeyPanel\Core\Content\Flow\Dispatching\Struct\IfSequence;
use HeyPanel\Core\Content\Flow\Dispatching\Struct\Sequence;
use HeyPanel\Core\Content\Flow\Exception\ExecuteSequenceException;
use HeyPanel\Core\Content\Flow\Extension\FlowExecutorExtension;
use HeyPanel\Core\Content\Flow\FlowException;
use HeyPanel\Core\Framework\Extensions\ExtensionDispatcher;
use Psr\Log\LoggerInterface;

/**
 * @internal not intended for decoration or replacement
 *
 * @phpstan-import-type FlowHolder from AbstractFlowLoader
 */
class FlowExecutor
{
    /**
     * @var array<string, FlowAction>
     */
    private readonly array $actions;

    /**
     * @param FlowAction[] $actions
     */
    public function __construct(
        private readonly Connection $connection,
        private readonly ExtensionDispatcher $extensions,
        private readonly LoggerInterface $logger,
        $actions
    ) {
        $this->actions = $actions instanceof \Traversable ? iterator_to_array($actions) : $actions;
    }

    /**
     * @param array<FlowHolder> $flowHolders
     */
    public function executeFlows(array $flowHolders, StorableFlow $event): void
    {
        foreach ($flowHolders as $flowHolder) {
            $flow = $flowHolder['payload'];
            $id = $flowHolder['id'];
            $name = $flowHolder['name'];

            try {
                $this->extensions->publish(
                    name: FlowExecutorExtension::NAME,
                    extension: new FlowExecutorExtension($flow, $event),
                    function: $this->_execute(...)
                );
            } catch (ExecuteSequenceException $e) {
                $this->logger->error(
                    "Could not execute flow with error message:\n"
                    . 'Flow name: ' . $name . "\n"
                    . 'Flow id: ' . $id . "\n"
                    . 'Sequence id: ' . $e->getSequenceId() . "\n"
                    . $e->getMessage() . "\n"
                    . 'Error Code: ' . $e->getCode() . "\n",
                    ['exception' => $e]
                );
            } catch (\Throwable $e) {
                $this->logger->error(
                    "Could not execute flow with error message:\n"
                    . 'Flow name: ' . $name . "\n"
                    . 'Flow id: ' . $id . "\n"
                    . $e->getMessage() . "\n"
                    . 'Error Code: ' . $e->getCode() . "\n",
                    ['exception' => $e]
                );
            }
        }
    }

    public function execute(Flow $flow, StorableFlow $event): void
    {
        $this->extensions->publish(
            name: FlowExecutorExtension::NAME,
            extension: new FlowExecutorExtension($flow, $event),
            function: $this->_execute(...)
        );
    }

    public function executeSequence(?Sequence $sequence, StorableFlow $event): void
    {
        if ($sequence === null) {
            return;
        }

        $event->getFlowState()->currentSequence = $sequence;

        if ($sequence instanceof IfSequence) {
            $this->executeIf($sequence, $event);

            return;
        }

        if ($sequence instanceof ActionSequence) {
            $this->executeAction($sequence, $event);
        }
    }

    public function executeAction(ActionSequence $sequence, StorableFlow $event): void
    {
        $actionName = $sequence->action;
        if (!$actionName) {
            return;
        }

        if ($event->getFlowState()->stop) {
            return;
        }

        $event->setConfig($sequence->config);

        $this->callHandle($sequence, $event);

        if ($event->getFlowState()->delayed) {
            return;
        }

        $event->getFlowState()->currentSequence = $sequence;

        /** @var ActionSequence $nextAction */
        $nextAction = $sequence->nextAction;
        if ($nextAction !== null) {
            $this->executeAction($nextAction, $event);
        }
    }

    public function executeIf(IfSequence $sequence, StorableFlow $event): void
    {
        $this->executeSequence($sequence->falseCase, $event);
    }

    private function _execute(Flow $flow, StorableFlow $event): void
    {
        $state = new FlowState();

        $event->setFlowState($state);
        $state->flowId = $flow->getId();
        foreach ($flow->getSequences() as $sequence) {
            $state->delayed = false;

            try {
                $this->executeSequence($sequence, $event);
            } catch (\Exception $e) {
                throw ExecuteSequenceException::sequenceExecutionFailed(
                    $sequence->flowId,
                    $sequence->sequenceId,
                    $e->getMessage(),
                    $e->getCode(),
                    $e
                );
            }

            if ($state->stop) {
                return;
            }
        }
    }

    private function callHandle(ActionSequence $sequence, StorableFlow $event): void
    {
        $action = $this->actions[$sequence->action] ?? null;

        if (!$action instanceof FlowAction) {
            return;
        }

        if (!$action instanceof TransactionalAction) {
            $action->handleFlow($event);

            return;
        }

        $this->connection->beginTransaction();

        try {
            $action->handleFlow($event);
        } catch (\Throwable $e) {
            $this->connection->rollBack();

            throw FlowException::transactionFailed($e);
        }

        try {
            $this->connection->commit();
        } catch (DBALException $e) {
            $this->connection->rollBack();

            throw FlowException::transactionFailed($e);
        }
    }
}
