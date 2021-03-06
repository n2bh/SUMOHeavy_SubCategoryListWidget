<?php
/**
 * SUMOHeavy_SubCategoryListWidget
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.sumoheavy.com/LICENSE.txt
 *
 * @category   SUMOHeavy
 * @package    SUMOHeavy_SubCategoryListWidget
 * @copyright  Copyright 2013 SUMO Heavy Industries (http://www.sumoheavy.com)
 * @license    http://www.sumoheavy.com/LICENSE.txt
 * @author     Sean Kennedy <support@sumoheavy.com>
 */
class SUMOHeavy_SubCategoryListWidget_Block_Category_Widget_List
    extends Mage_Core_Block_Template
    implements Mage_Widget_Block_Interface
{
    /**
     * Category parent path
     *
     * @var
     */
    protected $_scParentPath;

    /**
     * Subcategories collection
     *
     * @var
     */
    protected $_scSubcategories;

    /**
     * List of subcategories
     *
     * @var array
     */
    protected $_scList = array();

    /**
     * Category image height
     *
     * @var int
     */
    protected $_imgH = 135;

    /**
     * Category image width
     *
     * @var int
     */
    protected $_imgW = 135;

    /**
     * Construct
     */
    protected function _construct()
    {
        $this->_init();
        parent::_construct();
    }


    /**
     * Initialize
     */
    protected function _init()
    {
        $this->_scParentPath = $this->getData('category_path');
        $this->_getSubcategories();
        $this->_imgH = Mage::getStoreConfig(
            'sumoheavy_subcategorylistwidget/settings/image_h'
        );
        $this->_imgW = Mage::getStoreConfig(
            'sumoheavy_subcategorylistwidget/settings/image_w'
        );
    }

    /**
     * Load the Parent Category
     */
    protected function _getSubcategories()
    {
        $categoryId = (int)end(explode('/', $this->_scParentPath));
        $this->_scSubcategories = Mage::getModel('catalog/category')
            ->getCategories($categoryId);
    }

    /**
     * Gets a category's image
     *
     * @param $category
     * @return string
     */
    protected function _getCategoryImage($category)
    {
        $src = '';
        // Overriden image
        if ($category->getData('sh_sclw_img')) {
            $src = Mage::getBaseUrl('media')
                . 'catalog/category/'
                . $category->getData('sh_sclw_img');

        // Category image
        } elseif ($category->getImage()) {
            $src = $category->getImageUrl();
        }
        return $src;
    }

    /**
     * Setup the category array value
     *
     * @param $category
     * @return mixed
     */
    protected function _generateCategoryArray($category)
    {
        $category = Mage::getModel('catalog/category')
            ->load($category->getEntityId());
        return array(
            'id'    => $category->getId(),
            'img'   => $this->_getCategoryImage($category),
            'img_h' => $this->_imgH,
            'img_w' => $this->_imgW,
            'name'  => $category->getName(),
            'url'   => $category->getUrl()
            );
    }

    /**
     * Produces subcategory list based on
     * a provided category id
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (empty($this->_scParentPath)) {
            // No category ID found
            return $this->__("No category ID could be found.");
        } else {
            if ($this->_scSubcategories->count() < 1) {
                // No subcategories found
                return $this->__("No subcategories were found.");
            } else {
                foreach ($this->_scSubcategories as $category) {
                    $this->_scList[] = $this
                        ->_generateCategoryArray($category);
                }
                $this->assign('subcategories', $this->_scList);
            }
        }
        return parent::_toHtml();
    }

}

