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
 * HTML View class for Membership Pro component
 *
 * @static
 * @package        Joomla
 * @subpackage     Membership Pro
 */
class OSMembershipViewStateHtml extends MPFViewItem
{

	protected function prepareView()
	{
		parent::prepareView();

		$db = JFactory::getDbo();
		$db->setQuery(
			$db->getQuery(true)
				->select('id, name')
				->from('#__osmembership_countries')
				->where('published = 1')
				->order('name')
		);
		$options = array();
		$options[] = JHtml::_('select.option', 0, ' - ' . JText::_('OSM_SELECT_COUNTRY') . ' - ', 'id', 'name');
		$options = array_merge($options, $db->loadObjectList());

		$this->lists['country_id'] = JHtml::_('select.genericlist', $options, 'country_id', ' class="inputbox"', 'id', 'name', $this->item->country_id);
	}
}