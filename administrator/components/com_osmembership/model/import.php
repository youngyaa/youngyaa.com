<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die();

/**
 * OS Membership Component Import Model
 *
 * @package        Joomla
 * @subpackage     Membership Pro
 */
class OSMembershipModelImport extends MPFModel
{
	/**
	 * @param $input
	 *
	 * @return int
	 * @throws Exception
	 */
	public function store($input)
	{
		jimport('joomla.user.helper');
		$db               = JFactory::getDbo();
		$query            = $db->getQuery(true);
		$params           = JComponentHelper::getParams('com_users');
		$newUserType      = $params->get('new_usertype', 2);
		$subscribers      = $this->_getSubscriberCSV($input);
		$data             = array();
		$data['groups']   = array();
		$data['groups'][] = $newUserType;
		$data['block']    = 0;
		$rowFieldValue    = JTable::getInstance('OsMembership', 'FieldValue');
		$query->select('id, name')
			->from('#__osmembership_fields')
			->where('is_core = 0');
		$db->setQuery($query);
		$customFields = $db->loadObjectList();
		$imported     = 0;
		JPluginHelper::importPlugin('osmembership');
		$dispatcher = JDispatcher::getInstance();

		// Get list of plans
		$query->clear();
		$query->select('id, title')
			->from('#__osmembership_plans');
		$db->setQuery($query);
		$rows  = $db->loadObjectList();
		$plans = array();
		foreach ($rows as $row)
		{
			$plans[JString::strtolower($row->title)] = $row->id;
		}

		if (count($subscribers))
		{
			foreach ($subscribers as $subscriber)
			{
				$userId = 0;
				//check username exit in table users
				if ($subscriber['username'])
				{
					$sql = 'SELECT id FROM #__users WHERE username="' . $subscriber['username'] . '"';
					$db->setQuery($sql);
					$userId = (int) $db->loadResult();
					if (!$userId)
					{
						$data['name'] = $subscriber['first_name'] . ' ' . $subscriber['last_name'];
						if ($subscriber['password'])
						{
							$data['password'] = $data['password2'] = $subscriber['password'];
						}
						else
						{
							$data['password'] = $data['password2'] = JUserHelper::genRandomPassword();
						}
						$data['email']    = $data['email1'] = $data['email2'] = $subscriber['email'];
						$data['username'] = $subscriber['username'];
						if ($data['username'] && $data['name'] && $data['email1'])
						{
							$user = new JUser();
							$user->bind($data);
							$user->save();
							$userId = $user->id;
						}
					}
				}
				//get plan Id
				$planTitle             = JString::strtolower($subscriber['plan']);
				$planId                = isset($plans[$planTitle]) ? $plans[$planTitle] : 0;
				$subscriber['plan_id'] = $planId;
				$subscriber['user_id'] = $userId;
				//save subscribers core
				$row = $this->getTable('Subscriber');
				$row->bind($subscriber);
				if (!$row->payment_date)
				{
					$row->payment_date = $row->from_date;
				}
				$row->created_date = $row->from_date;
				$profileId         = 0;
				if ($userId > 0)
				{
					$query->clear();
					$query->select('id')
						->from('#__osmembership_subscribers')
						->where('is_profile = 1')
						->where('user_id = ' . $userId);
					$db->setQuery($query);
					$profileId = $db->loadResult();
				}
				if ($profileId)
				{
					$row->is_profile = 0;
					$row->profile_id = $profileId;
				}
				else
				{
					$row->is_profile = 1;
				}

				// If the subscription record exists, we will just update the data
				$query->clear();
				$query->select('id')
					->from('#__osmembership_subscribers')
					->where('email = ' . $db->quote($row->email))
					->where('plan_id = ' . (int) $row->plan_id)
					->where('from_date=' . $db->quote($row->from_date))
					->where('to_date=' . $db->quote($row->to_date));

				$db->setQuery($query);
				$subscriptionId = (int) $db->loadResult();
				if ($subscriptionId)
				{
					$row->id = $subscriptionId;
					// Delete old custom fields data
					$query->clear();
					$query->delete('#__osmembership_field_value')
						->where('subscriber_id = ' . $subscriptionId);
					$db->setQuery($query);
					$db->execute();
				}

				$row->store();
				if (!$row->profile_id)
				{
					$row->profile_id = $row->id;
					$row->store();
				}
				//get Extra Field				
				if (count($customFields))
				{
					foreach ($customFields as $customField)
					{
						if (isset($subscriber[$customField->name]) && $subscriber[$customField->name])
						{
							$rowFieldValue->id            = 0;
							$rowFieldValue->field_id      = $customField->id;
							$rowFieldValue->subscriber_id = $row->id;
							$rowFieldValue->field_value   = $subscriber[$customField->name];
							$rowFieldValue->store();
						}
					}
				}
				if ($row->published == 1)
				{
					$dispatcher->trigger('onMembershipActive', array($row));
				}
				$imported++;
			}
		}

		return $imported;
	}

	/**
	 * Get subscribers data from csv file
	 *
	 * @param $input
	 *
	 * @return array
	 */
	protected function _getSubscriberCSV($input)
	{
		jimport('joomla.filesystem.file');
		$keys        = array();
		$subscribers = array();
		$subscriber  = array();
		$allowedExts = array('csv');
		$csvFile     = $input->files->get('csv_subscribers');
		$csvFileName = $csvFile['tmp_name'];
		$fileName    = $csvFile['name'];
		$fileExt     = strtolower(JFile::getExt($fileName));
		if (in_array($fileExt, $allowedExts))
		{
			$line = 0;
			$fp   = fopen($csvFileName, 'r');
			while (($cells = fgetcsv($fp)) !== false)
			{
				if ($line == 0)
				{
					foreach ($cells as $key)
					{
						$keys[] = $key;
					}
					$line++;
				}
				else
				{
					$i = 0;
					foreach ($cells as $cell)
					{
						$subscriber[$keys[$i]] = $cell;
						$i++;
					}
					$subscribers[] = $subscriber;
				}
			}
			fclose($fp);

			return $subscribers;
		}
	}
}