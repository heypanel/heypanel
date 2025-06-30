<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Seo\Validation;

use HeyPanel\Core\Content\Seo\SeoUrlRoute\SeoUrlRouteConfig;
use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\DataAbstractionLayer\Validation\EntityExists;
use HeyPanel\Core\Framework\Validation\DataValidationDefinition;
use HeyPanel\Core\System\Channel\ChannelDefinition;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class SeoUrlValidationFactory implements SeoUrlDataValidationFactoryInterface
{
    public function buildValidation(Context $context, ?SeoUrlRouteConfig $config): DataValidationDefinition
    {
        $definition = new DataValidationDefinition('seo_url.create');

        $this->addConstraints($definition, $config, $context);

        return $definition;
    }

    private function addConstraints(
        DataValidationDefinition $definition,
        ?SeoUrlRouteConfig $routeConfig,
        Context $context
    ): void {
        $fkConstraints = [new NotBlank()];

        if ($routeConfig) {
            $fkConstraints[] = new EntityExists([
                'entity' => $routeConfig->getDefinition()->getEntityName(),
                'context' => $context,
            ]);
        }

        $definition
            ->add('foreignKey', ...$fkConstraints)
            ->add('routeName', new NotBlank(), new Type('string'))
            ->add('pathInfo', new NotBlank(), new Type('string'))
            ->add('seoPathInfo', new NotBlank(), new Type('string'))
            ->add('channelId', new NotBlank(), new EntityExists([
                'entity' => ChannelDefinition::ENTITY_NAME,
                'context' => $context,
            ]));
    }
}
