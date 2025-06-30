<?php declare(strict_types=1);

namespace HeyPanel\Frontend\Theme;

use HeyPanel\Frontend\Theme\Exception\InvalidThemeConfigException;

class ThemeConfigFieldFactory
{
    public function create(string $name, array $configFieldArray): ThemeConfigField
    {
        $configField = new ThemeConfigField();
        $configField->setName($name);

        foreach ($configFieldArray as $key => $value) {
            $setter = 'set' . $key;
            if (!method_exists($configField, $setter)) {
                throw new InvalidThemeConfigException($key);
            }
            $configField->$setter($value); /* @phpstan-ignore-line */
        }

        return $configField;
    }
}
