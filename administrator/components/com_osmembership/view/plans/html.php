<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die();

/**
 * HTML View class for OS Membership Component
 *
 * @static
 * @package        Joomla
 * @subpackage     Membership Pro
 */
class OSMembershipViewPlansHtml extends MPFViewList
{

	protected function prepareView()
	{
		parent::prepareView();

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->clear();
		$query->select('COUNT(*)')
			->from('#__osmembership_categories')
			->where('published = 1');
		$db->setQuery($query);
		$categories = $db->loadObjectList();

		if (count($categories))
		{
			$this->lists['filter_category_id'] = OSMembershipHelperHtml::buildCategoryDropdown($this->state->filter_category_id, 'filter_category_id', 'onchange="submit();"');
		}

		// Check to see whether we will show thumbnail column
		$query->clear();
		$query->select('COUNT(*)')
			->from('#__osmembership_plans')
			->where('thumb != ""');
		$db->setQuery($query);
		$this->showThumbnail = (int) $db->loadResult();

		// Check to see whether we should show category column
		$query->clear();
		$query->select('COUNT(*)')
			->from('#__osmembership_plans')
			->where('category_id > 0');
		$db->setQuery($query);
		$this->showCategory = (int) $db->loadResult();
	}
}