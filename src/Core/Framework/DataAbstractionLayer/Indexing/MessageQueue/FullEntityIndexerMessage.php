<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Indexing\MessageQueue;

use HeyPanel\Core\Framework\MessageQueue\AsyncMessageInterface;

class FullEntityIndexerMessage implements AsyncMessageInterface
{
    /**
     * @internal
     *
     * @param list<string> $skip
     * @param list<string> $only
     */
    public function __construct(
        protected array $skip = [],
        protected array $only = []
    ) {
    }

    /**
     * @return list<string>
     */
    public function getSkip(): array
    {
        return $this->skip;
    }

    /**
     * @return list<string>
     */
    public function getOnly(): array
    {
        return $this->only;
    }
}
