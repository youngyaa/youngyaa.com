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

class plgOSMembershipEasyprofile extends JPlugin
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

		$this->canRun = file_exists(JPATH_ROOT . '/components/com_jsn/jsn.php');
	}

	/**
	 * Method to get data stored in EasyProfile of the given user
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
			$synchronizer = new MPFSynchronizerEasyprofile();

			return $synchronizer->getData($userId, $mappings);
		}
	}

	/**
	 * Method to get list of custom fields in Easyprofile used to map with fields in Membership Pro
	 *
	 * Method is called on custom field add / edit page from backend of Membership Pro
	 *
	 * @return mixed
	 */
	public function onGetFields()
	{
		if ($this->canRun)
		{
			$db = JFactory::getDbo();
			$fields = array_keys($db->getTableColumns('#__jsn_users'));
			$fields = array_diff($fields, array('id', 'params'));

			$options = array();
			foreach($fields as $field)
			{
				$options[] = JHtml::_('select.option', $field, $field);
			}

			return $options;
		}
	}

	/**
	 * Method to create a CB account for subscriber if it does not exist yet
	 *
	 * @param SubscriberOSMembership $row
	 *            The subscription record
	 *
	 * @return bool
	 */
	function onAfterStoreSubscription($row)
	{
		if (!$this->canRun)
		{
			return;
		}

		$this->storeEasyprofile($row);
	}


	/**
	 * Plugin triggered when user update his profile
	 *
	 * @param object $row The subscription record
	 */
	function onProfileUpdate($row)
	{
		if (!$this->canRun)
		{
			return;
		}
		$this->storeEasyprofile($row);
	}

	/**
	 * Plugin triggered when membership active
	 *
	 * @param object $row The subscription record
	 */
	function onMembershipActive($row)
	{
		if (!$this->canRun)
		{
			return;
		}
		$config = OSMembershipHelper::getConfig();
		if ($config->create_account_when_membership_active === '1')
		{
			$this->storeEasyprofile($row);
		}
	}


	/**
	 * Method to create or update easyprofile data
	 *
	 * @param $row
	 */
	protected function storeEasyprofile($row)
	{		
		if ($row->user_id)
		{
			$db = JFactory::getDbo();

			// Check if user exist
			$query = $db->getQuery(true);
			$query->select('a.id')->from('#__jsn_users AS a')->where('a.id = ' . $row->user_id);
			$db->setQuery($query);
			$profileId = $db->loadResult();

			// Get list of fields in #__jsn_users table
			$columns = array_keys($db->getTableColumns('#__jsn_users'));

			// Get custom fields data (both from core and none-core fields
			$fieldValues = array();

			$query->clear();
			$query->select('name, field_mapping')
				->from('#__osmembership_fields')
				->where('field_mapping != ""')
				->where('field_mapping IS NOT NULL')
				->where('is_core = 1')
				->where('published = 1');
			$db->setQuery($query);
			$fields      = $db->loadObjectList();

			if (count($fields))
			{
				foreach ($fields as $field)
				{
					$fieldName = $field->field_mapping;
					if ($fieldName && in_array($fieldName, $columns))
					{
						$fieldValues[$fieldName] = $row->{$field->name};
					}
				}
			}

			$query->clear();
			$query->select('a.field_mapping, b.field_value')
				->from('#__osmembership_fields AS a')
				->innerJoin('#__osmembership_field_value AS b ON a.id = b.field_id')
				->where('b.subscriber_id = '. $row->id);
			$db->setQuery($query);
			$fields = $db->loadObjectList();

			if (count($fields))
			{
				foreach ($fields as $field)
				{
					if ($field->field_mapping)
					{
						$fieldValues[$field->field_mapping] = $field->field_value;
					}
				}
			}
			// Write Jsn User
			if ($profileId)
			{
				// Update User
				$query = $db->getQuery(true);
				$query->update("#__jsn_users");
				foreach ($fieldValues as $key => $value)
				{
					$query->set($db->quoteName($key) . ' = ' . $db->quote($value));
				}
				$query->where('id = ' . $row->user_id);
				$db->setQuery($query);
				$db->execute();
			}
			else
			{
				// New User
				$fields = array();
				$values = array();
				foreach ($fieldValues as $key => $value)
				{
					$fields[] = $db->quoteName($key);
					$values[] = $db->quote($value);
				}
				$query = "INSERT INTO #__jsn_users(id," . implode(', ', $fields) . ") VALUES(" . $row->user_id . ", " . implode(', ', $values) . ")";
				$db->setQuery($query);
				$db->execute();
			}
		}
	}
}	