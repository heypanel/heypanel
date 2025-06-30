<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Adapter\Twig\Extension;

use HeyPanel\Core\Framework\Adapter\Twig\TemplateFinder;
use HeyPanel\Core\Framework\Adapter\Twig\TemplateScopeDetector;
use HeyPanel\Core\Framework\Adapter\Twig\TokenParser\ExtendsTokenParser;
use HeyPanel\Core\Framework\Adapter\Twig\TokenParser\IncludeTokenParser;
use HeyPanel\Core\Framework\Adapter\Twig\TokenParser\ReturnNodeTokenParser;
use Twig\Extension\AbstractExtension;
use Twig\TokenParser\TokenParserInterface;

class NodeExtension extends AbstractExtension
{
    /**
     * @internal
     *
     * @deprecated tag:v6.8.0  - replace TemplateFinder with TemplateFinderInterface
     */
    public function __construct(
        private readonly TemplateFinder $finder,
        private readonly TemplateScopeDetector $templateScopeDetector,
    ) {
    }

    /**
     * @return TokenParserInterface[]
     */
    public function getTokenParsers(): array
    {
        return [
            new ExtendsTokenParser($this->finder, $this->templateScopeDetector),
            new IncludeTokenParser($this->finder),
            new ReturnNodeTokenParser(),
        ];
    }

    public function getFinder(): TemplateFinder
    {
        return $this->finder;
    }
}
