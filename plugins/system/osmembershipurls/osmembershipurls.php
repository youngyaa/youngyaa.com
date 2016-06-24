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

error_reporting(0);
if (!file_exists(JPATH_ROOT.'/components/com_osmembership/osmembership.php'))
{
	return ;
}
class plgSystemOSMembershipUrls extends JPlugin
{
	function onAfterInitialise()
	{				
		$app = JFactory::getApplication() ;		
		if ($app->isAdmin())
		{
			return true;
		}				
		if (JPluginHelper::isEnabled('osmembership', 'urls')) 
		{			
			$db = JFactory::getDbo();
			$currentUrl = trim(JUri::getInstance()->toString());			
			//remove www in the url
			$currentUrl = str_replace('www.', '', $currentUrl);		
			$siteURL = JURI::root() ;
			$siteURL = str_replace('www.', '', $siteURL) ;
			if ($siteURL == $currentUrl) 
			{
				//Dont' prevent access to homepage
				return ;
			}						
			$sql = 'SELECT DISTINCT plan_id FROM #__osmembership_urls WHERE url = "'.$currentUrl.'"';
			$db->setQuery($sql);
			$planIds = $db->loadColumn() ;
			if (count($planIds)) 
			{
				//Check to see the current user has an active subscription plans
				require_once JPATH_ROOT.'/components/com_osmembership/helper/helper.php';
				$activePlans = OSMembershipHelper::getActiveMembershipPlans();
				if (!count(array_intersect($planIds, $activePlans))) 
				{
					//Load language file
					OSMembershipHelper::loadLanguage();
					//Get title of these subscription plans
					$sql = 'SELECT title FROM #__osmembership_plans WHERE id IN ('.implode(',', $planIds).') AND published=1 ORDER BY ordering';
					$db->setQuery($sql) ;
					$planTitles = $db->loadColumn();
					$planTitles = implode(' OR ', $planTitles);
					$msg = JText::_('OS_MEMBERSHIP_URL_ACCESS_RESITRICTED');
					$msg = str_replace('[PLAN_TITLES]', $planTitles, $msg) ;		
					$redirectUrl = $this->params->get('redirect_url', OSMembershipHelper::getViewUrl(array('categories', 'plans', 'plan', 'register')));
					if (!$redirectUrl) 
					{
						$redirectUrl = JUri::root() ;
					}								
					JFactory::getApplication()->redirect($redirectUrl, $msg);			
				}				
			}			
		}
	}
}

