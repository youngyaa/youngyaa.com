<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die();

class OSMembershipModelProfile extends MPFModel
{

	/**
	 * Get profile data of the users
	 */
	public function getData()
	{
		$user  = JFactory::getUser();
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('a.*, b.username')
			->from('#__osmembership_subscribers AS a ')
			->leftJoin('#__users AS b ON a.user_id=b.id')
			->where('is_profile=1')
			->where("(a.email='$user->email' OR user_id=$user->id)")
			->order('id DESC');
		
		$db->setQuery($query);

		return $db->loadObject();
	}

	/**
	 * Update profile of the user
	 *
	 * @param array $data
	 *
	 * @return boolean
	 */
	public function updateProfile($data)
	{
		$db  = $this->getDbo();
		$row = $this->getTable('Subscriber');
		$row->load($data['id']);
		$query    = $db->getQuery(true);
		$userData = array();
		$query->select('COUNT(*)')
			->from('#__users')
			->where('email=' . $db->quote($data['email']))
			->where('id!=' . $row->user_id);
		$db->setQuery($query);
		$total = $db->loadResult();
		if (!$total)
		{
			$userData['email'] = $data['email'];
		}
		if ($data['password'])
		{
			$userData['password2'] = $userData['password'] = $data['password'];
		}
		if (count($userData))
		{
			$user = JFactory::getUser($row->user_id);
			$user->bind($userData);
			$user->save(true);
		}

		if (!$row->bind($data))
		{
			$this->setError($db->getErrorMsg());

			return false;
		}
		if (!$row->check())
		{
			$this->setError($db->getErrorMsg());

			return false;
		}
		if (!$row->store())
		{
			$this->setError($db->getErrorMsg());

			return false;
		}

		//Store custom field data for this profile record

		if (OSMembershipHelper::isUniquePlan($user->id))
		{
			$planId = $row->plan_id;
		}
		else
		{
			$planId = 0;
		}

		$rowFields = OSMembershipHelper::getProfileFields($planId, false);
		$form      = new MPFForm($rowFields);
		$form->storeData($row->id, $data, true);

		//Synchronize profile data of other subscription records from this subscriber
		$config = OSMembershipHelper::getConfig();
		if ($config->synchronize_data !== '0')
		{
			OSMembershipHelper::syncronizeProfileData($row, $data);
		}
		//Trigger event	onProfileUpdate event
		JPluginHelper::importPlugin('osmembership');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onProfileUpdate', array($row));

		return true;
	}

}	