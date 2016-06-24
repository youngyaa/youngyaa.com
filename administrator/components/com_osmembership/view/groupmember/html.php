<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 Ossolution Team
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
class OSMembershipViewGroupmemberHtml extends MPFViewItem
{

	protected function prepareView()
	{
		parent::prepareView();

		$db     = JFactory::getDbo();
		$query  = $db->getQuery(true);
		$item   = $this->item;
		$lists  = &$this->lists;
		$config = OSMembershipHelper::getConfig();

		// Plan section
		$query->select('id, title')
			->from('#__osmembership_plans')
			->where('published = 1')
			->where('number_group_members > 0')
			->order('ordering');

		$db->setQuery($query);
		$options          = array();
		$options[]        = JHtml::_('select.option', 0, JText::_('OSM_SELECT_PLAN'), 'id', 'title');
		$options          = array_merge($options, $db->loadObjectList());
		$lists['plan_id'] = JHtml::_('select.genericlist', $options, 'plan_id', ' class="inputbox" onchange="buildGroupAdmin(this.value);" ', 'id', 'title', $item->plan_id);

		// Group selection
		$options   = array();
		$options[] = JHtml::_('select.option', 0, JText::_('OSM_SELECT_GROUP'), 'user_id', 'name');
		if ($item->plan_id)
		{
			$query->clear();
			$query->select('DISTINCT user_id, CONCAT(first_name, " ", last_name) AS name')
				->from('#__osmembership_subscribers AS a')
				->where('plan_id = ' . $item->plan_id)
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
		$this->lists['group_admin_id'] = JHtml::_('select.genericlist', $options, 'group_admin_id', ' class="inputbox"', 'user_id', 'name', $item->group_admin_id);

		// Form field data
		$rowFields = OSMembershipHelper::getProfileFields($item->plan_id, true, $item->language);
		$data      = array();

		$firstName = $this->input->getString('first_name', '');
		if ($firstName)
		{
			$data       = $this->input->post->getData();
			$setDefault = false;
		}
		elseif ($item->id)
		{
			$data       = OSMembershipHelper::getProfileData($item, $item->plan_id, $rowFields);
			$setDefault = false;
		}
		else
		{
			$setDefault = true;
		}
		if (!isset($data['country']) || !$data['country'])
		{
			$data['country'] = $config->default_country;
		}

		$form = new MPFForm($rowFields);
		$form->setData($data)->bindData($setDefault);
		$this->config = $config;
		$this->form   = $form;
	}
}