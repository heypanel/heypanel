<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Seo\Validation;

use HeyPanel\Core\Content\Seo\SeoUrlRoute\SeoUrlRouteConfig;
use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\Validation\DataValidationDefinition;

interface SeoUrlDataValidationFactoryInterface
{
    public function buildValidation(Context $context, SeoUrlRouteConfig $config): DataValidationDefinition;
}
