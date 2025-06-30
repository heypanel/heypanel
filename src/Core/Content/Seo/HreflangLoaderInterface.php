<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Seo;

use HeyPanel\Core\Content\Seo\Hreflang\HreflangCollection;

interface HreflangLoaderInterface
{
    public function load(HreflangLoaderParameter $parameter): HreflangCollection;
}
