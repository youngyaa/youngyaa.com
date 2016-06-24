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
defined('_JEXEC') or die;

/**
 * HTML View class for Membership Pro component
 *
 * @static
 * @package        Joomla
 * @subpackage     Membership Pro
 */
class OSMembershipViewMembersHtml extends MPFViewHtml
{

	public function display()
	{
		if (!JFactory::getUser()->authorise('core.view_members', 'com_osmembership'))
		{
			JFactory::getApplication()->redirect('index.php', JText::_('OSM_NOT_ALLOW_TO_VIEW_MEMBERS'));
		}
		$model  = $this->getModel();
		$state  = $model->getState();
		$fields = OSMembershipHelper::getProfileFields($state->id, true);
		for ($i = 0, $n = count($fields); $i < $n; $i++)
		{
			if (!$fields[$i]->show_on_members_list)
			{
				unset($fields[$i]);
			}
		}
		$this->items      = $model->getData();
		$this->pagination = $model->getPagination();
		$this->fieldsData = $model->getFieldsData();
		$this->fields     = $fields;
		$this->config     = OSMembershipHelper::getConfig();
		$this->state      = $state;
		$this->params     = JFactory::getApplication()->getParams();

		parent::display();
	}
}