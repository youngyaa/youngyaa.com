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
class OSMembershipViewUpgrademembershipHtml extends MPFViewHtml
{
	public $hasModel =false;

	public function display()
	{
		$user   = JFactory::getUser();
		if (!$user->id)
		{
			$return = 'index.php?option=com_osmembership&view=upgrademembership&Itemid=' . $this->Itemid;
			JFactory::getApplication()->redirect('index.php?option=com_users&view=login&return=' . base64_encode($return), JText::_('OSM_LOGIN_TO_UPGRADE_MEMBERSHIP'));
		}

		$db     = JFactory::getDbo();
		$config = OSMembershipHelper::getConfig();
		$query  = $db->getQuery(true);
		$query->select('a.*, b.username')
			->from('#__osmembership_subscribers AS a ')
			->leftJoin('#__users AS b ON a.user_id=b.id')
			->where('is_profile=1')
			->where("(a.email='$user->email' OR user_id=$user->id)");
		$db->setQuery($query);
		$item = $db->loadObject();
		if (!$item)
		{
			// Fix Profile ID
			if (OSMembershipHelper::fixProfileId($user->id))
			{
				JFactory::getApplication()->redirect(JUri::getInstance()->toString());
			}
			else
			{
				JFactory::getApplication()->redirect('index.php', JText::_('OSM_DONOT_HAVE_SUBSCRIPTION_RECORD_TO_UPGRADE'));
			}
		}
		
		if ($item->id != $item->profile_id)
		{
			$item->profile_id = $item->id;			
			$query->clear();
			$query->update('#__osmembership_subscribers')
				->set('profile_id = '. $item->id)
				->where('id = '. $item->id);
			$db->setQuery($query);
			$db->execute();			
		}

		// Get free trial plans
		$query->clear();
		$query->select('id')
			->from('#__osmembership_plans')
			->where('price = 0')
			->where('published = 1');
		$db->setQuery($query);
		$trialPlanIds = $db->loadColumn();
		if (!count($trialPlanIds))
		{
			$trialPlanIds = array(0);
		}

		// We will allow upgrade from active subscription and free trial subscriptions
		$query->clear();
		$query->select('DISTINCT plan_id')
			->from('#__osmembership_subscribers')
			->where('profile_id = ' . $item->id)
			->where('(published = 1 OR (published < 3 AND plan_id IN ('.implode(',', $trialPlanIds).')))');
		$db->setQuery($query);
		$planIds = $db->loadColumn();
		if (!count($planIds))
		{
			$planIds = array(0); 	
		}
		$query->clear();
		$query->select('*')
			->from('#__osmembership_upgraderules')
			->where('from_plan_id IN (' . implode(',', $planIds) . ')')
			->order('from_plan_id');
		$db->setQuery($query);
		$upgradeRules = $db->loadObjectList();

		$query->clear();
		$query->select('*')
			->from('#__osmembership_plans')
			->where('published = 1');

		$fieldSuffix = OSMembershipHelper::getFieldSuffix();

		if ($fieldSuffix)
		{
			OSMembershipHelperDatabase::getMultilingualFields($query, array('title'), $fieldSuffix);
		}

		$db->setQuery($query);

		$plans = $db->loadObjectList('id');

		// Load js file to support state field dropdown
		OSMembershipHelper::addLangLinkForAjax();
		JFactory::getDocument()->addScript(JUri::base(true) . '/components/com_osmembership/assets/js/paymentmethods.js');

		// Need to get subscriptions information of the user
		$this->planIds      = $planIds;
		$this->upgradeRules = $upgradeRules;
		$this->config       = $config;
		$this->plans        = $plans;
		$this->bootstrapHelper = new OSMembershipHelperBootstrap($config->twitter_bootstrap_version);

		parent::display();
	}
}