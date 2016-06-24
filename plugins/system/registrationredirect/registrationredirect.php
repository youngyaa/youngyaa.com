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

/**
 * OS Membership Registration Redirect Plugin
 *
 * @package        Joomla
 * @subpackage     OS Membership
 */
class plgSystemRegistrationRedirect extends JPlugin
{

	public function onAfterRoute()
	{

		if (file_exists(JPATH_ROOT . '/components/com_osmembership/osmembership.php'))
		{
			require_once JPATH_ROOT . '/components/com_osmembership/helper/helper.php';
			$app    = JFactory::getApplication();
			$input  = $app->input;
			$option = $input->getCmd('option');
			$task   = $input->getCmd('task');
			$view   = $input->getCmd('view');

			// Registration redirect
			if (($option == 'com_users' && $view == 'registration') || ($option == 'com_comprofiler' && $task == 'registers') || ($option == 'com_community' && $view == 'register'))
			{
				$url = $this->params->get('redirect_url', OSMembershipHelper::getViewUrl(array('categories', 'plans', 'plan', 'register')));
				if (!$url)
				{
					$Itemid = OSMembershipHelper::getItemid();
					$url    = JRoute::_('index.php?option=com_osmembership&view=plans&Itemid=' . $Itemid);
				}
				$app->redirect($url);
			}

			// In case users enter email to login, we can convert it to username if needed
			$config = OSMembershipHelper::getConfig();
			if (!empty($config->use_email_as_username) && $option == 'com_users' && $task == 'user.login')
			{

				$method   = $input->getMethod();
				$username = $input->$method->get('username', '', 'USERNAME');

				if (JMailHelper::isEmailAddress($username))
				{
					$db = JFactory::getDbo();
					$query = $db->getQuery(true);

					$query->select('*')
						->from('#__users')
						->where('(username = '.$db->quote($username).' OR email='.$db->quote($username).')');
					$db->setQuery($query);
					$user = $db->loadObject();

					if ($user && ($user->username != $username))
					{
						$input->$method->set('username', $user->username);
					}
				}
			}

		}

		return true;
	}
}
