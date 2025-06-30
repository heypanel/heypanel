<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Event;

use HeyPanel\Core\Framework\Context;

class EntityDeletedEvent extends EntityWrittenEvent
{
    public function __construct(
        string $entityName,
        array $writeResult,
        Context $context,
        array $errors = []
    ) {
        parent::__construct($entityName, $writeResult, $context, $errors);

        $this->name = $entityName . '.deleted';
    }
}
