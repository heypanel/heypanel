<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Flow\Events;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Criteria;
use HeyPanel\Core\Framework\Event\GenericEvent;
use HeyPanel\Core\Framework\Event\HeyPanelEvent;
use Symfony\Contracts\EventDispatcher\Event;

class BeforeLoadStorableFlowDataEvent extends Event implements HeyPanelEvent, GenericEvent
{
    public function __construct(
        private readonly string $entityName,
        private readonly Criteria $criteria,
        private readonly Context $context,
    ) {
    }

    public function getName(): string
    {
        return 'flow.storer.' . $this->entityName . '.criteria.event';
    }

    public function getCriteria(): Criteria
    {
        return $this->criteria;
    }

    public function getEntityName(): string
    {
        return $this->entityName;
    }

    public function getContext(): Context
    {
        return $this->context;
    }
}
