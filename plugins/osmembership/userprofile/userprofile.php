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

class plgOSMembershipUserprofile extends JPlugin
{
	public function __construct(& $subject, $config = array())
	{
		parent::__construct($subject, $config);
	}

	/**
	 * Run when a membership activated
	 *
	 * @param PlanOsMembership $row
	 */
	function onAfterStoreSubscription($row)
	{
		if ($row->user_id)
		{
			$db     = JFactory::getDbo();
			$userId = $row->user_id;
			// Update Name of users based on first name and last name from profile
			$user = JFactory::getUser($userId);
			$user->set('name', $row->first_name . ' ' . $row->last_name);
			$user->save(true);

			$deleteFields = array(
				'profile.address1',
				'profile.address2',
				'profile.city',
				'profile.region',
				'profile.country',
				'profile.postal_code',
				'profile.phone',
				'profile.website',
				'profile.favoritebook',
				'profile.aboutme',
				'profile.dob'
			);
			//Delete old profile data			
			$db->setQuery(
				'DELETE FROM #__user_profiles WHERE user_id = ' . $userId .
				' AND profile_key IN ("' . implode('","', $deleteFields) . '")'
			);
			$db->execute();
			//Get all data from this subscribers
			$sql = 'SELECT * FROM #__osmembership_fields WHERE published=1 AND (plan_id = 0 OR plan_id=' . $row->plan_id . ')';
			$db->setQuery($sql);
			$rowFields      = $db->loadObjectList();
			$subscriberData = array();
			$fieldMappings  = array();
			foreach ($rowFields as $rowField)
			{
				if ($rowField->profile_field_mapping)
				{
					$fieldMappings[$rowField->profile_field_mapping] = $rowField->name;
				}
				if ($rowField->is_core)
				{
					$subscriberData[$rowField->name] = $row->{$rowField->name};
				}
			}
			//Get all custom fields value
			$sql = 'SELECT a.name, b.field_value FROM #__osmembership_fields AS a INNER JOIN #__osmembership_field_value AS b ON a.id=b.field_id WHERE b.subscriber_id=' . $row->id;
			$db->setQuery($sql);
			$fieldValues = $db->loadObjectList();
			if (count($fieldValues))
			{
				foreach ($fieldValues as $fieldValue)
				{
					$subscriberData[$fieldValue->name] = $fieldValue->field_value;
				}
			}

			$fields = array(
				'address1',
				'address2',
				'city',
				'region',
				'country',
				'postal_code',
				'phone',
				'website',
				'favoritebook',
				'aboutme',
				'dob'
			);


			$tuples = array();
			$order  = 1;
			foreach ($fields as $field)
			{
				$value = '';
				if (isset($fieldMappings[$field]))
				{
					$fieldMapping = $fieldMappings[$field];
					if (isset($subscriberData[$fieldMapping]))
					{
						$value = $subscriberData[$fieldMapping];
					}
				}
				$tuples[] = '(' . $userId . ', ' . $db->quote('profile.' . $field) . ', ' . $db->quote(json_encode($value)) . ', ' . $order++ . ')';
			}
			$db->setQuery('INSERT INTO #__user_profiles VALUES ' . implode(', ', $tuples));
			$db->execute();
		}
	}

	/**
	 * Plugin triggered when user update his profile
	 *
	 * @param object $row The subscription record
	 */
	function onProfileUpdate($row)
	{
		$this->onAfterStoreSubscription($row);
	}

	/**
	 * Plugin triggered when membership active
	 *
	 * @param object $row The subscription record
	 */
	function onMembershipActive($row)
	{
		$config = OSMembershipHelper::getConfig();
		if ($config->create_account_when_membership_active === '1')
		{
			$this->onAfterStoreSubscription($row);
		}
	}
}	