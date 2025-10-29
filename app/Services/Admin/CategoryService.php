<?php

namespace App\Services\Admin;

use App\Models\Content\Category;

class CategoryService
{
    /**
     * Generate path for category based on alias and parent.
     */
    public function generatePath(string $alias, int $parentId): string
    {
        if ($parentId == 0) {
            return $alias;
        }

        $parent = Category::find($parentId);
        return $parent ? $parent->path . '/' . $alias : $alias;
    }

    /**
     * Calculate level for category based on parent.
     */
    public function calculateLevel(int $parentId): int
    {
        if ($parentId == 0) {
            return 0;
        }

        $parent = Category::find($parentId);
        return $parent ? $parent->level + 1 : 0;
    }

    /**
     * Calculate left value for nested set model.
     */
    public function calculateLft(int $parentId): int
    {
        if ($parentId == 0) {
            $maxRgt = Category::where('parent_id', 0)->max('rgt');
            return $maxRgt ? $maxRgt + 1 : 1;
        }

        $parent = Category::find($parentId);
        if (!$parent) {
            return 1;
        }

        return $parent->rgt;
    }

    /**
     * Update right values for nested set model.
     */
    public function updateRgtValues(int $lft): void
    {
        Category::where('rgt', '>=', $lft)->increment('rgt', 2);
        Category::where('lft', '>', $lft)->increment('lft', 2);
    }

    /**
     * Prepare category data for creation.
     */
    public function prepareCategoryData(array $data): array
    {
        $data['path'] = $this->generatePath($data['alias'], $data['parent_id']);
        $data['level'] = $this->calculateLevel($data['parent_id']);
        $data['lft'] = $this->calculateLft($data['parent_id']);
        $data['rgt'] = $data['lft'] + 1;
        $data['created_time'] = now();
        $data['modified_time'] = now();

        return $data;
    }
}
