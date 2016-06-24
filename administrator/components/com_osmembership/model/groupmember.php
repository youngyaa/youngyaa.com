<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die();

/**
 * Membership Pro Component Groupmember Model
 *
 * @package        Joomla
 * @subpackage     Membership Pro
 */
class OSMembershipModelGroupmember extends MPFModelAdmin
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
	 * Initialize data for group member
	 *
	 *
	 * @return JTable
	 */
	public function getData()
	{
		$row = $this->getTable('Subscriber');
		if (count($this->state->cid))
		{
			$this->state->id = (int) $this->state->cid[0];
		}
		if ($this->state->id)
		{
			$row->load($this->state->id);
		}

		return $row;
	}

	/**
	 * Override store function to perform specific saving
	 * @see OSModel::store()
	 */
	public function store($input, $ignore = array())
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$row   = $this->getTable('Subscriber');
		$data  = $input->getData();
		$isNew = true;
		if (!$data['id'] && $data['username'] && $data['password'] && empty($data['user_id']))
		{
			//Store this account into the system and get the username
			jimport('joomla.user.helper');
			$params      = JComponentHelper::getParams('com_users');
			$newUserType = $params->get('new_usertype', 2);

			$data['groups']   = array();
			$data['groups'][] = $newUserType;
			$data['block']    = 0;
			$data['name']     = $data['first_name'] . ' ' . $data['last_name'];
			$data['email1']   = $data['email2'] = $data['email'];
			$user             = new JUser();
			$user->bind($data);
			if (!$user->save())
			{
				throw new Exception($user->getError());
			}
			$data['user_id'] = $user->id;
		}

		if ($data['id'])
		{
			$isNew = false;
			$row->load($data['id']);
		}
		if (!$row->bind($data))
		{
			throw new Exception($db->getErrorMsg());
		}

		if ($isNew)
		{
			$row->user_id      = (int) $row->user_id;
			$row->published    = 1;
			$row->created_date = gmdate('Y-m-d H:i:s');
			$row->from_date    = gmdate('Y-m-d H:i:s');
			$row->is_profile   = 1;

			// Calculate to_date
			$query->select('MAX(to_date)')
				->from('#__osmembership_subscribers')
				->where('user_id=' . $row->group_admin_id . ' AND plan_id=' . $row->plan_id . ' AND published = 1');
			$db->setQuery($query);
			$row->to_date = $db->loadResult();
		}
		if (!$row->store())
		{
			$this->setError($db->getErrorMsg());

			return false;
		}

		if ($isNew)
		{
			$row->profile_id = $row->id;
			$row->store();
		}

		$rowFields = OSMembershipHelper::getProfileFields($row->plan_id, false);
		$form      = new MPFForm($rowFields);
		$form->storeData($row->id, $data);

		if ($isNew && $row->user_id)
		{
			JPluginHelper::importPlugin('osmembership');
			$dispatcher = JDispatcher::getInstance();
			$dispatcher->trigger('onAfterStoreSubscription', array($row));
			$dispatcher->trigger('onMembershipActive', array($row));
		}

		return true;
	}

	/**
	 * Delete the selected group members
	 *
	 * @param array $cid
	 */
	public function delete($cid = array())
	{
		$row   = $this->getTable('Subscriber');
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		foreach ($cid as $id)
		{
			$row->load($id);
			$query->clear();
			$query->delete('#__osmembership_field_value')
				->where('subscriber_id = ' . $id);
			$db->setQuery($query);
			$db->execute();
			JPluginHelper::importPlugin('osmembership');
			$dispatcher = JDispatcher::getInstance();
			$dispatcher->trigger('onMembershipExpire', array($row));
			if ($row->user_id)
			{
				// If there is only one subscription record, we will delete Joomla account as well
				$query->clear();
				$query->select('COUNT(*)')
					->from('#__osmembership_subscribers')
					->where('user_id = ' . $row->user_id);
				$db->setQuery($query);
				$total = (int) $db->loadResult();
				if ($total == 1)
				{
					// Only one record
					$rowUser = new JUser();
					$rowUser->load($row->user_id);
					if ($rowUser)
					{
						$rowUser->delete();
					}
				}
			}

			// Delete the subscription record
			$row->delete();
		}
	}

	/**
	 * Delete custom fields data related to selected subscribers, trigger event before actual delete the data
	 *
	 * @param array $cid
	 */
	protected function beforeDelete($cid)
	{
		if (count($cid))
		{
			//
			$db    = $this->getDbo();
			$query = $db->getQuery(true);
			$query->delete('#__osmembership_field_value')
				->where('subscriber_id IN (' . implode(',', $cid) . ')');
			$db->setQuery($query);
			$db->execute();
			JPluginHelper::importPlugin('osmembership');
			$dispatcher = JDispatcher::getInstance();
			$row        = $this->getTable('Subscriber');
			foreach ($cid as $id)
			{
				$row->load($id);
				$dispatcher->trigger('onMembershipExpire', array($row));
			}
		}
	}

	/**
	 * Pre-process before publishing the actual record
	 *
	 * @param array $cid
	 * @param int   $state
	 *
	 * @throws Exception
	 */
	protected function beforePublish($cid, $state)
	{
		if ($state == 1)
		{
			$row = $this->getTable('Subscriber');
			JPluginHelper::importPlugin('osmembership');
			$dispatcher = JDispatcher::getInstance();
			foreach ($cid as $id)
			{
				$row->load($id);
				if (!$row->published)
				{
					$dispatcher->trigger('onMembershipActive', array($row));
					OSMembershipHelper::sendMembershipApprovedEmail($row);
				}
			}
		}
		parent::publish($cid, $state);

	}

	/**
	 * Get JTable object for the model
	 *
	 * @param string $name
	 *
	 * @return JTable
	 */
	public function getTable($name = 'Subscriber')
	{

		return parent::getTable($name);
	}
}