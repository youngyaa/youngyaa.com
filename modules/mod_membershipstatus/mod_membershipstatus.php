<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

$userId = JFactory::getUser()->get('id');
if (file_exists(JPATH_ROOT . '/components/com_osmembership/osmembership.php') && $userId)
{
	require_once JPATH_ROOT . '/components/com_osmembership/helper/helper.php';

	OSMembershipHelper::loadLanguage();
	
	$config = OSMembershipHelper::getConfig();
	if (empty($config->debug))
	{
		error_reporting(0);
	}
	else
	{
		error_reporting(E_ALL);
	}

	$db    = JFactory::getDbo();
	$query = $db->getQuery(true);
	$query->select('*')
		->from('#__osmembership_subscribers')
		->where('is_profile = 1')
		->where('user_id = ' . $userId);
	$db->setQuery($query);
	$rowProfile = $db->loadObject();
	if ($rowProfile)
	{
		$rowSubscriptions = OSMembershipHelper::getSubscriptions($rowProfile->id);
		for ($i = 0; $i < $n; $i++)
		{
			$rowSubscription = $rowSubscriptions[$i];
			if ($rowSubscription->subscription_status != 1)
			{
				unset($rowSubscriptions[$i]);
			}
		}
	}

	require JModuleHelper::getLayoutPath('mod_membershipstatus', 'default');
}