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
 * OSMembership Plugin controller
 *
 * @package        Joomla
 * @subpackage     Membership Pro
 */
class OSMembershipControllerSubscription extends OSMembershipController
{

	/**
	 * Renew subscription for given user
	 */
	public function renew()
	{
		$cid = $this->input->get('cid', array(), 'array');
		JArrayHelper::toInteger($cid);
		$id    = $cid[0];
		$model = $this->getModel('subscription');
		$model->renew($id);
		$msg = JText::_('Subscription was successfully renewed for selected subscriber');
		$this->setRedirect($this->getViewListUrl(), $msg);
	}

	/**
	 * Import Subscribers from CSV
	 */
	public function import()
	{
		$model             = $this->getModel('import');
		$numberSubscribers = $model->store($this->input);
		if ($numberSubscribers === false)
		{
			$this->setRedirect('index.php?option=com_osmembership&view=import', JText::_('OSM_ERROR_IMPORT_SUBSCRIBERS'));
		}
		else
		{
			$this->setRedirect('index.php?option=com_osmembership&view=subscriptions',
				JText::sprintf('OSM_NUMNER_SUBSCRIBERS_IMPORTED', $numberSubscribers));
		}
	}

	/**
	 * Export subscribers
	 */
	public function export()
	{
		$config    = OSMembershipHelper::getConfig();
		$db        = JFactory::getDbo();
		$query     = $db->getQuery(true);
		$planId    = $this->input->getInt('plan_id');
		$published = $this->input->getInt('published', -1);

		$query->select('a.*, b.username, c.title')
			->from('#__osmembership_subscribers AS a')
			->leftJoin('#__users AS b ON a.user_id = b.id')
			->leftJoin('#__osmembership_plans AS c ON a.plan_id = c.id');

		if ($planId > 0)
		{
			$query->where('a.plan_id = ' . $planId);
		}

		if ($published != -1)
		{
			$query->where(' a.published=' . $published);
		}
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		if (count($rows))
		{

			$ids = array();
			for ($i = 0, $n = count($rows); $i < $n; $i++)
			{
				$row   = $rows[$i];
				$ids[] = $row->id;
				switch ($row->published)
				{
					case 0:
						$row->subscription_status = JText::_('OSM_PENDING');
						break;
					case 1:
						$row->subscription_status = JText::_('OSM_ACTIVE');
						break;
					case 2:
						$row->subscription_status = JText::_('OSM_EXPIRED');
						break;
					case 3:
						$row->subscription_status = JText::_('OSM_CANCELLED_PENDING');
						break;
					case 4:
						$row->subscription_status = JText::_('OSM_CANCELLED_REFUNDED');
						break;
					default:
						$row->subscription_status = '';
						break;
				}
			}

			$query->clear();
			$query->select('name, title')
				->from('#__osmembership_plugins');
			$db->setQuery($query);
			$plugins      = $db->loadObjectList();
			$pluginTitles = array();
			foreach ($plugins as $plugin)
			{
				$pluginTitles[$plugin->name] = $plugin->title;
			}

			//Get list of custom fields
			$query->clear();
			$query->select('id, name, title, is_core')
				->from('#__osmembership_fields')
				->where('published = 1')
				->order('ordering');
			$db->setQuery($query);
			$rowFields = $db->loadObjectList();

			$customFieldDatas = array();
			$query->clear();
			$query->select('*')
				->from('#__osmembership_field_value')
				->where('subscriber_id IN (' . implode(',', $ids) . ')');
			$db->setQuery($query);
			$fieldDatas = $db->loadObjectList();
			if (count($fieldDatas))
			{
				foreach ($fieldDatas as $fieldData)
				{
					$customFieldDatas[$fieldData->subscriber_id][$fieldData->field_id] = $fieldData->field_value;
				}
			}

			$results_arr   = array();
			$results_arr[] = JText::_('OSM_PLAN');
			$results_arr[] = JText::_('Username');
			foreach ($rowFields as $rowField)
			{
				$results_arr[] = $rowField->title;
			}
			$results_arr[] = JText::_('OSM_SUBSCRIPTION_START_DATE');
			$results_arr[] = JText::_('OSM_SUBSCRIPTION_END_DATE');
			$results_arr[] = JText::_('OSM_SUBSCRIPTION_STATUS');
			$results_arr[] = JText::_('OSM_DISCOUNT_AMOUNT');
			$results_arr[] = JText::_('OSM_TAX_AMOUNT');
			$results_arr[] = JText::_('OSM_GROSS_AMOUNT');
			$results_arr[] = JText::_('OSM_PAYMENT_METHOD');
			$results_arr[] = JText::_('OSM_TRANSACTION_ID');
			$results_arr[] = JText::_('OSM_MEMBERSHIP_ID');

			$csv_output = "\"" . implode("\",\"", $results_arr) . "\"";

			foreach ($rows as $r)
			{
				$results_arr   = array();
				$results_arr[] = $r->title;
				$results_arr[] = $r->username;
				foreach ($rowFields as $rowField)
				{
					if ($rowField->is_core)
					{
						$fieldName     = $rowField->name;
						$results_arr[] = $r->{$fieldName};
					}
					else
					{
						$fieldId    = $rowField->id;
						$fieldValue = @$customFieldDatas[$r->id][$fieldId];
						if (is_string($fieldValue) && is_array(json_decode($fieldValue)))
						{
							$fieldValue = implode(', ', json_decode($fieldValue));
						}
						$results_arr[] = $fieldValue;
					}
				}
				$results_arr[] = JHtml::_('date', $r->from_date, $config->date_format);
				$results_arr[] = JHtml::_('date', $r->to_date, $config->date_format);
				$results_arr[] = $r->subscription_status;
				$results_arr[] = round($r->discount_amount, 2);
				$results_arr[] = round($r->tax_amount, 2);
				$results_arr[] = round($r->gross_amount, 2);
				$results_arr[] = $pluginTitles[$r->payment_method];
				$results_arr[] = $r->transaction_id;
				$results_arr[] = $r->membership_id;
				$csv_output .= "\n\"" . implode("\",\"", $results_arr) . "\"";
			}
			$csv_output .= "\n";
			if (ereg('Opera(/| )([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT']))
			{
				$UserBrowser = "Opera";
			}
			elseif (ereg('MSIE ([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT']))
			{
				$UserBrowser = "IE";
			}
			else
			{
				$UserBrowser = '';
			}
			$mime_type = ($UserBrowser == 'IE' || $UserBrowser == 'Opera') ? 'application/octetstream' : 'application/octet-stream';
			$filename  = "subscribers_list";
			@ob_end_clean();
			ob_start();
			header('Content-Type: ' . $mime_type);
			header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
			if ($UserBrowser == 'IE')
			{
				header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
			}
			else
			{
				header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
				header('Pragma: no-cache');
			}
			print $csv_output;
			exit();
		}
	}


	/**
	 * Generate CSV Template use to import subscribers into the system
	 */
	public function csv_import_template()
	{
		$db     = JFactory::getDbo();
		$query  = $db->getQuery(true);
		$query->select('name')
			->from('#__osmembership_fields')
			->where('published = 1')
			->order('ordering');
		$db->setQuery($query);
		$rowFields = $db->loadObjectList();

		$results_arr   = array();
		$results_arr[] = 'plan';
		$results_arr[] = 'username';
		$results_arr[] = 'password';
		foreach ($rowFields as $rowField)
		{
			$results_arr[] = $rowField->name;
		}
		$results_arr[] = 'payment_date';
		$results_arr[] = 'from_date';
		$results_arr[] = 'to_date';
		$results_arr[] = 'published';
		$results_arr[] = 'amount';
		$results_arr[] = 'tax_amount';
		$results_arr[] = 'discount_amount';
		$results_arr[] = 'gross_amount';
		$results_arr[] = 'payment_method';
		$results_arr[] = 'transaction_id';

		$csv_output = "\"" . implode("\",\"", $results_arr) . "\"";

		$results_arr   = array();
		$results_arr[] = '6 Months Membership';
		$results_arr[] = 'tuanpn';
		$results_arr[] = 'tuanpn';
		foreach ($rowFields as $rowField)
		{
			if ($rowField->name == 'first_name')
			{
				$results_arr[] = 'Tuan';
			}
			elseif ($rowField->name == 'last_name')
			{
				$results_arr[] = 'Pham Ngoc';
			}
			elseif ($rowField->name == 'email')
			{
				$results_arr[] = 'tuanpn@joomdonation.com';
			}
			else
			{
				$results_arr[] = 'sample_data_for_'.$rowField->name;
			}
		}
		$results_arr[] = '2014-12-24';
		$results_arr[] = '2014-12-24';
		$results_arr[] = '2015-06-24';
		$results_arr[] = '1';
		$results_arr[] = '100';
		$results_arr[] = '10';
		$results_arr[] = '0';
		$results_arr[] = '110';
		$results_arr[] = 'os_paypal';
		$results_arr[] = 'TR4756RUI78465';

		$csv_output .= "\n\"" . implode("\",\"", $results_arr) . "\"";

		$csv_output .= "\n";
		if (ereg('Opera(/| )([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT']))
		{
			$UserBrowser = "Opera";
		}
		elseif (ereg('MSIE ([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT']))
		{
			$UserBrowser = "IE";
		}
		else
		{
			$UserBrowser = '';
		}
		$mime_type = ($UserBrowser == 'IE' || $UserBrowser == 'Opera') ? 'application/octetstream' : 'application/octet-stream';
		$filename  = "sample_csv";
		@ob_end_clean();
		ob_start();
		header('Content-Type: ' . $mime_type);
		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		if ($UserBrowser == 'IE')
		{
			header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
		}
		else
		{
			header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
			header('Pragma: no-cache');
		}
		print $csv_output;
		exit();
	}
}
