<?php declare(strict_types=1);

namespace HeyPanel\Frontend\Theme;

use HeyPanel\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<ThemeEntity>
 */
class ThemeCollection extends EntityCollection
{
    public function getByTechnicalName(string $technicalName): ?ThemeEntity
    {
        return $this->filter(fn (ThemeEntity $theme) => $theme->getTechnicalName() === $technicalName)->first();
    }

    protected function getExpectedClass(): string
    {
        return ThemeEntity::class;
    }
}
