<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Category\Tree;

use HeyPanel\Core\Content\Category\CategoryEntity;
use HeyPanel\Core\Content\Category\CategoryException;
use HeyPanel\Core\Framework\Struct\Struct;

class TreeItem extends Struct
{
    /**
     * @internal public to allow AfterSort::sort()
     */
    public ?string $afterId = null;

    protected ?CategoryEntity $category = null;

    /**
     * @var TreeItem[]
     */
    protected array $children;

    /**
     * @param TreeItem[] $children
     */
    public function __construct(
        ?CategoryEntity $category,
        array $children
    ) {
        $this->category = $category;
        $this->children = $children;
        $this->afterId = $category ? $category->getAfterCategoryId() : null;
    }

    public function getId(): string
    {
        return $this->getCategory()->getId();
    }

    public function setCategory(CategoryEntity $category): void
    {
        $this->category = $category;
        $this->afterId = $category->getAfterCategoryId();
    }

    public function getCategory(): CategoryEntity
    {
        if (!$this->category) {
            throw CategoryException::categoryNotFound('treeItem');
        }

        return $this->category;
    }

    /**
     * @return TreeItem[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function addChildren(TreeItem ...$items): void
    {
        foreach ($items as $item) {
            $this->children[] = $item;
        }
    }

    /**
     * @param TreeItem[] $children
     */
    public function setChildren(array $children): void
    {
        $this->children = $children;
    }

    public function getApiAlias(): string
    {
        return 'category_tree_item';
    }
}
