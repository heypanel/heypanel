<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Event;

use HeyPanel\Core\System\Customer\Event\CustomerBeforeLoginEvent;
use HeyPanel\Core\System\Customer\Event\CustomerLoginEvent;
use HeyPanel\Core\System\Customer\Event\CustomerLogoutEvent;

class BusinessEventRegistry
{
    /**
     * @var list<class-string>
     */
    private array $classes = [
        CustomerBeforeLoginEvent::class,
        CustomerLoginEvent::class,
        CustomerLogoutEvent::class,
    ];

    /**
     * @param list<class-string> $classes
     */
    public function addClasses(array $classes): void
    {
        /** @var list<class-string> */
        $classes = array_unique(array_merge($this->classes, $classes));

        $this->classes = $classes;
    }

    /**
     * @return list<class-string>
     */
    public function getClasses(): array
    {
        return $this->classes;
    }
}
