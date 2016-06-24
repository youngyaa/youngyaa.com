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

class PlgUserOSMembership extends JPlugin
{
	/**
	 * Utility method to act on a user after it has been saved.
	 *
	 * This method creates a subscription record for the saved user
	 *
	 * @param   array   $user    Holds the new user data.
	 * @param   boolean $isnew   True if a new user is stored.
	 * @param   boolean $success True if user was successfully stored in the database.
	 * @param   string  $msg     Message.
	 *
	 * @return  void
	 *
	 * @since   2.0.7
	 */
	public function onUserAfterSave($user, $isnew, $success, $msg)
	{
		// If the user wasn't stored we don't resync
		if (!$success)
		{
			return false;
		}

		// If the user isn't new we don't sync
		if (!$isnew)
		{
			return false;
		}

		// Ensure the user id is really an int
		$userId = (int) $user['id'];

		// If the user id appears invalid then bail out just in case
		if (empty($userId))
		{
			return false;
		}

		$planId = $this->params->get('plan_id', 0);

		if (empty($planId))
		{
			return false;
		}

		if (!file_exists(JPATH_ROOT . '/components/com_osmembership/osmembership.php'))
		{
			return;
		}

		$input  = JFactory::getApplication()->input;
		$option = $input->getCmd('option');
		if ($option == 'com_osmembership')
		{
			return false;
		}

		$db       = JFactory::getDbo();
		$nullDate = $db->getNullDate();

		// Create subscription record
		require_once JPATH_ADMINISTRATOR . '/components/com_osmembership/loader.php';
		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_osmembership/table');
		$row     = JTable::getInstance('osmembership', 'Subscriber');
		$rowPlan = JTable::getInstance('osmembership', 'Plan');
		$rowPlan->load($planId);

		// Initial basic data for the subscription record
		$row->plan_id = $planId;
		$row->user_id = $userId;
		$row->email   = $user['email'];
		$name         = $user['name'];
		$pos          = strpos($name, ' ');
		if ($pos !== false)
		{
			$row->first_name = substr($name, 0, $pos);
			$row->last_name  = substr($name, $pos + 1);
		}
		else
		{
			$row->first_name = $name;
			$row->last_name  = '';
		}
		$row->created_date = JFactory::getDate()->toSql();
		$date              = JFactory::getDate();
		$row->from_date    = $date->toSql();
		$row->from_date    = JFactory::getDate()->toSql();

		// Calculate price, from date, to date for the subscription record
		if ($rowPlan->expired_date && $rowPlan->expired_date != $nullDate)
		{
			$expiredDate = JFactory::getDate($rowPlan->expired_date);

			// Change year of expired date to current year
			$expiredDate->setDate(JFactory::getDate()->format('Y'), $expiredDate->month, $expiredDate->day);
			$expiredDate->setTime(0, 0, 0);
			$startDate = clone $date;
			$startDate->setTime(0, 0, 0);

			if ($startDate >= $expiredDate)
			{
				$date->setDate($date->year + 1, $expiredDate->month, $expiredDate->day);
				$row->to_date = $date->toSql();
			}
			else
			{
				$row->to_date = $rowPlan->expired_date;
			}
		}
		else
		{
			if ($rowPlan->lifetime_membership)
			{
				$row->to_date = '2099-12-31 23:59:59';
			}
			else
			{
				$dateIntervalSpec = 'P' . $rowPlan->subscription_length . $rowPlan->subscription_length_unit;
				$row->to_date     = $date->add(new DateInterval($dateIntervalSpec))->toSql();
			}
		}

		$row->amount                 = $rowPlan->price;
		$row->discount_amount        = 0;
		$row->tax_amount             = 0;
		$row->payment_processing_fee = 0;
		$row->gross_amount           = $rowPlan->price;
		$row->published              = 1;
		$row->store();

		// Store profile ID
		$row->profile_id = $row->id;
		$row->store();

		JPluginHelper::importPlugin('osmembership');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onAfterStoreSubscription', array($row));
		$dispatcher->trigger('onMembershipActive', array($row));

		// Store custom fields data if Joomla core user-profile is enabled
		if (JPluginHelper::isEnabled('user', 'profile'))
		{
			$rowFields   = OSMembershipHelper::getProfileFields($row->plan_id, true);
			$formData    = $input->post->get('jform', array(), 'array');
			$profileData = isset($formData['profile']) ? $formData['profile'] : array();
			$customFieldData = array();
			if (count($formData))
			{				
				foreach ($rowFields as $rowField)
				{
					if ($rowField->profile_field_mapping && !empty($profileData[$rowField->profile_field_mapping]))
					{
						if ($rowField->is_core)
						{
							$row->{$rowField->name} = $profileData[$rowField->profile_field_mapping];
						}
						else
						{
							$customFieldData[$rowField->id] = $profileData[$rowField->profile_field_mapping];
						}
					}
				}
			}
			$row->store();
			if (count($customFieldData))
			{
				$rowFieldValue = JTable::getInstance('OsMembership', 'FieldValue');
				foreach ($customFieldData as $fieldId => $fieldValue)
				{
					$rowFieldValue->id            = 0;
					$rowFieldValue->field_id      = $fieldId;
					$rowFieldValue->subscriber_id = $row->id;
					if (is_array($fieldValue))
					{
						$rowFieldValue->field_value = json_encode($fieldValue);
					}
					else
					{
						$rowFieldValue->field_value = $fieldValue;
					}
					$rowFieldValue->store();
				}
			}
		}

		return true;
	}

	/**
	 * Handle Login redirect
	 *
	 * @param $options
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function onUserAfterLogin($options)
	{
		if (!file_exists(JPATH_ROOT . '/components/com_osmembership/osmembership.php'))
		{
			return;
		}

		$app = JFactory::getApplication();

		if ($app->isAdmin())
		{
			return;
		}

		if ($this->params->get('handle_login_redirect', 0))
		{
			require_once JPATH_ROOT . '/components/com_osmembership/helper/helper.php';

			$loginRedirectUrl = OSMembershipHelper::getLoginRedirectUrl();

			if ($loginRedirectUrl)
			{
				$app->setUserState('users.login.form.return', $loginRedirectUrl);
			}
		}
	}
}
