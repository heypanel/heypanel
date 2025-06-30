<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Store\Struct;

use HeyPanel\Core\Framework\Struct\Struct;

/**
 * @codeCoverageIgnore
 */
class PluginCategoryStruct extends Struct
{
    protected string $name;

    protected string $label;

    public function __construct(
        string $name,
        string $label
    ) {
        $this->name = $name;
        $this->label = $label;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getApiAlias(): string
    {
        return 'store_category';
    }
}
