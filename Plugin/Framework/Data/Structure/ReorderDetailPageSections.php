<?php

namespace MageSuite\ProductDetailsReorder\Plugin\Framework\Data\Structure;

class ReorderDetailPageSections
{

    /**
     * @var \Magento\Framework\View\Layout
     */
    protected $layout;

    /**
     * @var \Magento\Framework\View\ConfigInterface
     */
    protected $viewConfig;

    public function __construct(
        \Magento\Framework\View\Layout $layout,
        \Magento\Framework\View\ConfigInterface $viewConfig
    )
    {
        $this->layout = $layout;
        $this->viewConfig = $viewConfig;
    }

    public function aroundGetGroupChildNames(\Magento\Framework\Data\Structure $subject, \Closure $proceed, $parentId, $groupName)
    {
        $groupChildNames = $proceed($parentId, $groupName);

        $viewConfig = $this->viewConfig->getViewConfig();
        $sortableContainers = $viewConfig->getVarValue('MageSuite_ProductDetailsReorder', 'sortable_containers');
        if (!in_array($parentId, $sortableContainers)) {
            return $groupChildNames;
        }

        $result = [];
        $blocksWithSortOrderExist = false;

        $blocks = $this->layout->getChildBlocks($parentId);
        foreach ($blocks as $block) {
            $blockNameInLayout = $block->getNameInLayout();
            if (!in_array($blockNameInLayout, $groupChildNames)) {
                continue;
            }

            $sortOrder = $block->getSortOrder();
            $result[$blockNameInLayout] = $sortOrder ? (int)$sortOrder : 10;

            if (!is_null($sortOrder)) {
                $blocksWithSortOrderExist = true;
            }
        }

        if ($blocksWithSortOrderExist) {
            asort($result);
            $result = array_keys($result);
        } else {
            $result = $groupChildNames;
        }

        return $result;

    }
}
