<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Seo\Event;

use Symfony\Contracts\EventDispatcher\Event;

class SeoUrlUpdateEvent extends Event
{
    public function __construct(protected array $seoUrls)
    {
    }

    public function getSeoUrls(): array
    {
        return $this->seoUrls;
    }
}
