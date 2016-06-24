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

class plgOSMembershipCB extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @param   object &$subject   The object to observe
	 * @param   array  $config     An optional associative array of configuration settings.
	 *                             Recognized key values include 'name', 'group', 'params', 'language'
	 *                             (this list is not meant to be comprehensive).
	 */
	public function __construct(& $subject, $config = array())
	{
		parent::__construct($subject, $config);

		$option = JFactory::getApplication()->input->getCmd('option', '');
		$this->canRun = file_exists(JPATH_ROOT . '/components/com_comprofiler/comprofiler.php') && ($option != 'com_comprofiler');
		if ($this->canRun)
		{
			require_once JPATH_ROOT . '/components/com_osmembership/helper/helper.php';
		}
	}

	/**
	 * Method to get list of custom fields in Community builder used to map with fields in Membership Pro
	 *
	 * Method is called on custom field add / edit page from backend of Membership Pro
	 *
	 * @return mixed
	 */
	public function onGetFields()
	{
		if ($this->canRun)
		{
			$db  = JFactory::getDbo();
			$sql = 'SELECT name AS `value`, name AS `text` FROM #__comprofiler_fields WHERE `table`="#__comprofiler"';
			$db->setQuery($sql);

			return $db->loadObjectList();
		}
	}

	/**
	 * Method to get data stored in CB profile of the given user
	 *
	 * @param int   $userId
	 * @param array $mappings
	 *
	 * @return array
	 */
	public function onGetProfileData($userId, $mappings)
	{
		if ($this->canRun)
		{
			$synchronizer = new MPFSynchronizerCommunitybuilder();

			return $synchronizer->getData($userId, $mappings);
		}
	}

	/**
	 * Method to create a CB account for subscriber if it does not exist yet
	 *
	 * @param SubscriberOSMembership $row The subscription record
	 *
	 * @return bool
	 */
	public function onMembershipActive($row)
	{
		if (!$this->canRun)
		{
			return;
		}
		if ($row->user_id)
		{
			$this->createOrUpdateCBAccount($row);
			// Update the block field in users table
			$config = OSMembershipHelper::getConfig();
			if (!$config->send_activation_email)
			{
				$db  = JFactory::getDbo();
				$sql = 'UPDATE  #__users SET `block` = 0 WHERE id=' . $row->user_id;
				$db->setQuery($sql);
				$db->execute();

				$this->setCBAuth($row->user_id, 1);
			}
			return true;
		}
	}

	/**
	 * Method to block the CB account when the subscription record is expired
	 *
	 * @param SubscriberOSMembership $row The subscription record
	 */
	function onMembershipExpire($row)
	{
		if (!$this->canRun)
		{
			return;
		}
		$blockUser = $this->params->get('block_user_on_membership_expire', 0);
		if ($row->user_id && $blockUser)
		{

			$activePlanIds = OSMembershipHelper::getActiveMembershipPlans($row->user_id);
			if (count($activePlanIds) == 2 && $activePlanIds[1] == $row->plan_id)
			{
				$this->setCBAuth($row->user_id, 0);
			}
		}
	}

	/**
	 * Method to update CB profile when subscriber update his profile in Membership Pro
	 *
	 * @param SubscriberOSMembership $row The subscription record
	 *
	 * @return bool
	 */
	function onProfileUpdate($row)
	{
		if (!$this->canRun)
		{
			return;
		}
		if ($row->user_id)
		{
			$this->createOrUpdateCBAccount($row);

			return true;
		}
	}

	/**
	 * Method to create / update a CB account for subscriber
	 *
	 * @param SubscriberOSMembership $row The subscription record
	 */
	protected function createOrUpdateCBAccount($row)
	{
		$db  = JFactory::getDbo();
		$sql = 'SELECT count(*) FROM `#__comprofiler` WHERE `user_id` = ' . $db->quote($row->user_id);
		$db->setQuery($sql);
		$count = $db->loadResult();
		$sql   = ' SHOW FIELDS FROM #__comprofiler ';
		$db->setQuery($sql);
		$fields    = $db->loadObjectList();
		$fieldList = array();
		for ($i = 0, $n = count($fields); $i < $n; $i++)
		{
			$field       = $fields[$i];
			$fieldList[] = $field->Field;
		}
		//Get list of fields belong to table
		$sql = 'SELECT name, field_mapping FROM #__osmembership_fields WHERE field_mapping != "" AND field_mapping IS NOT NULL AND is_core = 1';
		$db->setQuery($sql);
		$fields      = $db->loadObjectList();
		$fieldValues = array();
		if (count($fields))
		{
			foreach ($fields as $field)
			{
				$fieldName = $field->field_mapping;
				if ($fieldName && in_array($fieldName, $fieldList))
				{
					$fieldValues[$fieldName] = $row->{$field->name};
				}
			}
		}
		$sql = 'SELECT a.field_mapping, b.field_value FROM #__osmembership_fields AS a ' . ' INNER JOIN #__osmembership_field_value AS b ' .
			' ON a.id = b.field_id ' . ' WHERE b.subscriber_id=' . $row->id;

		$db->setQuery($sql);
		$fields = $db->loadObjectList();
		if (count($fields))
		{
			foreach ($fields as $field)
			{
				if ($field->field_mapping && in_array($field->field_mapping, $fieldList))
				{
					//Check if this is a json value
					if (is_string($field->field_value) && is_array(json_decode($field->field_value)))
					{
						$fieldValues[$field->field_mapping] = implode('|*|', json_decode($field->field_value));
					}
					else
					{
						$fieldValues[$field->field_mapping] = $field->field_value;
					}
				}
			}
		}
		$profile                 = new stdClass();
		$profile->id             = $row->user_id;
		$profile->user_id        = $row->user_id;
		$profile->firstname      = $row->first_name;
		$profile->lastname       = $row->last_name;
		$profile->avatarapproved = 1;
		$profile->confirmed      = 1;
		$profile->registeripaddr = htmlspecialchars($_SERVER['REMOTE_ADDR']);
		$profile->banned         = 0;
		$profile->acceptedterms  = 1;
		foreach ($fieldValues as $fieldName => $value)
		{
			$profile->{$fieldName} = $value;
		}
		if ($count)
		{
			$db->updateObject('#__comprofiler', $profile, 'id');
		}
		else
		{
			$db->insertObject('#__comprofiler', $profile);
		}
	}

	/**
	 * Method to block / unblock a CB account
	 *
	 * @param int $userId
	 * @param int $auth
	 */
	protected function setCBAuth($userId, $auth)
	{
		$auth   = $auth ? 1 : 0;
		$userId = (int) $userId;
		$db     = JFactory::getDbo();
		$sql    = "UPDATE `#__comprofiler` SET `approved` = $auth, `confirmed` = $auth, `acceptedterms` = $auth WHERE `user_id` = $userId";
		$db->setQuery($sql);
		$db->execute();
	}
}	