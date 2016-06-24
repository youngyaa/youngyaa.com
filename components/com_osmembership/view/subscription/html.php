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
class OSMembershipViewSubscriptionHtml extends MPFViewHtml
{

	function display()
	{
		$user  = JFactory::getUser();
		$model = $this->getModel();
		$item  = $model->getData();

		if ($item->user_id != $user->get('id'))
		{
			JFactory::getApplication()->redirect('index.php', JText::_('OSM_INVALID_ACTION'));
		}

		//Form
		$rowFields = OSMembershipHelper::getProfileFields($item->plan_id, true, $item->language);
		$data      = OSMembershipHelper::getProfileData($item, $item->plan_id, $rowFields);
		$form      = new MPFForm($rowFields);
		$form->setData($data)->bindData();
		$form->buildFieldsDependency(false);

		$this->config = OSMembershipHelper::getConfig();
		$this->item   = $item;
		$this->form   = $form;

		parent::display();
	}
}