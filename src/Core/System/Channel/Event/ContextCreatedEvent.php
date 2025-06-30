<?php

declare(strict_types=1);

namespace HeyPanel\Core\System\Channel\Event;

use HeyPanel\Core\Framework\Context;

/**
 * @codeCoverageIgnore
 *
 * This event can be used to react to the creation of a new context.
 * It must be used very carefully, as it practically effects every part of HeyPanel.
 */
final class ContextCreatedEvent
{
    public function __construct(
        public Context $context,
    ) {
    }
}
