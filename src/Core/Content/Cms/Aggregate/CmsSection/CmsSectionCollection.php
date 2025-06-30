<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Cms\Aggregate\CmsSection;

use HeyPanel\Core\Content\Cms\Aggregate\CmsBlock\CmsBlockCollection;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<CmsSectionEntity>
 */
class CmsSectionCollection extends EntityCollection
{
    public function getBlocks(): CmsBlockCollection
    {
        $blocks = new CmsBlockCollection();

        /** @var CmsSectionEntity $section */
        foreach ($this->elements as $section) {
            if (!$section->getBlocks()) {
                continue;
            }

            $blocks->merge($section->getBlocks());
        }

        return $blocks;
    }

    public function getApiAlias(): string
    {
        return 'cms_page_section_collection';
    }

    /**
     * @experimental stableVersion:v6.8.0 feature:SPATIAL_BASES
     */
    public function hasBlockWithType(string $type): bool
    {
        return $this->firstWhere(fn (CmsSectionEntity $section) => $section->getBlocks()?->hasBlockWithType($type)) !== null;
    }

    protected function getExpectedClass(): string
    {
        return CmsSectionEntity::class;
    }
}
