<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Validation;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\Event\GenericEvent;
use HeyPanel\Core\Framework\Event\HeyPanelEvent;
use HeyPanel\Core\Framework\Validation\DataBag\DataBag;
use Symfony\Contracts\EventDispatcher\Event;

class BuildValidationEvent extends Event implements HeyPanelEvent, GenericEvent
{
    public function __construct(
        private readonly DataValidationDefinition $definition,
        private readonly DataBag $data,
        private readonly Context $context
    ) {
    }

    public function getName(): string
    {
        return 'framework.validation.' . $this->definition->getName();
    }

    public function getDefinition(): DataValidationDefinition
    {
        return $this->definition;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function getData(): DataBag
    {
        return $this->data;
    }
}
