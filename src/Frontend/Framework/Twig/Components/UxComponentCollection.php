<?php declare(strict_types=1);

namespace HeyPanel\Frontend\Framework\Twig\Components;

use HeyPanel\Core\Framework\Struct\Collection;

/**
 * @extends Collection<UxComponent>
 */
class UxComponentCollection extends Collection
{
    public function __construct(iterable $elements = [])
    {
        parent::__construct();

        foreach ($elements as $element) {
            $this->validateType($element);

            $this->set($element->getName(), $element);
        }
    }

    public function add($element): void
    {
        $this->validateType($element);
        $this->set($element->getName(), $element);
    }
}
