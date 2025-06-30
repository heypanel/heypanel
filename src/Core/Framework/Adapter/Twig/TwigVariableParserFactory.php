<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Adapter\Twig;

use Twig\Environment;

class TwigVariableParserFactory
{
    public function getParser(Environment $twig): TwigVariableParser
    {
        return new TwigVariableParser($twig);
    }
}
