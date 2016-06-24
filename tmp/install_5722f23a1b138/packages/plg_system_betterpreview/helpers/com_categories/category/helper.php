<?php
/**
 * @package         Better Preview
 * @version         5.0.1
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2016 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

include_once JPATH_SITE . '/components/com_content/helpers/route.php';

class HelperBetterPreviewHelperCategoriesCategory extends PlgSystemBetterPreviewHelper
{
	function getCategory()
	{
		if (JFactory::getApplication()->input->get('extension', 'com_content') != 'com_content'
			|| !JFactory::getApplication()->input->get('id')
		)
		{
			return;
		}

		$item = parent::getItem(
			JFactory::getApplication()->input->get('id'),
			'categories',
			array('name' => 'title', 'parent' => 'parent_id', 'language' => 'language'),
			array('type' => 'JCATEGORY'),
			1
		);

		$item->url = ContentHelperRoute::getCategoryRoute($item->id, $item->language);

		return $item;
	}

	function getCategoryParents($item)
	{
		if (empty($item)
			|| JFactory::getApplication()->input->get('extension', 'com_content') != 'com_content'
			|| !JFactory::getApplication()->input->get('id')
		)
		{
			return false;
		}

		$parents = parent::getParents(
			$item,
			'categories',
			array('name' => 'title', 'parent' => 'parent_id', 'language' => 'language'),
			array('type' => 'JCATEGORY'),
			1
		);

		foreach ($parents as &$parent)
		{
			$parent->url = ContentHelperRoute::getCategoryRoute($parent->id, $item->language);
		}

		return $parents;
	}
}
