<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Event;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityWriteResult;
use HeyPanel\Core\Framework\DataAbstractionLayer\Write\EntityExistence;
use HeyPanel\Core\Framework\Event\GenericEvent;
use HeyPanel\Core\Framework\Event\NestedEvent;
use HeyPanel\Core\Framework\Event\NestedEventCollection;

class EntityWrittenEvent extends NestedEvent implements GenericEvent
{
    protected ?array $ids = null;

    protected NestedEventCollection $events;

    protected ?array $payloads = null;

    /**
     * @var EntityExistence[]
     */
    protected ?array $existences = null;

    protected string $name;

    /**
     * @param EntityWriteResult[] $writeResults
     */
    public function __construct(
        protected string $entityName,
        protected array $writeResults,
        protected Context $context,
        protected array $errors = []
    ) {
        $this->events = new NestedEventCollection();
        $this->name = $this->entityName . '.written';
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getIds(): array
    {
        if ($this->ids === null) {
            $this->ids = [];
            foreach ($this->writeResults as $entityWriteResult) {
                $this->ids[] = $entityWriteResult->getPrimaryKey();
            }
        }

        return $this->ids;
    }

    public function getEntityName(): string
    {
        return $this->entityName;
    }

    public function hasErrors(): bool
    {
        return \count($this->errors) > 0;
    }

    public function addEvent(NestedEvent $event): void
    {
        $this->events->add($event);
    }

    public function getPayloads(): array
    {
        if ($this->payloads === null) {
            $this->payloads = [];
            foreach ($this->writeResults as $entityWriteResult) {
                $this->payloads[] = $entityWriteResult->getPayload();
            }
        }

        return $this->payloads;
    }

    /**
     * @return EntityExistence[]
     */
    public function getExistences(): array
    {
        if ($this->existences === null) {
            $this->existences = [];
            foreach ($this->writeResults as $entityWriteResult) {
                if ($entityWriteResult->getExistence()) {
                    $this->existences[] = $entityWriteResult->getExistence();
                }
            }
        }

        return $this->existences;
    }

    /**
     * @return EntityWriteResult[]
     */
    public function getWriteResults(): array
    {
        return $this->writeResults;
    }
}
