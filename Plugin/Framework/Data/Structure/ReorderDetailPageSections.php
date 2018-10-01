<?php

namespace MageSuite\ProductDetailsReorder\Plugin\Framework\Data\Structure;

class ReorderDetailPageSections
{

    /**
     * @var \Magento\Framework\View\Layout
     */
    protected $layout;

    public function __construct(\Magento\Framework\View\Layout $layout)
    {
        $this->layout = $layout;
    }

    public function aroundGetGroupChildNames(\Magento\Framework\Data\Structure $subject, \Closure $proceed, $parentId, $groupName)
    {
        if ($parentId != 'product.info.details' or $groupName != 'detailed_info') {
            return $proceed($parentId, $groupName);
        }

        $result = [];
        $blocksWithSortOrderExist = false;

        $blocks = $this->layout->getChildBlocks($parentId);
        foreach ($blocks as $block) {
            $sortOrder = $block->getSortOrder();
            $result[$block->getNameInLayout()] = $sortOrder ? (int)$sortOrder : 10;

            if (is_null($sortOrder)) {
                $blocksWithSortOrderExist = true;
            }
        }

        if ($blocksWithSortOrderExist) {
            asort($result);
            $result = array_keys($result);
        } else {
            $result = $proceed($parentId, $groupName);
        }

        return $result;

    }
}