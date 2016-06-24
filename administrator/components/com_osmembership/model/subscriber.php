<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     OS Membership
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die();

class OSMembershipModelSubscriber extends MPFModelAdmin
{
	/**
	 * Instantiate the model.
	 *
	 * @param array $config configuration data for the model
	 *
	 */
	public function __construct($config = array())
	{
		$config['table'] = '#__osmembership_subscribers';

		parent::__construct($config);
	}

	/**
	 * Load profile data
	 *
	 * @return mixed
	 */
	public function loadData()
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('a.*, b.username')
			->from('#__osmembership_subscribers AS a ')
			->leftJoin('#__users AS b ON a.user_id=b.id')
			->where('a.id=' . (int) $this->state->id);
		$db->setQuery($query);

		$this->data = $db->loadObject();
	}

	/**
	 * Method to store a subscription record
	 *
	 * @param MPFInput $input
	 * @param array    $ignore
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function store($input, $ignore = array())
	{
		$db   = $this->getDbo();
		$row  = $this->getTable('Subscriber');
		$data = $input->getData();
		$row->load($data['id']);
		if (isset($data['password']))
		{
			$userData = array();
			$query    = $db->getQuery(true);
			$query->select('COUNT(*)')
				->from('#__users')
				->where('email=' . $db->quote($data['email']))
				->where('id!=' . (int) $row->user_id);
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
		}
		if (!$row->bind($data))
		{
			throw new Exception($db->getErrorMsg());
		}
		if (!$row->check())
		{
			throw new Exception($db->getErrorMsg());
		}
		if (!$row->store())
		{
			throw new Exception($db->getErrorMsg());
		}

		//Store custom field data for this profile record
		$rowFields = OSMembershipHelper::getProfileFields(0, false);
		$form      = new MPFForm($rowFields);
		$form->storeData($row->id, $data);

		$config = OSMembershipHelper::getConfig();
		if ($config->synchronize_data !== '0')
		{
			//Syncronize profile data of other subscription records from this subscriber
			OSMembershipHelper::syncronizeProfileData($row, $data);
		}

		//Trigger event	onProfileUpdate event	
		JPluginHelper::importPlugin('osmembership');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onProfileUpdate', array($row));

		return true;
	}
}	