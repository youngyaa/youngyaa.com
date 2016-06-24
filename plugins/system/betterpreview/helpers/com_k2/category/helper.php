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

include_once JPATH_SITE . '/components/com_k2/helpers/route.php';

class HelperBetterPreviewHelperK2Category extends PlgSystemBetterPreviewHelper
{
	function getK2Category()
	{
		if (!JFactory::getApplication()->input->get('cid'))
		{
			return;
		}

		$item = $this->getItem(
			JFactory::getApplication()->input->get('cid'),
			'k2_categories',
			array(),
			array('type' => 'K2_CATEGORY')
		);

		$item->url = K2HelperRoute::getCategoryRoute($item->id, $item->parent);

		return $item;
	}

	function getK2CategoryParents($item)
	{
		if (empty($item)
			|| !JFactory::getApplication()->input->get('cid')
		)
		{
			return false;
		}

		$parents = $this->getParents(
			$item,
			'k2_categories',
			array(),
			array('type' => 'K2_CATEGORY')
		);

		foreach ($parents as &$parent)
		{
			$parent->url = K2HelperRoute::getCategoryRoute($parent->id);
		}

		return $parents;
	}
}
