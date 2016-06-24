<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * HTML View class for Membership Pro component
 *
 * @static
 * @package        Joomla
 * @subpackage     Membership Pro
 */
class OSMembershipViewGroupmembersHtml extends MPFViewList
{
	protected function prepareView()
	{
		parent::prepareView();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id, title')
			->from('#__osmembership_plans')
			->where('published = 1')
			->where('number_group_members > 0')
			->order('title');
		$db->setQuery($query);

		$options = array();
		$options[] = JHtml::_('select.option', 0, JText::_('OSM_ALL_PLANS'), 'id', 'title');
		$options = array_merge($options, $db->loadObjectList());
		$this->lists['filter_plan_id'] = JHtml::_('select.genericlist', $options, 'filter_plan_id', ' class="inputbox" onchange="submit();" ', 'id', 'title', $this->state->filter_plan_id);

		if ($this->state->filter_plan_id > 0)
		{
			$query->clear();
			$query->select('DISTINCT user_id, CONCAT(first_name, " ", last_name) AS name')
				->from('#__osmembership_subscribers AS a')
				->where('plan_id = '. $this->state->filter_plan_id)
				->where('user_id IN  (SELECT DISTINCT group_admin_id FROM #__osmembership_subscribers WHERE plan_id = '.$this->state->filter_plan_id.' AND group_admin_id > 0)')
				->group('user_id')
				->order('name');
			$db->setQuery($query);
			$groupAdmins = $db->loadObjectList();
			if (count($groupAdmins))
			{
				$options = array();
				$options[] = JHtml::_('select.option', 0, JText::_('OSM_SELECT_GROUP'), 'user_id', 'name');
				$options = array_merge($options, $groupAdmins);
				$this->lists['filter_group_admin_id'] = JHtml::_('select.genericlist', $options, 'filter_group_admin_id', ' class="inputbox" onchange="submit();" ', 'user_id', 'name', $this->state->filter_group_admin_id);
			}
		}
		$options = array();
		$options[] = JHtml::_('select.option', 0, JText::_('OSM_ALL'));
		$options[] = JHtml::_('select.option', 1, JText::_('OSM_ACTIVE'));
		$options[] = JHtml::_('select.option', 2, JText::_('OSM_EXPIRED'));
		$this->lists['filter_published'] = JHtml::_('select.genericlist', $options, 'filter_published', ' class="input-box" onchange="submit();" ', 'value', 'text', $this->state->filter_published);

		$this->config = OSMembershipHelper::getConfig();
	}
}