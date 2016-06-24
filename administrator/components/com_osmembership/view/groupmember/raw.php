<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     OSMembership
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die();

class OSMembershipViewGroupmemberRaw extends MPFViewHtml
{

	public function display()
	{
		$this->setLayout('groupadmins');
		$db      = JFactory::getDbo();
		$query   = $db->getQuery(true);
		$planId = $this->input->getInt('plan_id');
		$groupAdminId = $this->input->getInt('group_admin_id');

		$options   = array();
		$options[] = JHtml::_('select.option', 0, JText::_('OSM_SELECT_GROUP'), 'user_id', 'name');
		if ($planId)
		{
			$query->clear();
			$query->select('DISTINCT user_id, CONCAT(first_name, " ", last_name) AS name')
				->from('#__osmembership_subscribers AS a')
				->where('plan_id = ' . $planId)
				->where('group_admin_id = 0')
				->group('user_id')
				->order('name');
			$db->setQuery($query);
			$groupAdmins = $db->loadObjectList();
			if (count($groupAdmins))
			{

				$options = array_merge($options, $groupAdmins);

			}
		}
		$this->lists['group_admin_id'] = JHtml::_('select.genericlist', $options, 'group_admin_id', ' class="inputbox"', 'user_id', 'name', $groupAdminId);

		parent::display();
	}
}