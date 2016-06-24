<?php

/**
 * @version            2.0.4
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2015 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */
class EventbookingHelperData
{
	/**
	 * Get day name from given day number
	 *
	 * @param $dayNumber
	 *
	 * @return mixed
	 */
	public static function getDayName($dayNumber)
	{
		static $days;
		if ($days == null)
		{
			$days = array(
				JText::_('EB_SUNDAY'),
				JText::_('EB_MONDAY'),
				JText::_('EB_TUESDAY'),
				JText::_('EB_WEDNESDAY'),
				JText::_('EB_THURSDAY'),
				JText::_('EB_FRIDAY'),
				JText::_('EB_SATURDAY')
			);
		}
		$i = $dayNumber % 7;

		return $days[$i];
	}

	/**
	 * Get day name from day number in mini calendar
	 *
	 * @param $dayNumber
	 *
	 * @return mixed
	 */
	public static function getDayNameMini($dayNumber)
	{
		static $daysMini = null;
		if ($daysMini === null)
		{
			$daysMini    = array();
			$daysMini[0] = JText::_('EB_MINICAL_SUNDAY');
			$daysMini[1] = JText::_('EB_MINICAL_MONDAY');
			$daysMini[2] = JText::_('EB_MINICAL_TUESDAY');
			$daysMini[3] = JText::_('EB_MINICAL_WEDNESDAY');
			$daysMini[4] = JText::_('EB_MINICAL_THURSDAY');
			$daysMini[5] = JText::_('EB_MINICAL_FRIDAY');
			$daysMini[6] = JText::_('EB_MINICAL_SATURDAY');
		}
		$i = $dayNumber % 7; //
		return $daysMini[$i];
	}

	/**
	 * Get day name HTML code for a given day
	 *
	 * @param int  $dayNumber
	 * @param bool $colored
	 *
	 * @return string
	 */
	public static function getDayNameHtml($dayNumber, $colored = false)
	{
		$i = $dayNumber % 7; // modulo 7
		if ($i == '0' && $colored === true)
		{
			$dayName = '<span class="sunday">' . self::getDayName($i) . '</span>';
		}
		else if ($i == '6' && $colored === true)
		{
			$dayName = '<span class="saturday">' . self::getDayName($i) . '</span>';
		}
		else
		{
			$dayName = self::getDayName($i);
		}

		return $dayName;
	}

	/**
	 * Get day name HTML code for a given day
	 *
	 * @param int  $dayNumber
	 * @param bool $colored
	 *
	 * @return string
	 */
	public static function getDayNameHtmlMini($dayNumber, $colored = false)
	{
		$i = $dayNumber % 7; // modulo 7
		if ($i == '0' && $colored === true)
		{
			$dayName = '<span class="sunday">' . self::getDayNameMini($i) . '</span>';
		}
		else if ($i == '6' && $colored === true)
		{
			$dayName = '<span class="saturday">' . self::getDayNameMini($i) . '</span>';
		}
		else
		{
			$dayName = self::getDayNameMini($i);
		}

		return $dayName;
	}

	/**
	 * Build the data used for rendering calendar
	 *
	 * @param $rows
	 * @param $year
	 * @param $month
	 *
	 * @return array
	 */
	public static function getCalendarData($rows, $year, $month, $mini = false)
	{
		$rowCount         = count($rows);
		$data             = array();
		$data['startday'] = $startDay = (int) EventbookingHelper::getConfigValue('calendar_start_date');
		$data['year']     = $year;
		$data['month']    = $month;
		$data["daynames"] = array();
		$data["dates"]    = array();
		$month            = intval($month);
		if ($month <= '9')
		{
			$month = '0' . $month;
		}
		// get days in week
		for ($i = 0; $i < 7; $i++)
		{
			if ($mini)
			{
				$data["daynames"][$i] = self::getDayNameMini(($i + $startDay) % 7);
			}
			else
			{
				$data["daynames"][$i] = self::getDayName(($i + $startDay) % 7);
			}
		}
		//Start days
		$start = ((date('w', mktime(0, 0, 0, $month, 1, $year)) - $startDay + 7) % 7);
		//Previous month
		$priorMonth = $month - 1;
		$priorYear  = $year;
		if ($priorMonth <= 0)
		{
			$priorMonth += 12;
			$priorYear -= 1;
		}
		$dayCount = 0;
		for ($a = $start; $a > 0; $a--)
		{
			$data["dates"][$dayCount]                 = array();
			$data["dates"][$dayCount]["monthType"]    = "prior";
			$data["dates"][$dayCount]["month"]        = $priorMonth;
			$data["dates"][$dayCount]["year"]         = $priorYear;
			$data["dates"][$dayCount]['countDisplay'] = 0;
			$dayCount++;
		}
		sort($data["dates"]);
		$todayDate  = JFactory::getDate('+0 seconds');
		$todayDay   = $todayDate->format('d');
		$todayMonth = $todayDate->format('m');
		$todayYear  = $todayDate->format('Y');


		//Current month
		$end = date('t', mktime(0, 0, 0, ($month + 1), 0, $year));
		for ($d = 1; $d <= $end; $d++)
		{
			$data["dates"][$dayCount]                 = array();
			$data["dates"][$dayCount]['countDisplay'] = 0;
			$data["dates"][$dayCount]["monthType"]    = "current";
			$data["dates"][$dayCount]["month"]        = $month;
			$data["dates"][$dayCount]["year"]         = $year;
			if ($month == $todayMonth && $year == $todayYear && $d == $todayDay)
			{
				$data["dates"][$dayCount]["today"] = true;
			}
			else
			{
				$data["dates"][$dayCount]["today"] = false;
			}
			$data["dates"][$dayCount]['d']      = $d;
			$data["dates"][$dayCount]['events'] = array();
			if ($rowCount > 0)
			{
				foreach ($rows as $row)
				{
					$date_of_event = explode('-', $row->event_date);
					$date_of_event = (int) $date_of_event[2];
					if ($d == $date_of_event)
					{
						$i                                      = count($data["dates"][$dayCount]['events']);
						$data["dates"][$dayCount]['events'][$i] = $row;
					}
				}
			}

			$dayCount++;
		}

		//Following month
		$days        = (7 - date('w', mktime(0, 0, 0, $month + 1, 1, $year)) + $startDay) % 7;
		$followMonth = $month + 1;
		$followYear  = $year;
		if ($followMonth > 12)
		{
			$followMonth -= 12;
			$followYear += 1;
		}
		$data["followingMonth"] = array();
		for ($d = 1; $d <= $days; $d++)
		{
			$data["dates"][$dayCount]                 = array();
			$data["dates"][$dayCount]["monthType"]    = "following";
			$data["dates"][$dayCount]["month"]        = $followMonth;
			$data["dates"][$dayCount]["year"]         = $followYear;
			$data["dates"][$dayCount]['countDisplay'] = 0;
			$dayCount++;
		}

		return $data;
	}

	/**
	 * Calculate the discounted prices for events
	 *
	 * @param $rows
	 */
	public static function calculateDiscount($rows)
	{
		$db       = JFactory::getDbo();
		$query    = $db->getQuery(true);
		$user     = JFactory::getUser();
		$config   = EventbookingHelper::getConfig();
		$nullDate = $db->getNullDate();
		$userId   = $user->get('id');
		for ($i = 0, $n = count($rows); $i < $n; $i++)
		{
			$row = $rows[$i];

			if ($userId > 0)
			{
				$query->select('COUNT(id)')
					->from('#__eb_registrants')
					->where('user_id = ' . $userId)
					->where('event_id = ' . $row->id)
					->where('(published=1 OR (payment_method LIKE "os_offline%" AND published NOT IN (2,3)))');
				$db->setQuery($query);
				$row->user_registered = $db->loadResult();
				$query->clear();
			}

			// Calculate discount price
			if ($config->show_discounted_price)
			{
				$discount = 0;
				if (($row->early_bird_discount_date != $nullDate) && ($row->date_diff >= 0))
				{
					if ($row->early_bird_discount_type == 1)
					{
						$discount += $row->individual_price * $row->early_bird_discount_amount / 100;
					}
					else
					{
						$discount += $row->early_bird_discount_amount;
					}
				}
				if ($userId > 0)
				{
					$discountRate = EventbookingHelper::calculateMemberDiscount($row->discount_amounts, $row->discount_groups);
					if ($discountRate > 0)
					{
						if ($row->discount_type == 1)
						{
							$discount += $row->individual_price * $discountRate / 100;
						}
						else
						{
							$discount += $discountRate;
						}
					}
				}

				$row->discounted_price = $row->individual_price - $discount;
			}

			$lateFee = 0;
			if (($row->late_fee_date != $nullDate) && $row->late_fee_date_diff >= 0 && $row->late_fee_amount > 0)
			{
				if ($row->late_fee_type == 1)
				{
					$lateFee = $row->individual_price * $row->late_fee_amount / 100;
				}
				else
				{

					$lateFee = $row->late_fee_amount;
				}
			}

			$row->late_fee = $lateFee;
		}
	}

	/**
	 * Get parent categories of the given category
	 *
	 * @param $categoryId
	 *
	 * @return array
	 */
	public static function getParentCategories($categoryId)
	{
		$db          = JFactory::getDbo();
		$parents     = array();
		$fieldSuffix = EventbookingHelper::getFieldSuffix();
		while (true)
		{
			$sql = "SELECT id, name'.$fieldSuffix.' AS name, parent FROM #__eb_categories WHERE id = " . $categoryId . " AND published=1";
			$db->setQuery($sql);
			$row = $db->loadObject();
			if ($row)
			{
				$parents[]  = $row;
				$categoryId = $row->parent;
			}
			else
			{
				break;
			}
		}

		return $parents;
	}

	public static function getCategoriesBreadcrumb($id, $parentId)
	{
		$db          = JFactory::getDbo();
		$query       = $db->getQuery(true);
		$fieldSuffix = EventbookingHelper::getFieldSuffix();
		$query->select('id, name' . $fieldSuffix . ' AS name, parent')->from('#__eb_categories')->where('published=1');
		$db->setQuery($query);
		$categories = $db->loadObjectList('id');
		$paths      = array();
		while ($id != $parentId)
		{
			if (isset($categories[$id]))
			{
				$paths[] = $categories[$id];
				$id      = $categories[$id]->parent;
			}
			else
			{
				break;
			}
		}

		return $paths;
	}

	/**
	 * Decode custom fields data and store it for each event record
	 *
	 * @param $items
	 */
	public static function prepareCustomFieldsData($items)
	{
		$params       = new JRegistry();
		$xml          = JFactory::getXML(JPATH_ROOT . '/components/com_eventbooking/fields.xml');
		$fields       = $xml->fields->fieldset->children();
		$customFields = array();
		foreach ($fields as $field)
		{
			$name                  = $field->attributes()->name;
			$label                 = JText::_($field->attributes()->label);
			$customFields["$name"] = $label;
		}
		for ($i = 0, $n = count($items); $i < $n; $i++)
		{
			$item = $items[$i];
			$params->loadString($item->custom_fields, 'JSON');
			$paramData = array();
			foreach ($customFields as $name => $label)
			{
				$paramData[$name]['title'] = $label;
				$paramData[$name]['value'] = $params->get($name);
			}

			$item->paramData = $paramData;
		}
	}


	public static function csvExport($rows, $config, $rowFields, $fieldValues, $groupNames)
	{
		if (count($rows))
		{
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
			$filename  = "registrants_list";
			header('Content-Encoding: UTF-8');
			header('Content-Type: ' . $mime_type . ' ;charset=UTF-8');
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
			$fp = fopen('php://output', 'w');
			fwrite($fp, "\xEF\xBB\xBF");
			$delimiter = $config->csv_delimiter ? $config->csv_delimiter : ',';
			$fields     = array();
			$fields[]   = JText::_('EB_EVENT');
			if ($config->show_event_date)
			{
				$fields[] = JText::_('EB_EVENT_DATE');
			}
			if (count($rowFields))
			{
				foreach ($rowFields as $rowField)
				{
					$fields[] = $rowField->title;
				}
			}
			$fields[] = JText::_('EB_NUMBER_REGISTRANTS');
			$fields[] = JText::_('EB_AMOUNT');
			$fields[] = JText::_('EB_DISCOUNT_AMOUNT');
			$fields[] = JText::_('EB_LATE_FEE');
			$fields[] = JText::_('EB_TAX');
			$fields[] = JText::_('EB_GROSS_AMOUNT');
			if ($config->activate_deposit_feature)
			{
				$fields[] = JText::_('EB_DEPOSIT_AMOUNT');
				$fields[] = JText::_('EB_DUE_AMOUNT');
			}
			if ($config->show_coupon_code_in_registrant_list)
			{
				$fields[] = JText::_('EB_COUPON');
			}
			$fields[] = JText::_('EB_REGISTRATION_DATE');
			$fields[] = JText::_('EB_TRANSACTION_ID');
			$fields[] = JText::_('EB_PAYMENT_STATUS');
			$fields[] = JText::_('EB_ID');
			fputcsv($fp, $fields, $delimiter);
			foreach ($rows as $r)
			{

				$fields   = array();
				$fields[] = $r->event_title;
				if ($config->show_event_date)
				{
					$fields[] = JHtml::_('date', $r->event_date, $config->date_format, null);
				}
				foreach ($rowFields as $rowField)
				{
					if ($rowField->name == 'first_name')
					{
						if ($r->is_group_billing)
						{
							$fields[] = $r->first_name . ' ' . JText::_('EB_GROUP_BILLING');
						}
						elseif ($r->group_id > 0)
						{
							$fields[] = $r->first_name . ' ' . JText::_('EB_GROUP') . $groupNames[$r->group_id];
						}
						else
						{
							$fields[] = $r->first_name;
						}
						continue;
					}
					if ($rowField->is_core)
					{
						$fields[] = @$r->{$rowField->name};
					}
					else
					{
						$fieldValue = @$fieldValues[$r->id][$rowField->id];
						if (is_string($fieldValue) && is_array(json_decode($fieldValue)))
						{
							$fieldValue = implode(', ', json_decode($fieldValue));
						}
						$fields[] = $fieldValue;
					}
				}
				$fields[] = $r->number_registrants;
				$fields[] = EventbookingHelper::formatAmount($r->total_amount, $config);
				$fields[] = EventbookingHelper::formatAmount($r->discount_amount, $config);
				$fields[] = EventbookingHelper::formatAmount($r->late_fee, $config);
				$fields[] = EventbookingHelper::formatAmount($r->tax_amount, $config);
				$fields[] = EventbookingHelper::formatAmount($r->amount, $config);
				if ($config->activate_deposit_feature)
				{
					if ($r->deposit_amount > 0)
					{
						$fields[] = EventbookingHelper::formatAmount($r->deposit_amount, $config);
						$fields[] = EventbookingHelper::formatAmount($r->amount - $r->deposit_amount, $config);
					}
					else
					{
						$fields[] = '';
						$fields[] = '';
					}
				}

				if ($config->show_coupon_code_in_registrant_list)
				{
					$fields[] = $r->coupon_code;
				}

				$fields[] = JHtml::_('date', $r->register_date, $config->date_format);
				$fields[] = $r->transaction_id;
				if ($r->published)
				{
					$fields[] = 'Paid';
				}
				else
				{
					$fields[] = 'Not Paid';
				}
				$fields[] = $r->id;
				fputcsv($fp, $fields, $delimiter);
			}
			fclose($fp);
		}
		JFactory::getApplication()->close();
	}
}