<?php declare(strict_types=1);

namespace HeyPanel\Core\System\NumberRange\ValueGenerator;

use HeyPanel\Core\Framework\Context;

interface NumberRangeValueGeneratorInterface
{
    /**
     * generates a new Value while taking Care of States, Events and Connectors
     */
    public function getValue(string $type, Context $context, ?string $channelId, bool $preview = false): string;

    /**
     * generates a preview for a given pattern and start
     */
    public function previewPattern(string $definition, ?string $pattern, int $start): string;
}
