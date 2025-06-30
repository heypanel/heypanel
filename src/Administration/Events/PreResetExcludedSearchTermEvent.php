<?php declare(strict_types=1);

namespace HeyPanel\Administration\Events;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\Event\HeyPanelEvent;
use Symfony\Contracts\EventDispatcher\Event;

class PreResetExcludedSearchTermEvent extends Event implements HeyPanelEvent
{
    /**
     * @param array<string> $excludedTerms
     */
    public function __construct(
        private string $searchConfigId,
        private array $excludedTerms,
        private Context $context
    ) {
    }

    public function getSearchConfigId(): string
    {
        return $this->searchConfigId;
    }

    public function setSearchConfigId(string $searchConfigId): void
    {
        $this->searchConfigId = $searchConfigId;
    }

    /**
     * @return array<string>
     */
    public function getExcludedTerms(): array
    {
        return $this->excludedTerms;
    }

    /**
     * @param array<string> $excludedTerms
     */
    public function setExcludedTerms(array $excludedTerms): void
    {
        $this->excludedTerms = $excludedTerms;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function setContext(Context $context): void
    {
        $this->context = $context;
    }
}
