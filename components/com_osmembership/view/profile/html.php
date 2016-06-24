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
 * HTML View class for OS Membership component
 *
 * @static
 * @package        Joomla
 * @subpackage     Membership Pro
 */
class OSMembershipViewProfileHtml extends MPFViewHtml
{
	function display($tpl = null)
	{
		$user = JFactory::getUser();
		if (!$user->id)
		{
			$return = 'index.php?option=com_osmembership&view=profile&Itemid=' . $this->Itemid;
			JFactory::getApplication()->redirect('index.php?option=com_users&view=login&return=' . base64_encode($return), JText::_('OSM_LOGIN_TO_EDIT_PROFILE'));
		}
		$config = OSMembershipHelper::getConfig();
		$model = $this->getModel();
		$item   = $model->getData();
		if (!$item)
		{
			if (OSMembershipHelper::fixProfileId($user->id))
			{
				// Redirect to current page after fixing the data
				JFactory::getApplication()->redirect(JUri::getInstance()->toString());
			}
			else
			{
				$redirectURL = OSMembershipHelper::getViewUrl(array('categories', 'plans', 'plan', 'register'));
				if (!$redirectURL)
				{
					$redirectURL = 'index.php';
				}
				JFactory::getApplication()->redirect($redirectURL, JText::_('OSM_DONOT_HAVE_SUBSCRIPTION_RECORD'));
			}
		}
		
		// Fix wrong data for profile record
		if ($item->id != $item->profile_id)
		{
			$item->profile_id = $item->id;
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->update('#__osmembership_subscribers')
				->set('profile_id = '. $item->id)
				->where('id = '. $item->id);
			$db->setQuery($query);
			$db->execute();			
		}

		// Get subscriptions history
		require_once JPATH_COMPONENT . '/model/subscriptions.php';
		$model = JModelLegacy::getInstance('Subscriptions', 'OSMembershipModel');
		$items = $model->getData();

		if (OSMembershipHelper::isUniquePlan($item->user_id))
		{
			$planId = $item->plan_id;
		}
		else
		{
			$planId = 0;
		}

		// Form
		$rowFields = OSMembershipHelper::getProfileFields($planId, true, $item->language);
		$data      = OSMembershipHelper::getProfileData($item, $planId, $rowFields);
		$form      = new MPFForm($rowFields);
		$form->setData($data)->bindData();
		$form->buildFieldsDependency();

		// Trigger third party add-on
		JPluginHelper::importPlugin('osmembership');
		$dispatcher = JDispatcher::getInstance();
		$results    = $dispatcher->trigger('onProfileDisplay', array($item));

		// Get renew options
		$planIds = OSMembershipHelper::getRenewablePlanIds($item->id, $user->id, (int) $config->number_days_before_renewal);

		// Load js file to support state field dropdown
		OSMembershipHelper::addLangLinkForAjax();
		JFactory::getDocument()->addScript(JUri::base(true) . '/components/com_osmembership/assets/js/paymentmethods.js');

		// Need to get subscriptions information of the user
		$plans                 = OSMembershipHelper::getSubscriptions($item->profile_id);
		$this->item            = $item;
		$this->config          = $config;
		$this->items           = $items;
		$this->form            = $form;
		$this->plugins         = $results;
		$this->planIds         = $planIds;
		$this->plans           = $plans;
		$this->bootstrapHelper = new OSMembershipHelperBootstrap($config->twitter_bootstrap_version);

		parent::display($tpl);
	}
}