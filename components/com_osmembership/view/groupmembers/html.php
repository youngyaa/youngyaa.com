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
class OSMembershipViewGroupmembersHtml extends MPFViewHtml
{

	public function display()
	{
		$canManage = OSMembershipHelper::getManageGroupMemberPermission();
		if (!$canManage)
		{
			JFactory::getApplication()->redirect('index.php', JText::_('OSM_NOT_ALLOW_TO_MANAGE_GROUP_MEMBERS'));

			return;
		}
		$fields = OSMembershipHelper::getProfileFields(0, true);
		for ($i = 0, $n = count($fields); $i < $n; $i++)
		{
			if (!$fields[$i]->show_on_members_list)
			{
				unset($fields[$i]);
			}
		}

		$model = $this->getModel();

		$this->items      = $model->getData();
		$this->fields     = $fields;
		$this->config     = OSMembershipHelper::getConfig();
		$this->pagination = $model->getPagination();
		$this->canManage  = $canManage;
		$this->state      = $model->getState();

		parent::display();
	}
}