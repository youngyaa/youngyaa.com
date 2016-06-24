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
class OSMembershipViewRenewmembershipHtml extends MPFViewHtml
{
	public $hasModel = false;

	public function display()
	{
		$db   = JFactory::getDbo();
		$user = JFactory::getUser();
		if (!$user->id)
		{
			$return = 'index.php?option=com_osmembership&view=renewmembership&Itemid=' . $this->Itemid;
			JFactory::getApplication()->redirect('index.php?option=com_users&view=login&return=' . base64_encode($return), JText::_('OSM_LOGIN_TO_RENEW_MEMBERSHIP'));
		}
		$config = OSMembershipHelper::getConfig();
		$query  = $db->getQuery(true);
		$query->select('a.*, b.username')
			->from('#__osmembership_subscribers AS a ')
			->leftJoin('#__users AS b ON a.user_id=b.id')
			->where('is_profile=1')
			->where("(a.email='$user->email' OR a.user_id=$user->id)");
		$db->setQuery($query);
		$item = $db->loadObject();
		if (!$item)
		{
			// Try to fix the profile id field
			if (OSMembershipHelper::fixProfileId($user->id))
			{
				JFactory::getApplication()->redirect(JUri::getInstance()->toString());
			}
			else
			{
				JFactory::getApplication()->redirect('index.php', JText::_('OSM_DONOT_HAVE_SUBSCRIPTION_RECORD_TO_RENEW'));
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
		
		// Get IDs of the plans which can be renewed
		$planIds = OSMembershipHelper::getRenewablePlanIds($item->id, $user->id, (int) $config->number_days_before_renewal);

		// Load js file to support state field dropdown
		OSMembershipHelper::addLangLinkForAjax();
		JFactory::getDocument()->addScript(JUri::base(true) . '/components/com_osmembership/assets/js/paymentmethods.js');

		// Need to get subscriptions information of the user
		$this->planIds = $planIds;
		$this->config  = $config;
		$this->bootstrapHelper = new OSMembershipHelperBootstrap($config->twitter_bootstrap_version);

		parent::display();
	}
}