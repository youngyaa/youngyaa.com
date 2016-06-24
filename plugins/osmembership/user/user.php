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

class plgOSMembershipUser extends JPlugin
{
	/**
	 * Activate user account when membership is active
	 *
	 * @param $row
	 *
	 * @return bool
	 */
	public function onMembershipActive($row)
	{
		$config         = OSMembershipHelper::getConfig();
		$params         = JComponentHelper::getParams('com_users');
		$userActivation = $params->get('useractivation');
		if ($row->user_id > 0 && !$config->send_activation_email && $userActivation != 2)
		{
			$user = JFactory::getUser($row->user_id);
			$user->set('block', 0);
			$user->set('activation', '');
			$user->save(true);
		}

		return true;
	}

	/**
	 * Block the user account when membership is expired
	 *
	 * @param $row
	 *
	 * @return bool
	 */
	public function onMembershipExpire($row)
	{
		if ($row->user_id)
		{
			$blockAccount = $this->params->get('block_account_when_expired', 0);
			if ($blockAccount)
			{
				$user = JFactory::getUser($row->user_id);
				$user->set('block', 1);
				$user->save(true);
			}
		}

		return true;
	}
}
