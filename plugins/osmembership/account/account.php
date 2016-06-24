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

class plgOSMembershipAccount extends JPlugin
{
	/**
	 * Create user account when membership is active if needed
	 *
	 * @param $row
	 *
	 * @return bool
	 */
	public function onMembershipActive($row)
	{
		if (!$row->user_id && $row->username && $row->user_password)
		{
			$data['username']   = $row->username;
			$data['first_name'] = $row->first_name;
			$data['last_name']  = $row->last_name;
			$data['email']      = $row->email;

			//Password
			$privateKey        = md5(JFactory::getConfig()->get('secret'));
			$key               = new JCryptKey('simple', $privateKey, $privateKey);
			$crypt             = new JCrypt(new JCryptCipherSimple, $key);
			$data['password1'] = $crypt->decrypt($row->user_password);
			try
			{
				require_once JPATH_ROOT . '/components/com_osmembership/helper/helper.php';
				$row->user_id = (int) OSMembershipHelper::saveRegistration($data);
				$row->store();
			}
			catch (Exception $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage());
			}
		}
	}
}	