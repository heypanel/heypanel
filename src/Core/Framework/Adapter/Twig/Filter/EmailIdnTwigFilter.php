<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Adapter\Twig\Filter;

use HeyPanel\Core\System\Customer\Service\EmailIdnConverter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @internal
 */
class EmailIdnTwigFilter extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('decodeIdnEmail', [EmailIdnConverter::class, 'decode']),
            new TwigFilter('encodeIdnEmail', [EmailIdnConverter::class, 'encode']),
        ];
    }
}
