<?php
/**
 * @version            2.0.4
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2015 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die;

class EventbookingHelper
{

	/**
	 * Return the current installed version
	 */
	public static function getInstalledVersion()
	{
		return '2.0.4';
	}

	/**
	 * Get configuration data and store in config object
	 *
	 * @return object
	 */
	public static function getConfig()
	{
		static $config;
		if (!$config)
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_eventbooking/libraries/rad/config/config.php';
			$config = new RADConfig('#__eb_configs');
		}

		return $config;
	}

	/**
	 * Get specify config value
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	public static function getConfigValue($key, $default = null)
	{
		$config = self::getConfig();

		return $config->get($key, $default);
	}

	/**
	 * Get component settings from json config file
	 *
	 * @param $appName
	 *
	 * @return array
	 */
	public static function getComponentSettings($appName)
	{
		$settings = json_decode(file_get_contents(JPATH_ADMINISTRATOR . '/components/com_eventbooking/config.json'), true);

		return $settings[strtolower($appName)];
	}

	/**
	 * We only need to generate invoice for paid events only
	 *
	 * @param $row
	 *
	 * @return bool
	 */
	public static function needInvoice($row)
	{
		$config = self::getConfig();
		if ($config->multiple_booking)
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('SUM(total_amount)')
				->from('#__eb_registrants')
				->where('id=' . $row->id . ' OR cart_id=' . $row->id);
			$db->setQuery($query);
			$totalAmount = $db->loadResult();
			if ($totalAmount > 0)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			if ($row->amount > 0 || $row->total_amount > 0)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}


	/**
	 * Get page params of the givem view
	 *
	 * @param $active
	 * @param $views
	 *
	 * @return JRegistry
	 */
	public static function getViewParams($active, $views)
	{
		if ($active && isset($active->query['view']) && in_array($active->query['view'], $views))
		{
			return $active->params;
		}

		return new JRegistry();
	}

	/**
	 *
	 * Apply some fixes for request data
	 *
	 * @return void
	 */
	public static function prepareRequestData()
	{
		//Remove cookie vars from request data
		$cookieVars = array_keys($_COOKIE);
		if (count($cookieVars))
		{
			foreach ($cookieVars as $key)
			{
				if (!isset($_POST[$key]) && !isset($_GET[$key]))
				{
					unset($_REQUEST[$key]);
				}
			}
		}
		if (isset($_REQUEST['start']) && !isset($_REQUEST['limitstart']))
		{
			$_REQUEST['limitstart'] = $_REQUEST['start'];
		}
		if (!isset($_REQUEST['limitstart']))
		{
			$_REQUEST['limitstart'] = 0;
		}
	}

	/**
	 * Get the email messages used for sending emails or displaying in the form
	 *
	 * @return stdClass
	 */
	public static function getMessages()
	{
		static $message;
		if (!$message)
		{
			$message = new stdClass();
			$db      = JFactory::getDbo();
			$query   = $db->getQuery(true);
			$query->select('*')->from('#__eb_messages');
			$db->setQuery($query);
			$rows = $db->loadObjectList();
			for ($i = 0, $n = count($rows); $i < $n; $i++)
			{
				$row           = $rows[$i];
				$key           = $row->message_key;
				$value         = stripslashes($row->message);
				$message->$key = $value;
			}
		}

		return $message;
	}

	/**
	 * Get field suffix used in sql query
	 *
	 * @param null $activeLanguage
	 *
	 * @return string
	 */
	public static function getFieldSuffix($activeLanguage = null)
	{
		$prefix = '';
		if (JLanguageMultilang::isEnabled())
		{
			if (!$activeLanguage || $activeLanguage == '*')
			{
				$activeLanguage = JFactory::getLanguage()->getTag();
			}
			if ($activeLanguage != self::getDefaultLanguage())
			{
				$prefix = '_' . substr($activeLanguage, 0, 2);
			}
		}

		return $prefix;
	}

	/**
	 * Get list of language uses on the site
	 *
	 * @return array
	 */
	public static function getLanguages()
	{
		$db      = JFactory::getDbo();
		$query   = $db->getQuery(true);
		$default = self::getDefaultLanguage();
		$query->select('lang_id, lang_code, title, `sef`')
			->from('#__languages')
			->where('published = 1')
			->where('lang_code != "' . $default . '"')
			->order('ordering');
		$db->setQuery($query);
		$languages = $db->loadObjectList();

		return $languages;
	}

	/**
	 * Get front-end default language
	 *
	 * @return string
	 */
	public static function getDefaultLanguage()
	{
		$params = JComponentHelper::getParams('com_languages');

		return $params->get('site', 'en-GB');
	}

	/**
	 * Get sef of current language
	 *
	 * @return mixed
	 */
	public static function addLangLinkForAjax()
	{
		if (JLanguageMultilang::isEnabled())
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$tag   = JFactory::getLanguage()->getTag();
			$query->select('`sef`')
				->from('#__languages')
				->where('published = 1')
				->where('lang_code=' . $db->quote($tag));
			$db->setQuery($query, 0, 1);
			$langLink = '&lang=' . $db->loadResult();
		}
		else
		{
			$langLink = '';
		}

		JFactory::getDocument()->addScriptDeclaration(
			'var langLinkForAjax="' . $langLink . '";'
		);
	}

	/**
	 * This function is used to check to see whether we need to update the database to support multilingual or not
	 *
	 * @return boolean
	 */
	public static function isSynchronized()
	{
		$db             = JFactory::getDbo();
		$fields         = array_keys($db->getTableColumns('#__eb_categories'));
		$extraLanguages = self::getLanguages();
		if (count($extraLanguages))
		{
			foreach ($extraLanguages as $extraLanguage)
			{
				$prefix = $extraLanguage->sef;
				if (!in_array('name_' . $prefix, $fields))
				{
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Synchronize Events Booking database to support multilingual
	 */
	public static function setupMultilingual()
	{
		$db        = JFactory::getDbo();
		$languages = self::getLanguages();
		if (count($languages))
		{
			$categoryTableFields = array_keys($db->getTableColumns('#__eb_categories'));
			$eventTableFields    = array_keys($db->getTableColumns('#__eb_events'));
			$fieldTableFields    = array_keys($db->getTableColumns('#__eb_fields'));
			foreach ($languages as $language)
			{
				$prefix = $language->sef;
				if (!in_array('name_' . $prefix, $categoryTableFields))
				{
					$fieldName = 'name_' . $prefix;
					$sql       = "ALTER TABLE  `#__eb_categories` ADD  `$fieldName` VARCHAR( 255 );";
					$db->setQuery($sql);
					$db->execute();

					$fieldName = 'alias_' . $prefix;
					$sql       = "ALTER TABLE  `#__eb_categories` ADD  `$fieldName` VARCHAR( 255 );";
					$db->setQuery($sql);
					$db->execute();

					$fieldName = 'description_' . $prefix;
					$sql       = "ALTER TABLE  `#__eb_categories` ADD  `$fieldName` TEXT NULL;";
					$db->setQuery($sql);
					$db->execute();
				}

				if (!in_array('title_' . $prefix, $eventTableFields))
				{
					$fieldName = 'title_' . $prefix;
					$sql       = "ALTER TABLE  `#__eb_events` ADD  `$fieldName` VARCHAR( 255 );";
					$db->setQuery($sql);
					$db->execute();

					$fieldName = 'alias_' . $prefix;
					$sql       = "ALTER TABLE  `#__eb_events` ADD  `$fieldName` VARCHAR( 255 );";
					$db->setQuery($sql);
					$db->execute();

					$fieldName = 'short_description_' . $prefix;
					$sql       = "ALTER TABLE  `#__eb_events` ADD  `$fieldName` TEXT NULL;";
					$db->setQuery($sql);
					$db->execute();

					$fieldName = 'description_' . $prefix;
					$sql       = "ALTER TABLE  `#__eb_events` ADD  `$fieldName` TEXT NULL;";
					$db->setQuery($sql);
					$db->execute();

					$fieldName = 'meta_keywords_' . $prefix;
					$sql       = "ALTER TABLE  `#__eb_events` ADD  `$fieldName` VARCHAR( 255 );";
					$db->setQuery($sql);
					$db->execute();

					$fieldName = 'meta_description_' . $prefix;
					$sql       = "ALTER TABLE  `#__eb_events` ADD  `$fieldName` VARCHAR( 255 );";
					$db->setQuery($sql);
					$db->execute();

					$fieldName = 'user_email_body_' . $prefix;
					$sql       = "ALTER TABLE  `#__eb_events` ADD  `$fieldName` TEXT NULL;";
					$db->setQuery($sql);
					$db->execute();

					$fieldName = 'user_email_body_offline_' . $prefix;
					$sql       = "ALTER TABLE  `#__eb_events` ADD  `$fieldName` TEXT NULL;";
					$db->setQuery($sql);
					$db->execute();

					$fieldName = 'thanks_message_' . $prefix;
					$sql       = "ALTER TABLE  `#__eb_events` ADD  `$fieldName` TEXT NULL;";
					$db->setQuery($sql);
					$db->execute();

					$fieldName = 'thanks_message_offline_' . $prefix;
					$sql       = "ALTER TABLE  `#__eb_events` ADD  `$fieldName` TEXT NULL;";
					$db->setQuery($sql);
					$db->execute();

					$fieldName = 'registration_approved_email_body_' . $prefix;
					$sql       = "ALTER TABLE  `#__eb_events` ADD  `$fieldName` TEXT NULL;";
					$db->setQuery($sql);
					$db->execute();
				}

				if (!in_array('title_' . $prefix, $fieldTableFields))
				{
					$fieldName = 'title_' . $prefix;
					$sql       = "ALTER TABLE  `#__eb_fields` ADD  `$fieldName` VARCHAR( 255 );";
					$db->setQuery($sql);
					$db->execute();

					$fieldName = 'description_' . $prefix;
					$sql       = "ALTER TABLE  `#__eb_fields` ADD  `$fieldName` TEXT NULL;";
					$db->setQuery($sql);
					$db->execute();

					$fieldName = 'values_' . $prefix;
					$sql       = "ALTER TABLE  `#__eb_fields` ADD  `$fieldName` TEXT NULL;";
					$db->setQuery($sql);
					$db->execute();

					$fieldName = 'default_values_' . $prefix;
					$sql       = "ALTER TABLE  `#__eb_fields` ADD  `$fieldName` TEXT NULL;";
					$db->setQuery($sql);
					$db->execute();

					$fieldName = 'depend_on_options_' . $prefix;
					$sql       = "ALTER TABLE  `#__eb_fields` ADD  `$fieldName` TEXT NULL;";
					$db->setQuery($sql);
					$db->execute();
				}
			}
		}
	}

	/**
	 * Get language use for re-captcha
	 *
	 * @return string
	 */
	public static function getRecaptchaLanguage()
	{
		$language  = JFactory::getLanguage();
		$tag       = explode('-', $language->getTag());
		$tag       = $tag[0];
		$available = array('en', 'pt', 'fr', 'de', 'nl', 'ru', 'es', 'tr');
		if (in_array($tag, $available))
		{
			return "lang : '" . $tag . "',";
		}
	}


	/**
	 * Buld tags array to use to replace the tags use in email & messages
	 *
	 * @param $row
	 * @param $form
	 * @param $event
	 * @param $config
	 *
	 * @return array
	 */
	public static function buildTags($row, $form, $event, $config, $loadCss = true)
	{
		$db       = JFactory::getDbo();
		$query    = $db->getQuery(true);
		$replaces = array();

		// Event information
		if ($config->multiple_booking)
		{
			$sql = 'SELECT event_id FROM #__eb_registrants WHERE id=' . $row->id . ' OR cart_id=' . $row->id . ' ORDER BY id';
			$db->setQuery($sql);
			$eventIds    = $db->loadColumn();
			$fieldSuffix = EventbookingHelper::getFieldSuffix();
			$sql         = 'SELECT title' . $fieldSuffix . ' AS title FROM #__eb_events WHERE id IN (' . implode(',', $eventIds) . ') ORDER BY FIND_IN_SET(id, "' . implode(',', $eventIds) .
				'")';
			$db->setQuery($sql);
			$eventTitles = $db->loadColumn();
			$eventTitle  = implode(', ', $eventTitles);
		}
		else
		{
			$eventTitle = $event->title;
		}
		$replaces['date']              = date($config->date_format);
		$replaces['event_title']       = $eventTitle;
		$replaces['event_date']        = JHtml::_('date', $event->event_date, $config->event_date_format, null);
		$replaces['event_end_date']    = JHtml::_('date', $event->event_end_date, $config->event_date_format, null);
		$replaces['short_description'] = $event->short_description;
		$replaces['description']       = $event->description;

		// Event custom fields
		if ($config->event_custom_field && file_exists(JPATH_ROOT . '/components/com_eventbooking/fields.xml'))
		{
			EventbookingHelperData::prepareCustomFieldsData(array($event));
			foreach($event->paramData as $customFieldName => $param)
			{
				$replaces[$customFieldName] = $param['value'];
			}
		}

		// Form fields
		$fields = $form->getFields();
		foreach ($fields as $field)
		{
			if ($field->hideOnDisplay)
			{
				$fieldValue = '';
			}
			else
			{
				if (is_string($field->value) && is_array(json_decode($field->value)))
				{
					$fieldValue = implode(', ', json_decode($field->value));
				}
				else
				{
					$fieldValue = $field->value;
				}
			}
			$replaces[$field->name] = $fieldValue;
		}

		if (isset($replaces['last_name']))
		{
			$replaces['name'] = $replaces['first_name'] . ' ' . $replaces['last_name'];
		}
		else
		{
			$replaces['name'] = $replaces['first_name'];
		}

		if ($row->coupon_id)
		{
			$query->clear();
			$query->select('a.code')
				->from('#__eb_coupons AS a')
				->innerJoin('#__eb_registrants AS b ON a.id = b.coupon_id')
				->where('b.id=' . $row->id);
			$db->setQuery($query);
			$data['couponCode'] = $db->loadResult();
		}
		else
		{
			$data['couponCode'] = '';
		}
		if ($config->multiple_booking)
		{
			//Amount calculation
			$sql = 'SELECT SUM(total_amount) FROM #__eb_registrants WHERE id=' . $row->id . ' OR cart_id=' . $row->id;
			$db->setQuery($sql);
			$totalAmount = $db->loadResult();

			$sql = 'SELECT SUM(tax_amount) FROM #__eb_registrants WHERE id=' . $row->id . ' OR cart_id=' . $row->id;
			$db->setQuery($sql);
			$taxAmount = $db->loadResult();

			$sql = 'SELECT SUM(payment_processing_fee) FROM #__eb_registrants WHERE id=' . $row->id . ' OR cart_id=' . $row->id;
			$db->setQuery($sql);
			$paymentProcessingFee = $db->loadResult();

			$sql = 'SELECT SUM(discount_amount) FROM #__eb_registrants WHERE id=' . $row->id . ' OR cart_id=' . $row->id;
			$db->setQuery($sql);
			$discountAmount = $db->loadResult();

			$sql = 'SELECT SUM(late_fee) FROM #__eb_registrants WHERE id=' . $row->id . ' OR cart_id=' . $row->id;
			$db->setQuery($sql);
			$lateFee = $db->loadResult();

			$amount         = $totalAmount - $discountAmount + $paymentProcessingFee + $taxAmount + $lateFee;

			$replaces['total_amount']           = EventbookingHelper::formatCurrency($totalAmount, $config, $event->currency_symbol);
			$replaces['tax_amount']             = EventbookingHelper::formatCurrency($taxAmount, $config, $event->currency_symbol);
			$replaces['discount_amount']        = EventbookingHelper::formatCurrency($discountAmount, $config, $event->currency_symbol);
			$replaces['late_fee']        = EventbookingHelper::formatCurrency($lateFee, $config, $event->currency_symbol);
			$replaces['payment_processing_fee'] = EventbookingHelper::formatCurrency($paymentProcessingFee, $config, $event->currency_symbol);
			$replaces['amount']                 = EventbookingHelper::formatCurrency($amount, $config, $event->currency_symbol);

			$replaces['amt_total_amount']           = $totalAmount;
			$replaces['amt_tax_amount']             = $taxAmount;
			$replaces['amt_discount_amount']        = $discountAmount;
			$replaces['amt_late_fee']               = $lateFee;
			$replaces['amt_amount']                 = $amount;
			$replaces['amt_payment_processing_fee'] = $paymentProcessingFee;
		}
		else
		{
			$replaces['total_amount']           = EventbookingHelper::formatCurrency($row->total_amount, $config, $event->currency_symbol);
			$replaces['tax_amount']             = EventbookingHelper::formatCurrency($row->tax_amount, $config, $event->currency_symbol);
			$replaces['discount_amount']        = EventbookingHelper::formatCurrency($row->discount_amount, $config, $event->currency_symbol);
			$replaces['late_fee']               = EventbookingHelper::formatCurrency($row->late_fee, $config, $event->currency_symbol);
			$replaces['payment_processing_fee'] = EventbookingHelper::formatCurrency($row->payment_processing_fee, $config, $event->currency_symbol);
			$replaces['amount']                 = EventbookingHelper::formatCurrency($row->amount, $config, $event->currency_symbol);
		}

		// Add support for location tag
		$query->clear();
		$query->select('a.*')
			->from('#__eb_locations AS a')
			->innerJoin('#__eb_events AS b ON a.id=b.location_id')
			->where('b.id=' . $row->event_id);

		$db->setQuery($query);
		$rowLocation = $db->loadObject();
		if ($rowLocation)
		{
			$locationInformation = array();
			if ($rowLocation->address)
			{
				$locationInformation[] = $rowLocation->address;
			}
			if ($rowLocation->city)
			{
				$locationInformation[] = $rowLocation->city;
			}
			if ($rowLocation->state)
			{
				$locationInformation[] = $rowLocation->state;
			}
			if ($rowLocation->zip)
			{
				$locationInformation[] = $rowLocation->zip;
			}
			if ($rowLocation->country)
			{
				$locationInformation[] = $rowLocation->country;
			}
			$replaces['location'] = $rowLocation->name . ' (' . implode(', ', $locationInformation) . ')';
		}
		else
		{
			$replaces['location'] = '';
		}

		// Registration record related tags
		$replaces['number_registrants'] = $row->number_registrants;
		$replaces['invoice_number']     = $row->invoice_number;
		$replaces['invoice_number']     = EventbookingHelper::formatInvoiceNumber($row->invoice_number, $config);
		$replaces['transaction_id']     = $row->transaction_id;
		$method                         = os_payments::loadPaymentMethod($row->payment_method);
		if ($method)
		{
			$replaces['payment_method'] = JText::_($method->title);
		}
		else
		{
			$replaces['payment_method'] = '';
		}


		// Registration detail tags
		$replaces['registration_detail'] = self::getEmailContent($config, $row, $loadCss, $form);

		// Cancel link
		$query->clear();
		$query->select('enable_cancel_registration')
			->from('#__eb_events')
			->where('id = ' . $row->event_id);
		$db->setQuery($query);
		$enableCancel = $db->loadResult();
		if ($enableCancel)
		{
			$Itemid = JRequest::getInt('Itemid', 0);
			if (!$Itemid)
			{
				$Itemid = self::getItemid();
			}
			$replaces['cancel_registration_link'] = self::getSiteUrl() . 'index.php?option=com_eventbooking&task=registrant.cancel&cancel_code=' . $row->registration_code . '&Itemid=' . $Itemid;
		}
		else
		{
			$replaces['cancel_registration_link'] = '';
		}

		return $replaces;
	}

	/**
	 * Calculate fees use for individual registration
	 *
	 * @param $event
	 * @param $form
	 * @param $data
	 * @param $config
	 * @param $paymentMethod
	 *
	 * @return array
	 */
	public static function calculateIndividualRegistrationFees($event, $form, $data, $config, $paymentMethod = null)
	{
		$fees                  = array();
		$user                  = JFactory::getUser();
		$db                    = JFactory::getDbo();
		$query                 = $db->getQuery(true);
		$couponCode            = isset($data['coupon_code']) ? $data['coupon_code'] : '';
		$totalAmount           = $event->individual_price + $form->calculateFee();
		$discountAmount        = 0;
		$fees['discount_rate'] = 0;
		$nullDate              = $db->getNullDate();
		if ($user->id)
		{
			$discountRate = self::calculateMemberDiscount($event->discount_amounts, $event->discount_groups);
			if ($discountRate > 0)
			{
				$fees['discount_rate'] = $discountRate;
				if ($event->discount_type == 1)
				{
					$discountAmount = $totalAmount * $discountRate / 100;
				}
				else
				{
					$discountAmount = $discountRate;
				}
			}
		}

		if (($event->early_bird_discount_date != $nullDate) && ($event->date_diff >= 0))
		{
			if ($event->early_bird_discount_amount > 0)
			{
				if ($event->early_bird_discount_type == 1)
				{
					$discountAmount = $discountAmount + $totalAmount * $event->early_bird_discount_amount / 100;
				}
				else
				{
					$discountAmount = $discountAmount + $event->early_bird_discount_amount;
				}
			}
		}

		if ($couponCode)
		{
			//Validate the coupon
			$query->clear();
			$query->select('*')
				->from('#__eb_coupons')
				->where('published=1')
				->where('code="' . $couponCode . '"')
				->where('(valid_from="0000-00-00" OR valid_from <= NOW())')
				->where('(valid_to="0000-00-00" OR valid_to >= NOW())')
				->where('(times = 0 OR times > used)')
				->where('(event_id = -1 OR id IN (SELECT coupon_id FROM #__eb_coupon_events WHERE event_id=' . $event->id . '))')
				->order('id DESC');
			$db->setQuery($query);
			$coupon = $db->loadObject();
			if ($coupon)
			{
				$fees['coupon_valid'] = 1;
				$fees['coupon']       = $coupon;
				if ($coupon->coupon_type == 0)
				{
					$discountAmount = $discountAmount + $totalAmount * $coupon->discount / 100;
				}
				else
				{
					$discountAmount = $discountAmount + $coupon->discount;
				}
			}
			else
			{
				$fees['coupon_valid'] = 0;
			}
		}
		else
		{
			$fees['coupon_valid'] = 1;
		}

		if ($discountAmount > $totalAmount)
		{
			$discountAmount = $totalAmount;
		}

		// Late Fee
		$lateFee = 0;
		if (($event->late_fee_date != $nullDate) && $event->late_fee_date_diff >= 0 && $event->late_fee_amount > 0)
		{
			if ($event->late_fee_type == 1)
			{
				$lateFee = $event->individual_price * $event->late_fee_amount / 100;
			}
			else
			{

				$lateFee = $event->late_fee_amount;
			}
		}

		if ($event->tax_rate && ($totalAmount - $discountAmount + $lateFee > 0))
		{
			$taxAmount = round(($totalAmount - $discountAmount + $lateFee) * $event->tax_rate / 100, 2);
		}
		else
		{
			$taxAmount = 0;
		}

		$amount = $totalAmount - $discountAmount + $taxAmount + $lateFee;

		// Payment processing fee
		$paymentFeeAmount  = 0;
		$paymentFeePercent = 0;
		if ($paymentMethod)
		{
			$method            = os_payments::loadPaymentMethod($paymentMethod);
			$params            = new JRegistry($method->params);
			$paymentFeeAmount  = (float) $params->get('payment_fee_amount');
			$paymentFeePercent = (float) $params->get('payment_fee_percent');
		}
		if (($paymentFeeAmount > 0 || $paymentFeePercent > 0) && $amount > 0)
		{
			$fees['payment_processing_fee'] = round($paymentFeeAmount + $amount * $paymentFeePercent / 100, 2);
			$amount += $fees['payment_processing_fee'];
		}
		else
		{
			$fees['payment_processing_fee'] = 0;
		}

		// Calculate the deposit amount as well
		if ($config->activate_deposit_feature && $event->deposit_amount > 0)
		{
			if ($event->deposit_type == 2)
			{
				$depositAmount = $event->deposit_amount;
			}
			else
			{
				$depositAmount = $event->deposit_amount * $amount / 100;
			}
		}
		else
		{
			$depositAmount = 0;
		}

		$fees['total_amount']    = $totalAmount;
		$fees['discount_amount'] = $discountAmount;
		$fees['tax_amount']      = $taxAmount;
		$fees['amount']          = $amount;
		$fees['deposit_amount']  = $depositAmount;
		$fees['late_fee']        = $lateFee;

		return $fees;
	}

	/**
	 * Calculate fees use for group registration
	 *
	 * @param $event
	 * @param $form
	 * @param $data
	 * @param $config
	 * @param $paymentMethod
	 *
	 * @return array
	 */
	public static function calculateGroupRegistrationFees($event, $form, $data, $config, $paymentMethod = null)
	{
		$fees                  = array();
		$session               = JFactory::getSession();
		$user                  = JFactory::getUser();
		$db                    = JFactory::getDbo();
		$query                 = $db->getQuery(true);
		$couponCode            = isset($data['coupon_code']) ? $data['coupon_code'] : '';
		$eventId               = $event->id;
		$extraFee              = $form->calculateFee();
		$numberRegistrants     = (int) $session->get('eb_number_registrants', '');
		$memberFormFields      = EventbookingHelper::getFormFields($eventId, 2);
		$rate                  = EventbookingHelper::getRegistrationRate($eventId, $numberRegistrants);
		$nullDate              = $db->getNullDate();
		$membersForm           = array();
		$membersTotalAmount    = array();
		$membersDiscountAmount = array();
		$membersLateFee        = array();
		$membersTaxAmount      = array();
		// Members data
		if ($config->collect_member_information)
		{
			$membersData = $session->get('eb_group_members_data', null);
			if ($membersData)
			{
				$membersData = unserialize($membersData);
			}
			else
			{
				$membersData = array();
			}
			for ($i = 0; $i < $numberRegistrants; $i++)
			{
				$memberForm = new RADForm($memberFormFields);
				$memberForm->setFieldSuffix($i + 1);
				$memberForm->bind($membersData);
				$memberExtraFee = $memberForm->calculateFee();
				$extraFee += $memberExtraFee;
				$membersTotalAmount[$i]    = $rate + $memberExtraFee;
				$membersDiscountAmount[$i] = 0;
				$membersLateFee[$i] = 0;
				$membersForm[$i]           = $memberForm;
			}
		}

		if ($event->fixed_group_price > 0)
		{
			$totalAmount = $event->fixed_group_price + $extraFee;
		}
		else
		{
			$totalAmount = $rate * $numberRegistrants + $extraFee;
		}

		// Calculate discount amount
		$discountAmount = 0;
		if ($user->id)
		{
			$discountRate = self::calculateMemberDiscount($event->discount_amounts, $event->discount_groups);
			if ($discountRate > 0)
			{
				if ($event->discount_type == 1)
				{
					$discountAmount = $totalAmount * $discountRate / 100;
					if ($config->collect_member_information)
					{
						for ($i = 0; $i < $numberRegistrants; $i++)
						{
							$membersDiscountAmount[$i] += $membersTotalAmount[$i] * $discountRate / 100;
						}
					}
				}
				else
				{
					$discountAmount = $numberRegistrants * $discountRate;
					if ($config->collect_member_information)
					{
						for ($i = 0; $i < $numberRegistrants; $i++)
						{
							$membersDiscountAmount[$i] += $discountRate;
						}
					}
				}
			}
		}

		if ($couponCode)
		{
			$query->clear();
			$query->select('*')
				->from('#__eb_coupons')
				->where('published=1')
				->where('code="' . $couponCode . '"')
				->where('(valid_from="0000-00-00" OR valid_from <= NOW())')
				->where('(valid_to="0000-00-00" OR valid_to >= NOW())')
				->where('(times = 0 OR times > used)')
				->where('(event_id = -1 OR id IN (SELECT coupon_id FROM #__eb_coupon_events WHERE event_id=' . $event->id . '))')
				->order('id DESC');
			$db->setQuery($query);
			$coupon = $db->loadObject();
			if ($coupon)
			{
				$fees['coupon_valid'] = 1;
				$fees['coupon']       = $coupon;
				if ($coupon->coupon_type == 0)
				{
					$discountAmount = $discountAmount + $totalAmount * $coupon->discount / 100;

					if ($config->collect_member_information)
					{
						for ($i = 0; $i < $numberRegistrants; $i++)
						{
							$membersDiscountAmount[$i] += $membersTotalAmount[$i] * $coupon->discount / 100;
						}
					}
				}
				else
				{
					$discountAmount = $discountAmount + $numberRegistrants * $coupon->discount;

					if ($config->collect_member_information)
					{
						for ($i = 0; $i < $numberRegistrants; $i++)
						{
							$membersDiscountAmount[$i] += $coupon->discount;
						}
					}
				}
			}
			else
			{
				$fees['coupon_valid'] = 0;
			}
		}
		else
		{
			$fees['coupon_valid'] = 1;
		}


		if (($event->early_bird_discount_date != $nullDate) && ($event->date_diff >= 0))
		{
			if ($event->early_bird_discount_amount > 0)
			{
				if ($event->early_bird_discount_type == 1)
				{
					$discountAmount = $discountAmount + $totalAmount * $event->early_bird_discount_amount / 100;
					if ($config->collect_member_information)
					{
						for ($i = 0; $i < $numberRegistrants; $i++)
						{
							$membersDiscountAmount[$i] += $membersTotalAmount[$i] * $event->early_bird_discount_amount / 100;
						}
					}
				}
				else
				{
					$discountAmount = $discountAmount + $numberRegistrants * $event->early_bird_discount_amount;
					if ($config->collect_member_information)
					{
						for ($i = 0; $i < $numberRegistrants; $i++)
						{
							$membersDiscountAmount[$i] += $event->early_bird_discount_amount;
						}
					}
				}
			}
		}

		// Late Fee
		$lateFee = 0;
		if (($event->late_fee_date != $nullDate) && $event->late_fee_date_diff >= 0 && $event->late_fee_amount > 0)
		{
			if ($event->late_fee_type == 1)
			{
				$lateFee = $totalAmount * $event->late_fee_amount / 100;
				if ($config->collect_member_information)
				{
					for ($i = 0; $i < $numberRegistrants; $i++)
					{
						$membersLateFee[$i] = $membersTotalAmount[$i] * $event->late_fee_amount / 100;
					}
				}
			}
			else
			{

				$lateFee = $numberRegistrants * $event->late_fee_amount;
				if ($config->collect_member_information)
				{
					for ($i = 0; $i < $numberRegistrants; $i++)
					{
						$membersLateFee[$i] = $event->late_fee_amount;
					}
				}
			}
		}

		// In case discount amount greater than total amount, reset it to total amount
		if ($discountAmount > $totalAmount)
		{
			$discountAmount = $totalAmount;
		}

		if ($config->collect_member_information)
		{
			for ($i = 0; $i < $numberRegistrants; $i++)
			{
				if ($membersDiscountAmount[$i] > $membersTotalAmount[$i])
				{
					$membersDiscountAmount[$i] = $membersTotalAmount[$i];
				}
			}
		}

		// Calculate tax amount
		if ($event->tax_rate && ($totalAmount - $discountAmount + $lateFee > 0))
		{
			$taxAmount = round(($totalAmount - $discountAmount + $lateFee) * $event->tax_rate / 100, 2);
			if ($config->collect_member_information)
			{
				for ($i = 0; $i < $numberRegistrants; $i++)
				{
					$membersTaxAmount[$i] = round(($membersTotalAmount[$i] - $membersDiscountAmount[$i] + $membersLateFee[$i]) * $event->tax_rate / 100, 2);
				}
			}
		}
		else
		{
			$taxAmount = 0;
			if ($config->collect_member_information)
			{
				for ($i = 0; $i < $numberRegistrants; $i++)
				{
					$membersTaxAmount[$i] = 0;
				}
			}
		}

		// Gross amount
		$amount = $totalAmount - $discountAmount + $taxAmount + $lateFee;

		// Payment processing fee
		$paymentFeeAmount  = 0;
		$paymentFeePercent = 0;
		if ($paymentMethod)
		{
			$method            = os_payments::loadPaymentMethod($paymentMethod);
			$params            = new JRegistry($method->params);
			$paymentFeeAmount  = (float) $params->get('payment_fee_amount');
			$paymentFeePercent = (float) $params->get('payment_fee_percent');
		}
		if (($paymentFeeAmount > 0 || $paymentFeePercent > 0) && $amount > 0)
		{
			$fees['payment_processing_fee'] = round($paymentFeeAmount + $amount * $paymentFeePercent / 100, 2);
			$amount += $fees['payment_processing_fee'];
		}
		else
		{
			$fees['payment_processing_fee'] = 0;
		}

		// Deposit amount
		if ($config->activate_deposit_feature && $event->deposit_amount > 0)
		{
			if ($event->deposit_type == 2)
			{
				$depositAmount = $numberRegistrants * $event->deposit_amount;
			}
			else
			{
				$depositAmount = $event->deposit_amount * $amount / 100;
			}
		}
		else
		{
			$depositAmount = 0;
		}

		$fees['total_amount']            = $totalAmount;
		$fees['discount_amount']         = $discountAmount;
		$fees['late_fee']                = $lateFee;
		$fees['tax_amount']              = $taxAmount;
		$fees['amount']                  = $amount;
		$fees['deposit_amount']          = $depositAmount;
		$fees['members_form']            = $membersForm;
		$fees['members_total_amount']    = $membersTotalAmount;
		$fees['members_discount_amount'] = $membersDiscountAmount;
		$fees['members_tax_amount']      = $membersTaxAmount;
		$fees['members_late_fee']        = $membersLateFee;

		return $fees;
	}

	/**
	 * Calculate registration fee for cart registration
	 *
	 * @param      $cart
	 * @param      $form
	 * @param      $data
	 * @param      $config
	 * @param null $paymentMethod
	 *
	 * @return array
	 */
	public static function calculateCartRegistrationFee($cart, $form, $data, $config, $paymentMethod = null)
	{
		$user                 = JFactory::getUser();
		$db                   = JFactory::getDbo();
		$query                = $db->getQuery(true);
		$nullDate             = $db->getNullDate();
		$fees                 = array();
		$recordsData          = array();
		$totalAmount          = 0;
		$discountAmount       = 0;
		$lateFee              = 0;
		$taxAmount            = 0;
		$amount               = 0;
		$depositAmount        = 0;
		$paymentProcessingFee = 0;
		$feeAmount            = $form->calculateFee();
		$items                = $cart->getItems();
		$quantities           = $cart->getQuantities();
		$paymentType          = isset($data['payment_type']) ? $data['payment_type'] : 1;
		$couponCode           = isset($data['coupon_code']) ? $data['coupon_code'] : '';
		$collectRecordsData   = isset($data['collect_records_data']) ? $data['collect_records_data'] : false;
		$paymentFeeAmount     = 0;
		$paymentFeePercent    = 0;
		if ($paymentMethod)
		{
			$method            = os_payments::loadPaymentMethod($paymentMethod);
			$params            = new JRegistry($method->params);
			$paymentFeeAmount  = (float) $params->get('payment_fee_amount');
			$paymentFeePercent = (float) $params->get('payment_fee_percent');
		}

		$couponDiscountedEventIds = array();
		if ($couponCode)
		{
			$query->clear();
			$query->select('*')
				->from('#__eb_coupons')
				->where('code=' . $db->quote($couponCode))
				->where('(event_id = -1 OR id IN (SELECT coupon_id FROM #__eb_coupon_events WHERE event_id IN (' . implode(',', $items) . ')))')
				->order('id DESC');
			$db->setQuery($query);
			$coupon = $db->loadObject();
			if ($coupon)
			{
				$fees['coupon_valid'] = 1;
				if ($coupon->event_id != -1)
				{
					// Get list of events which will receive discount
					$query->clear();
					$query->select('event_id')
						->from('#__eb_coupon_events')
						->where('coupon_id = '. $coupon->id);
					$db->setQuery($query);
					$couponDiscountedEventIds = $db->loadColumn();
				}
			}
			else
			{
				$fees['coupon_valid'] = 0;
			}
		}
		else
		{
			$fees['coupon_valid'] = 1;
		}

		for ($i = 0, $n = count($items); $i < $n; $i++)
		{
			$eventId               = (int) $items[$i];
			$quantity              = (int) $quantities[$i];
			$recordsData[$eventId] = array();
			$event = EventbookingHelperDatabase::getEvent($eventId);
			$rate  = self::getRegistrationRate($eventId, $quantity);
			if ($i == 0)
			{
				$registrantTotalAmount = $rate * $quantity + $feeAmount;
			}
			else
			{
				$registrantTotalAmount = $rate * $quantity;
			}

			$registrantDiscount = 0;

			// Member discount
			if ($user->id)
			{
				$discountRate = EventbookingHelper::calculateMemberDiscount($event->discount_amounts, $event->discount_groups);
				if ($discountRate > 0)
				{
					if ($event->discount_type == 1)
					{
						$registrantDiscount = $registrantTotalAmount * $discountRate / 100;
					}
					else
					{
						$registrantDiscount = $quantity * $discountRate;
					}
				}
			}
			if (($event->early_bird_discount_date != $nullDate) && $event->date_diff >= 0 && $event->early_bird_discount_amount > 0)
			{
				if ($event->early_bird_discount_type == 1)
				{
					$registrantDiscount += $registrantTotalAmount * $event->early_bird_discount_amount / 100;
				}
				else
				{
					$registrantDiscount += $event->early_bird_discount_amount;
				}
			}


			// Coupon discount
			if (!empty($coupon) && ($coupon->event_id == -1 || in_array($eventId, $couponDiscountedEventIds)))
			{
				if ($coupon->coupon_type == 0)
				{
					$registrantDiscount = $registrantDiscount + $registrantTotalAmount * $coupon->discount / 100;
				}
				else
				{
					$registrantDiscount = $registrantDiscount + $coupon->discount;
				}
				if ($collectRecordsData)
				{
					$recordsData[$eventId]['coupon_id'] = $coupon->id;
				}
			}

			if ($registrantDiscount > $registrantTotalAmount)
			{
				$registrantDiscount = $registrantTotalAmount;
			}

			// Late Fee
			$registrantLateFee = 0;
			if (($event->late_fee_date != $nullDate) && $event->late_fee_date_diff >= 0 && $event->late_fee_amount > 0)
			{
				if ($event->late_fee_type == 1)
				{
					$registrantLateFee = $registrantTotalAmount * $event->late_fee_amount / 100;
				}
				else
				{

					$registrantLateFee = $quantity*$event->late_fee_amount;
				}
			}

			if ($event->tax_rate > 0)
			{
				$registrantTaxAmount = round($event->tax_rate * ($registrantTotalAmount - $registrantDiscount + $registrantLateFee) / 100, 2);
			}
			else
			{
				$registrantTaxAmount = 0;
			}
			$registrantAmount = $registrantTotalAmount - $registrantDiscount + $registrantTaxAmount + $registrantLateFee;

			if (($paymentFeeAmount > 0 || $paymentFeePercent > 0) && $registrantAmount > 0)
			{
				$registrantPaymentProcessingFee = round($paymentFeeAmount + $registrantAmount * $paymentFeePercent / 100, 2);
				$registrantAmount += $registrantPaymentProcessingFee;
			}
			else
			{

				$registrantPaymentProcessingFee = 0;
			}

			if ($config->activate_deposit_feature && $event->deposit_amount > 0 && $paymentType == 1)
			{
				if ($event->deposit_type == 2)
				{
					$registrantDepositAmount = $event->deposit_amount * $quantity;
				}
				else
				{
					$registrantDepositAmount = round($registrantAmount * $event->deposit_amount / 100, 2);
				}
			}
			else
			{
				$registrantDepositAmount = 0;
			}
			$totalAmount += $registrantTotalAmount;
			$discountAmount += $registrantDiscount;
			$lateFee        += $registrantLateFee;
			$depositAmount += $registrantDepositAmount;
			$taxAmount += $registrantTaxAmount;
			$amount += $registrantAmount;
			$paymentProcessingFee += $registrantPaymentProcessingFee;

			if ($collectRecordsData)
			{
				$recordsData[$eventId]['total_amount']           = $registrantTotalAmount;
				$recordsData[$eventId]['discount_amount']        = $registrantDiscount;
				$recordsData[$eventId]['late_fee']               = $registrantLateFee;
				$recordsData[$eventId]['tax_amount']             = $registrantTaxAmount;
				$recordsData[$eventId]['payment_processing_fee'] = $registrantPaymentProcessingFee;
				$recordsData[$eventId]['amount']                 = $registrantAmount;
				$recordsData[$eventId]['deposit_amount']         = $registrantDepositAmount;
			}
		}

		$fees['total_amount']           = $totalAmount;
		$fees['discount_amount']        = $discountAmount;
		$fees['late_fee']               = $lateFee;
		$fees['tax_amount']             = $taxAmount;
		$fees['amount']                 = $amount;
		$fees['deposit_amount']         = $depositAmount;
		$fees['payment_processing_fee'] = $paymentProcessingFee;
		if ($collectRecordsData)
		{
			$fees['records_data'] = $recordsData;
		}

		return $fees;
	}

	/**
	 * Get URL of the site, using for Ajax request
	 */
	public static function getSiteUrl()
	{
		$uri  = JUri::getInstance();
		$base = $uri->toString(array('scheme', 'host', 'port'));
		if (strpos(php_sapi_name(), 'cgi') !== false && !ini_get('cgi.fix_pathinfo') && !empty($_SERVER['REQUEST_URI']))
		{
			$script_name = $_SERVER['PHP_SELF'];
		}
		else
		{
			$script_name = $_SERVER['SCRIPT_NAME'];
		}
		$path = rtrim(dirname($script_name), '/\\');
		if ($path)
		{
			return $base . $path . '/';
		}
		else
		{
			return $base . '/';
		}
	}

	/**
	 * Get the form fields to display in registration form
	 *
	 * @param int    $eventId (ID of the event or ID of the registration record in case the system use shopping cart)
	 * @param int    $registrationType
	 * @param string $activeLanguage
	 *
	 * @return array
	 */
	public static function getFormFields($eventId = 0, $registrationType = 0, $activeLanguage = null)
	{
		$user        = JFactory::getUser();
		$db          = JFactory::getDbo();
		$query       = $db->getQuery(true);
		$config      = EventbookingHelper::getConfig();
		$fieldSuffix = EventbookingHelper::getFieldSuffix($activeLanguage);
		$query->select('*')
			->select('title' . $fieldSuffix . ' AS title')
			->select('description' . $fieldSuffix . ' AS description')
			->select('`values' . $fieldSuffix . '` AS `values`')
			->select('default_values' . $fieldSuffix . ' AS default_values')
			->select('depend_on_options' . $fieldSuffix . ' AS depend_on_options')
			->from('#__eb_fields')
			->where('published=1')
			->where(' `access` IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')');
		switch ($registrationType)
		{
			case 0:
				$query->where('display_in IN (0, 1, 3, 5)');
				break;
			case 1:
				$query->where('display_in IN (0, 2, 3)');
				break;
			case 2:
				$query->where('display_in IN (0, 4, 5)');
				break;
		}
		if ($registrationType == 4)
		{
			$cart  = new EventbookingHelperCart();
			$items = $cart->getItems();
			if ($config->custom_field_by_category)
			{
				if (!count($items))
				{
					//In this case, we have ID of registration record, so, get list of events from that registration
					$sql = 'SELECT event_id FROM #__eb_registrants WHERE id=' . $eventId;
					$db->setQuery($sql);
					$cartEventId = (int) $db->loadResult();
				}
				else
				{
					$cartEventId = (int) $items[0];
				}
				$sql = 'SELECT category_id FROM #__eb_event_categories WHERE event_id=' . $cartEventId . ' AND main_category = 1';
				$db->setQuery($sql);
				$categoryId = (int) $db->loadResult();
				$query->where('(category_id = -1 OR id IN (SELECT field_id FROM #__eb_field_categories WHERE category_id=' . $categoryId . '))');
			}
			else
			{
				if (!count($items))
				{
					//In this case, we have ID of registration record, so, get list of events from that registration
					$sql = 'SELECT event_id FROM #__eb_registrants WHERE id=' . $eventId;
					$db->setQuery($sql);
					$items = $db->loadColumn();
				}
				$query->where(' (event_id = -1 OR id IN (SELECT field_id FROM #__eb_field_events WHERE event_id IN (' . implode(',', $items) . ')))');
			}
		}
		else
		{
			if ($config->custom_field_by_category)
			{
				//Get main category of the event
				$sql = 'SELECT category_id FROM #__eb_event_categories WHERE event_id=' . $eventId . ' AND main_category = 1';
				$db->setQuery($sql);
				$categoryId = (int) $db->loadResult();
				$query->where('(category_id = -1 OR id IN (SELECT field_id FROM #__eb_field_categories WHERE category_id=' . $categoryId . '))');
			}
			else
			{
				$query->where(' (event_id = -1 OR id IN (SELECT field_id FROM #__eb_field_events WHERE event_id=' . $eventId . '))');
			}
		}
		$query->order('ordering');
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Get the form data used to bind to the RADForm object
	 *
	 * @param array  $rowFields
	 * @param int    $eventId
	 * @param int    $userId
	 * @param object $config
	 *
	 * @return array
	 */
	public static function getFormData($rowFields, $eventId, $userId, $config)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$data  = array();
		if ($userId)
		{
			if ($config->cb_integration == 1)
			{
				$syncronizer = new RADSynchronizerCommunitybuilder();
				$mappings    = array();
				foreach ($rowFields as $rowField)
				{
					if ($rowField->field_mapping)
					{
						$mappings[$rowField->name] = $rowField->field_mapping;
					}
				}
				$data = $syncronizer->getData($userId, $mappings);
			}
			elseif ($config->cb_integration == 2)
			{
				$syncronizer = new RADSynchronizerJomsocial();
				$mappings    = array();
				foreach ($rowFields as $rowField)
				{
					if ($rowField->field_mapping)
					{
						$mappings[$rowField->name] = $rowField->field_mapping;
					}
				}
				$data = $syncronizer->getData($userId, $mappings);
			}
			elseif ($config->cb_integration == 3)
			{
				$syncronizer = new RADSynchronizerMembershippro();
				$mappings    = array();
				foreach ($rowFields as $rowField)
				{
					if ($rowField->field_mapping)
					{
						$mappings[$rowField->name] = $rowField->field_mapping;
					}
				}
				$data = $syncronizer->getData($userId, $mappings);
			}
			elseif ($config->cb_integration == 4)
			{
				$syncronizer = new RADSynchronizerJoomla();
				$mappings    = array();
				foreach ($rowFields as $rowField)
				{
					if ($rowField->field_mapping)
					{
						$mappings[$rowField->name] = $rowField->field_mapping;
					}
				}
				$data = $syncronizer->getData($userId, $mappings);
			}
			elseif ($config->cb_integration == 5)
			{
				$syncronizer = new RADSynchronizerContactenhanced();
				$mappings    = array();
				foreach ($rowFields as $rowField)
				{
					if ($rowField->field_mapping)
					{
						$mappings[$rowField->name] = $rowField->field_mapping;
					}
				}
				$data = $syncronizer->getData($userId, $mappings);
			}
			else
			{
				$query->select('*')
					->from('#__eb_registrants')
					->where('user_id=' . $userId . ' AND event_id=' . $eventId . ' AND first_name != "" AND group_id=0')
					->order('id DESC');
				$db->setQuery($query, 0, 1);
				$rowRegistrant = $db->loadObject();
				if (!$rowRegistrant)
				{
					//Try to get registration record from other events if available
					$query->clear('where')->where('user_id=' . $userId . ' AND first_name != "" AND group_id=0');
					$db->setQuery($query, 0, 1);
					$rowRegistrant = $db->loadObject();
				}
				if ($rowRegistrant)
				{
					$data = self::getRegistrantData($rowRegistrant, $rowFields);
				}
			}
		}

		return $data;
	}

	/**
	 * Get data of registrant using to auto populate registration form
	 *
	 * @param Object $rowRegistrant
	 * @param array  $rowFields
	 *
	 * @return array
	 */
	public static function getRegistrantData($rowRegistrant, $rowFields)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$data  = array();
		$query->select('a.name, b.field_value')
			->from('#__eb_fields AS a')
			->innerJoin('#__eb_field_values AS b ON a.id = b.field_id')
			->where('b.registrant_id=' . $rowRegistrant->id);
		$db->setQuery($query);
		$fieldValues = $db->loadObjectList('name');
		for ($i = 0, $n = count($rowFields); $i < $n; $i++)
		{
			$rowField = $rowFields[$i];
			if ($rowField->is_core)
			{
				$data[$rowField->name] = $rowRegistrant->{$rowField->name};
			}
			else
			{
				if (isset($fieldValues[$rowField->name]))
				{
					$data[$rowField->name] = $fieldValues[$rowField->name]->field_value;
				}
			}
		}

		return $data;
	}

	/**
	 * Check to see whether we will show billing form on group registration
	 *
	 * @param int $eventId
	 *
	 * @return boolean
	 */
	public static function showBillingStep($eventId)
	{
		$config = self::getConfig();
		if (!$config->collect_member_information || $config->show_billing_step_for_free_events)
		{
			return true;
		}
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('individual_price')
			->from('#__eb_events')
			->where('id=' . $eventId);
		$db->setQuery($query);
		$individualPrice = $db->loadResult();
		if ($individualPrice == 0)
		{
			$config = EventbookingHelper::getConfig();
			if ($config->custom_field_by_category)
			{
				$sql = 'SELECT category_id FROM #__eb_event_categories WHERE event_id=' . $eventId . ' AND main_category = 1';
				$db->setQuery($sql);
				$categoryId = (int) $db->loadResult();
				$sql        = 'SELECT COUNT(*) FROM #__eb_fields WHERE fee_field = 1 AND published= 1 AND (category_id = -1 OR id IN (SELECT field_id FROM #__eb_field_categories WHERE category_id=' . $categoryId . '))';
				$db->setQuery($sql);
			}
			else
			{
				$sql = 'SELECT COUNT(*) FROM #__eb_fields WHERE fee_field = 1 AND published= 1 AND (event_id = -1 OR id IN (SELECT field_id FROM #__eb_field_events WHERE event_id=' .
					$eventId . '))';
				$db->setQuery($sql);
			}
			$numberFeeFields = (int) $db->loadResult();
			if ($numberFeeFields == 0)
			{
				return false;
			}
		}

		return true;
	}

	/**
	 *
	 *
	 * @return string
	 */
	public static function validateEngine()
	{
		$dateNow = JHtml::_('date', JFactory::getDate(), 'Y/m/d');
		//validate[required,custom[integer],min[-5]] text-input
		$validClass = array(
			"",
			"validate[custom[integer]]",
			"validate[custom[number]]",
			"validate[custom[email]]",
			"validate[custom[url]]",
			"validate[custom[phone]]",
			"validate[custom[date],past[$dateNow]]",
			"validate[custom[ipv4]]",
			"validate[minSize[6]]",
			"validate[maxSize[12]]",
			"validate[custom[integer],min[-5]]",
			"validate[custom[integer],max[50]]");

		return json_encode($validClass);
	}


	public static function getURL()
	{
		static $url;
		if (!$url)
		{
			$ssl = self::getConfigValue('use_https');
			$url = self::getSiteUrl();
			if ($ssl)
			{
				$url = str_replace('http://', 'https://', $url);
			}
		}

		return $url;
	}

	/**
	 * Get Itemid of Event Booking extension
	 *
	 * @return int
	 */
	public static function getItemid()
	{
		$app   = JFactory::getApplication();
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$user  = JFactory::getUser();
		$query->select('id')
			->from('#__menu AS a')
			->where('a.link LIKE "%index.php?option=com_eventbooking%"')
			->where('a.published=1')
			->where('a.access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')');
		if ($app->isSite() && $app->getLanguageFilter())
		{
			$query->where('a.language IN (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')');
		}
		$query->order('a.access');
		$db->setQuery($query);
		$itemId = $db->loadResult();
		if (!$itemId)
		{
			$Itemid = $app->input->getInt('Itemid', 0);
			if ($Itemid == 1)
			{
				$itemId = 999999;
			}
			else
			{
				$itemId = $Itemid;
			}
		}

		return $itemId;
	}

	/**
	 *
	 * @param JUser    $user the current logged in user
	 * @param Stdclass $config
	 *
	 * @return boolean
	 */
	public static function memberGetDiscount($user, $config)
	{
		if (isset($config->member_discount_groups) && $config->member_discount_groups)
		{
			$userGroups = $user->getAuthorisedGroups();
			if (count(array_intersect(explode(',', $config->member_discount_groups), $userGroups)))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return true;
		}
	}

	/**
	 * Calculate discount rate which the current user will receive
	 *
	 * @param $discount
	 * @param $groupIds
	 *
	 * @return float
	 */
	public static function calculateMemberDiscount($discount, $groupIds)
	{
		$user = JFactory::getUser();
		if (!$discount)
		{
			return 0;
		}
		if (!$groupIds)
		{
			return $discount;
		}
		$userGroupIds = explode(',', $groupIds);
		JArrayHelper::toInteger($userGroupIds);
		$groups = $user->get('groups');
		if (count(array_intersect($groups, $userGroupIds)))
		{
			//Calculate discount amount
			if (strpos($discount, ',') !== false)
			{
				$discountRates = explode(',', $discount);
				$maxDiscount   = 0;
				foreach ($groups as $group)
				{
					$index = array_search($group, $userGroupIds);
					if ($index !== false && isset($discountRates[$index]))
					{
						$maxDiscount = max($maxDiscount, $discountRates[$index]);
					}
				}

				return $maxDiscount;
			}
			else
			{
				return $discount;
			}
		}
		else
		{
			return 0;
		}
	}

	/**
	 * Format the currency according to the settings in Configuration
	 *
	 * @param      $amount         the input amount
	 * @param      $config         the config object
	 * @param null $currencySymbol the currency symbol. If null, the one in configuration will be used
	 *
	 * @return string   the formatted string
	 */
	public static function formatAmount($amount, $config)
	{
		$decimals      = isset($config->decimals) ? $config->decimals : 2;
		$dec_point     = isset($config->dec_point) ? $config->dec_point : '.';
		$thousands_sep = isset($config->thousands_sep) ? $config->thousands_sep : ',';

		return number_format($amount, $decimals, $dec_point, $thousands_sep);
	}

	/**
	 * Format the currency according to the settings in Configuration
	 *
	 * @param      $amount         the input amount
	 * @param      $config         the config object
	 * @param null $currencySymbol the currency symbol. If null, the one in configuration will be used
	 *
	 * @return string   the formatted string
	 */
	public static function formatCurrency($amount, $config, $currencySymbol = null)
	{
		$decimals      = isset($config->decimals) ? $config->decimals : 2;
		$dec_point     = isset($config->dec_point) ? $config->dec_point : '.';
		$thousands_sep = isset($config->thousands_sep) ? $config->thousands_sep : ',';
		$symbol        = $currencySymbol ? $currencySymbol : $config->currency_symbol;

		return $config->currency_position ? (number_format($amount, $decimals, $dec_point, $thousands_sep) . $symbol) : ($symbol .
			number_format($amount, $decimals, $dec_point, $thousands_sep));
	}

	/**
	 * Load Event Booking language file
	 */
	public static function loadLanguage()
	{
		static $loaded;
		if (!$loaded)
		{
			$lang = JFactory::getLanguage();
			$tag  = $lang->getTag();
			if (!$tag)
				$tag = 'en-GB';
			$lang->load('com_eventbooking', JPATH_ROOT, $tag);
			$loaded = true;
		}
	}

	/**
	 * Get email content, used for [REGISTRATION_DETAIL] tag
	 *
	 * @param object $config
	 * @param object $row
	 *
	 * @return string
	 */
	public static function getMemberDetails($config, $rowMember, $rowEvent, $rowLocation, $loadCss = true, $memberForm)
	{
		$data                = array();
		$data['rowMember']   = $rowMember;
		$data['rowEvent']    = $rowEvent;
		$data['config']      = $config;
		$data['rowLocation'] = $rowLocation;
		$data['memberForm']  = $memberForm;

		$text = EventbookingHelperHtml::loadCommonLayout(JPATH_ROOT . '/components/com_eventbooking/emailtemplates/email_group_member_detail.php',
			$data);
		if ($loadCss)
		{
			$text .= "
				<style type=\"text/css\">
				" . JFile::read(JPATH_ROOT . '/media/com_eventbooking/assets/css/style.css') . "
                </style>
            ";
		}

		return $text;
	}

	/**
	 * Get email content, used for [REGISTRATION_DETAIL] tag
	 *
	 * @param object $config
	 * @param object $row
	 *
	 * @return string
	 */
	public static function getEmailContent($config, $row, $loadCss = true, $form = null, $toAdmin = false)
	{
		$db     = JFactory::getDbo();
		$data   = array();
		$Itemid = JRequest::getInt('Itemid', 0);
		if ($config->multiple_booking)
		{
			if ($loadCss)
			{
				$layout = 'email_cart.php';
			}
			else
			{
				$layout = 'cart.php';
			}
		}
		else
		{
			if ($row->is_group_billing)
			{
				if ($loadCss)
				{
					$layout = 'email_group_detail.php';
				}
				else
				{
					$layout = 'group_detail.php';
				}
			}
			else
			{
				if ($loadCss)
				{
					$layout = 'email_individual_detail.php';
				}
				else
				{
					$layout = 'individual_detail.php';
				}
			}
		}
		if (!$loadCss)
		{
			// Need to pass bootstrap helper
			$data['bootstrapHelper'] = new EventbookingHelperBootstrap($config->twitter_bootstrap_version);
		}
		$fieldSuffix = EventbookingHelper::getFieldSuffix();
		if ($config->multiple_booking)
		{
			$data['row']    = $row;
			$data['config'] = $config;
			$data['Itemid'] = $Itemid;
			$sql            = 'SELECT a.*, b.event_date, b.title' . $fieldSuffix . ' AS title FROM #__eb_registrants AS a INNER JOIN #__eb_events AS b ON a.event_id=b.id WHERE a.id=' .
				$row->id . ' OR a.cart_id=' . $row->id;
			$db->setQuery($sql);
			$rows = $db->loadObjectList();
			$sql  = 'SELECT SUM(total_amount) FROM #__eb_registrants WHERE id=' . $row->id . ' OR cart_id=' . $row->id;
			$db->setQuery($sql);
			$totalAmount = $db->loadResult();

			$sql = 'SELECT SUM(tax_amount) FROM #__eb_registrants WHERE id=' . $row->id . ' OR cart_id=' . $row->id;
			$db->setQuery($sql);
			$taxAmount = $db->loadResult();

			$sql = 'SELECT SUM(discount_amount) FROM #__eb_registrants WHERE id=' . $row->id . ' OR cart_id=' . $row->id;
			$db->setQuery($sql);
			$discountAmount = $db->loadResult();

			$sql = 'SELECT SUM(late_fee) FROM #__eb_registrants WHERE id=' . $row->id . ' OR cart_id=' . $row->id;
			$db->setQuery($sql);
			$lateFee = $db->loadResult();

			$sql = 'SELECT SUM(payment_processing_fee) FROM #__eb_registrants WHERE id=' . $row->id . ' OR cart_id=' . $row->id;
			$db->setQuery($sql);
			$paymentProcessingFee = $db->loadResult();

			$amount = $totalAmount + $paymentProcessingFee - $discountAmount + $taxAmount + $lateFee;

			$sql = 'SELECT SUM(deposit_amount) FROM #__eb_registrants WHERE id=' . $row->id . ' OR cart_id=' . $row->id;
			$db->setQuery($sql);
			$depositAmount = $db->loadResult();
			//Added support for custom field feature
			$data['discountAmount']       = $discountAmount;
			$data['lateFee']              = $lateFee;
			$data['totalAmount']          = $totalAmount;
			$data['items']                = $rows;
			$data['amount']               = $amount;
			$data['taxAmount']            = $taxAmount;
			$data['paymentProcessingFee'] = $paymentProcessingFee;
			$data['depositAmount']        = $depositAmount;
			$data['form']                 = $form;
		}
		else
		{
			$query = $db->getQuery(true);
			$query->select('*, title' . $fieldSuffix . ' AS title')
				->from('#__eb_events')
				->where('id=' . $row->event_id);
			$db->setQuery($query);
			$rowEvent = $db->loadObject();

			$query->clear();
			$query->select('a.*')
				->from('#__eb_locations AS a')
				->innerJoin('#__eb_events AS b ON a.id = b.location_id')
				->where('b.id=' . $row->event_id);
			$db->setQuery($query);
			$rowLocation = $db->loadObject();
			//Override config
			$data['row']         = $row;
			$data['rowEvent']    = $rowEvent;
			$data['config']      = $config;
			$data['rowLocation'] = $rowLocation;
			$data['form']        = $form;
			if ($row->is_group_billing && $config->collect_member_information)
			{
				$query->clear();
				$query->select('*')
					->from('#__eb_registrants')
					->where('group_id = '. $row->id)
					->order('id');
				$db->setQuery($query);
				$rowMembers         = $db->loadObjectList();
				$data['rowMembers'] = $rowMembers;
			}
		}

		if ($toAdmin && $row->payment_method == 'os_offline_creditcard')
		{
			$cardNumber = JFactory::getApplication()->input->getString('x_card_num', '');
			if ($cardNumber)
			{
				$last4Digits         = substr($cardNumber, strlen($cardNumber) - 4);
				$data['last4Digits'] = $last4Digits;
			}
		}

		$text = EventbookingHelperHtml::loadCommonLayout(JPATH_ROOT . '/components/com_eventbooking/emailtemplates/' . $layout, $data);
		if ($loadCss)
		{
			$text .= "
				<style type=\"text/css\">
				" . JFile::read(JPATH_ROOT . '/media/com_eventbooking/assets/css/style.css') . "
                </style>
            ";
		}

		return $text;
	}

	/**
	 * Build category dropdown
	 *
	 * @param int     $selected
	 * @param string  $name
	 * @param Boolean $onChange
	 *
	 * @return string
	 */
	public static function buildCategoryDropdown($selected, $name = "parent", $onChange = true)
	{
		$db          = JFactory::getDbo();
		$fieldSuffix = EventbookingHelper::getFieldSuffix();
		$sql         = "SELECT id, parent, parent AS parent_id, name" . $fieldSuffix . " AS name, name" . $fieldSuffix . " AS title FROM #__eb_categories";
		$db->setQuery($sql);
		$rows     = $db->loadObjectList();
		$children = array();
		if ($rows)
		{
			// first pass - collect children
			foreach ($rows as $v)
			{
				$pt   = $v->parent;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push($list, $v);
				$children[$pt] = $list;
			}
		}
		$list      = JHtml::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);
		$options   = array();
		$options[] = JHtml::_('select.option', '0', JText::_('Top'));
		foreach ($list as $item)
		{
			$options[] = JHtml::_('select.option', $item->id, '&nbsp;&nbsp;&nbsp;' . $item->treename);
		}

		if ($onChange)
			return JHtml::_('select.genericlist', $options, $name,
				array(
					'option.text.toHtml' => false,
					'option.text'        => 'text',
					'option.value'       => 'value',
					'list.attr'          => 'class="inputbox" onchange="submit();"',
					'list.select'        => $selected));
		else
			return JHtml::_('select.genericlist', $options, $name,
				array(
					'option.text.toHtml' => false,
					'option.text'        => 'text',
					'option.value'       => 'value',
					'list.attr'          => 'class="inputbox" ',
					'list.select'        => $selected));
	}

	/**
	 * Parent category select list
	 *
	 * @param object $row
	 *
	 * @return void
	 */
	public static function parentCategories($row)
	{
		$db          = JFactory::getDbo();
		$fieldSuffix = EventbookingHelper::getFieldSuffix();
		$sql         = "SELECT id, parent, parent AS parent_id, name" . $fieldSuffix . " AS name, name" . $fieldSuffix . " AS title FROM #__eb_categories";
		if ($row->id)
			$sql .= ' WHERE id != ' . $row->id;
		if (!$row->parent)
		{
			$row->parent = 0;
		}
		$db->setQuery($sql);
		$rows     = $db->loadObjectList();
		$children = array();
		if ($rows)
		{
			// first pass - collect children
			foreach ($rows as $v)
			{
				$pt   = $v->parent;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push($list, $v);
				$children[$pt] = $list;
			}
		}
		$list = JHtml::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);

		$options   = array();
		$options[] = JHtml::_('select.option', '0', JText::_('Top'));
		foreach ($list as $item)
		{
			$options[] = JHtml::_('select.option', $item->id, '&nbsp;&nbsp;&nbsp;' . $item->treename);
		}

		return JHtml::_('select.genericlist', $options, 'parent',
			array(
				'option.text.toHtml' => false,
				'option.text'        => 'text',
				'option.value'       => 'value',
				'list.attr'          => ' class="inputbox" ',
				'list.select'        => $row->parent));
	}

	public static function attachmentList($attachment, $config)
	{
		jimport('joomla.filesystem.folder');
		$path      = JPATH_ROOT . '/media/com_eventbooking';
		$files     = JFolder::files($path,
			strlen(trim($config->attachment_file_types)) ? $config->attachment_file_types : 'bmp|gif|jpg|png|swf|zip|doc|pdf|xls');
		$options   = array();
		$options[] = JHtml::_('select.option', '', JText::_('EB_SELECT_ATTACHMENT'));
		for ($i = 0, $n = count($files); $i < $n; $i++)
		{
			$file      = $files[$i];
			$options[] = JHtml::_('select.option', $file, $file);
		}

		return JHtml::_('select.genericlist', $options, 'attachment', 'class="inputbox"', 'value', 'text', $attachment);
	}

	/**
	 * Get total document of a category
	 *
	 * @param int $categoryId
	 */
	public static function getTotalEvent($categoryId, $includeChildren = true)
	{
		$app            = JFactory::getApplication();
		$user           = JFactory::getUser();
		$hidePastEvents = EventbookingHelper::getConfigValue('hide_past_events');
		$db             = JFactory::getDbo();
		$arrCats        = array();
		$cats           = array();
		$arrCats[]      = $categoryId;
		$cats[]         = $categoryId;
		if ($includeChildren)
		{
			while (count($arrCats))
			{
				$catId = array_pop($arrCats);
				//Get list of children category
				$sql = 'SELECT id FROM #__eb_categories WHERE parent=' . $catId . ' AND published=1';
				$db->setQuery($sql);
				$rows = $db->loadObjectList();
				for ($i = 0, $n = count($rows); $i < $n; $i++)
				{
					$row       = $rows[$i];
					$arrCats[] = $row->id;
					$cats[]    = $row->id;
				}
			}
		}

		if ($hidePastEvents)
			$sql = 'SELECT COUNT(a.id) FROM #__eb_events AS a INNER JOIN #__eb_event_categories AS b ON a.id = b.event_id WHERE b.category_id IN(' .
				implode(',', $cats) . ') AND published = 1 AND `access` IN (' . implode(',', $user->getAuthorisedViewLevels()) .
				') AND event_date >= "' . JHtml::_('date', 'Now', 'Y-m-d') . '" ';
		else
			$sql = 'SELECT COUNT(a.id) FROM #__eb_events AS a INNER JOIN #__eb_event_categories AS b ON a.id = b.event_id WHERE b.category_id IN(' .
				implode(',', $cats) . ') AND `access` IN (' . implode(',', $user->getAuthorisedViewLevels()) . ') AND published = 1 ';

		$db->setQuery($sql);

		return (int) $db->loadResult();
	}

	/**
	 * Get all dependencies custom fields
	 *
	 * @param $id
	 *
	 * @return array
	 */
	public static function getAllDependencyFields($id)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$queue  = array($id);
		$fields = array($id);

		while (count($queue))
		{
			$masterFieldId = array_pop($queue);

			//Get list of dependency fields of this master field
			$query->clear();
			$query->select('id')
				->from('#__eb_fields')
				->where('depend_on_field_id=' . $masterFieldId);
			$db->setQuery($query);
			$rows = $db->loadObjectList();
			if (count($rows))
			{
				foreach ($rows as $row)
				{
					$queue[]  = $row->id;
					$fields[] = $row->id;
				}
			}
		}

		return $fields;
	}

	/**
	 * Check to see whether this event still accept registration
	 *
	 * @param object $event
	 *
	 * @return Boolean
	 */
	public static function acceptRegistration($event)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$user  = JFactory::getUser();
		if ($event->registration_type == 3)
		{
			return false;
		}

		if (!in_array($event->registration_access, $user->getAuthorisedViewLevels()))
		{
			return false;
		}

		if ($event->registration_start_minutes < 0)
		{
			return false;
		}

		if ($event->cut_off_date != $db->getNullDate() && $event->cut_off_minutes > 0)
		{
			return false;
		}

		if ($event->number_event_dates < 0)
		{
			return false;
		}

		if ($event->event_capacity && ($event->total_registrants >= $event->event_capacity))
		{
			return false;
		}

		$config = self::getConfig();

		//Check to see whether the current user has registered for the event
		$preventDuplicateRegistration = $config->prevent_duplicate_registration;
		if ($preventDuplicateRegistration && $user->id)
		{
			$query->clear();
			$query->select('COUNT(id)')
				->from('#__eb_registrants')
				->where('event_id = ' . $event->id)
				->where('user_id = ' . $user->id)
				->where('(published=1 OR (payment_method LIKE "os_offline%" AND published NOT IN (2,3)))');
			$db->setQuery($query);
			$total = $db->loadResult();
			if ($total)
			{
				return false;
			}
		}

		if (!$config->multiple_booking)
		{
			// Check for quantity fields
			$query->clear();
			$query->select('*')
				->from('#__eb_fields')
				->where('published=1')
				->where('quantity_field = 1')
				->where('quantity_values != ""')
				->where(' `access` IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')');

			if ($config->custom_field_by_category)
			{
				//Get main category of the event
				$sql = 'SELECT category_id FROM #__eb_event_categories WHERE event_id=' . $event->id . ' AND main_category = 1';
				$db->setQuery($sql);
				$categoryId = (int) $db->loadResult();
				$query->where('(category_id = -1 OR id IN (SELECT field_id FROM #__eb_field_categories WHERE category_id=' . $categoryId . '))');
			}
			else
			{
				$query->where(' (event_id = -1 OR id IN (SELECT field_id FROM #__eb_field_events WHERE event_id=' . $event->id . '))');
			}

			$db->setQuery($query);
			$quantityFields = $db->loadObjectList();
			if (count($quantityFields))
			{
				foreach ($quantityFields as $field)
				{
					$values         = explode("\r\n", $field->values);
					$quantityValues = explode("\r\n", $field->quantity_values);
					if (count($values) && count($quantityValues))
					{
						$values = EventbookingHelperHtml::getAvailableQuantityOptions($values, $quantityValues, $event->id, $field->id, ($field->fieldtype == 'Checkboxes') ? true : false);
						if (!count($values))
						{
							return false;
						}
					}
				}
			}
		}


		return true;
	}

	/**
	 * Get total registrants
	 *
	 */
	public static function getTotalRegistrants($eventId)
	{
		$db  = JFactory::getDbo();
		$sql = 'SELECT SUM(number_registrants) AS total_registrants FROM #__eb_registrants WHERE event_id=' . $eventId .
			' AND group_id=0 AND (published=1 OR (payment_method LIKE "os_offline%" AND published NOT IN (2,3)))';
		$db->setQuery($sql);
		$numberRegistrants = (int) $db->loadResult();

		return $numberRegistrants;
	}

	/**
	 * Get max number of registrants allowed for an event
	 *
	 * @param int    $eventId
	 * @param object $config
	 */
	public static function getMaxNumberRegistrants($event)
	{
		$eventCapacity  = (int) $event->event_capacity;
		$maxGroupNumber = (int) $event->max_group_number;
		if ($eventCapacity)
		{
			$maxRegistrants = $eventCapacity - $event->total_registrants;
		}
		else
		{
			$maxRegistrants = -1;
		}
		if ($maxGroupNumber)
		{
			if ($maxRegistrants == -1)
			{
				$maxRegistrants = $maxGroupNumber;
			}
			else
			{
				$maxRegistrants = $maxRegistrants > $maxGroupNumber ? $maxGroupNumber : $maxRegistrants;
			}
		}

		if ($maxRegistrants == -1)
		{
			//Default max registrants, we should only allow smaller than 10 registrants to make the form not too long
			$maxRegistrants = 10;
		}

		return $maxRegistrants;
	}

	/**
	 * Get registration rate for group registration
	 *
	 * @param $eventId
	 * @param $numberRegistrants
	 *
	 * @return mixed
	 */
	public static function getRegistrationRate($eventId, $numberRegistrants)
	{
		$db  = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('price')
			->from('#__eb_event_group_prices')
			->where('event_id = '. $eventId)
			->where('registrant_number <= '.$numberRegistrants)
			->order('registrant_number DESC');
		$db->setQuery($query, 0, 1);
		$rate = $db->loadResult();
		if (!$rate)
		{
			$query->clear();
			$query->select('individual_price')
				->from('#__eb_events')
				->where('id = '. $eventId);
			$db->setQuery($query);
			$rate = $db->loadResult();
		}

		return $rate;
	}

	/**
	 * Check to see whether the ideal payment plugin installed and activated
	 * @return boolean
	 */
	public static function idealEnabled()
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('COUNT(id)')
			->from('#__eb_payment_plugins')
			->where('name = "os_ideal"')
			->where('published = 1');
		$db->setQuery($query);
		$total = $db->loadResult();
		if ($total)
		{
			if (file_exists(JPATH_ROOT . '/components/com_eventbooking/payments/Mollie/API/Autoloader.php'))
			{
				require_once JPATH_ROOT . '/components/com_eventbooking/payments/Mollie/API/Autoloader.php';
			}
			else
			{
				require_once JPATH_ROOT . '/components/com_eventbooking/payments/ideal/ideal.class.php';
			}

			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Get list of banks for ideal payment plugin
	 * @return array
	 */
	public static function getBankLists()
	{
		$idealPlugin = os_payments::loadPaymentMethod('os_ideal');
		$params      = new JRegistry($idealPlugin->params);
		if (file_exists(JPATH_ROOT . '/components/com_eventbooking/payments/Mollie/API/Autoloader.php'))
		{
			$mollie = new Mollie_API_Client();
			$mollie->setApiKey($params->get('api_key'));
			$bankLists = array();
			$issuers   = $mollie->issuers->all();
			foreach ($issuers as $issuer)
			{
				if ($issuer->method == Mollie_API_Object_Method::IDEAL)
				{
					$bankLists[$issuer->id] = $issuer->name;
				}
			}
		}
		else
		{
			$partnerId = $params->get('partner_id');
			$ideal     = new iDEAL_Payment($partnerId);
			if (!$params->get('ideal_mode', 0))
			{
				$ideal->setTestmode(true);
			}
			$bankLists = $ideal->getBanks();
		}

		return $bankLists;
	}

	/**
	 * Send notification emails to waiting list users when someone cancel registration
	 *
	 * @param $row
	 * @param $config
	 */
	public static function notifyWaitingList($row, $config)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__eb_registrants')
			->where('event_id=' . (int) $row->event_id)
			->where('group_id = 0')
			->where('published = 3')
			->order('id');
		$db->setQuery($query);
		$registrants = $db->loadObjectList();
		if (count($registrants))
		{
			$mailer = JFactory::getMailer();
			if ($config->from_name)
			{
				$fromName = $config->from_name;
			}
			else
			{
				$fromName = JFactory::getConfig()->get('fromname');
			}
			if ($config->from_email)
			{
				$fromEmail = $config->from_email;
			}
			else
			{
				$fromEmail = JFactory::getConfig()->get('mailfrom');
			}
			$message                           = EventbookingHelper::getMessages();
			$fieldSuffix                       = EventbookingHelper::getFieldSuffix();
			$replaces                          = array();
			$replaces['registrant_first_name'] = $row->first_name;
			$replaces['registrant_last_name']  = $row->last_name;
			if (JFactory::getApplication()->isSite())
			{
				$replaces['event_link'] = JUri::getInstance()->toString(array('scheme', 'user', 'pass', 'host')) . JRoute::_(EventbookingHelperRoute::getEventRoute($row->event_id, 0, EventbookingHelper::getItemid()));
			}
			else
			{
				$replaces['event_link'] = JUri::getInstance()->toString(array('scheme', 'user', 'pass', 'host')) . EventbookingHelperRoute::getEventRoute($row->event_id, 0, EventbookingHelper::getItemid());
			}
			$query->clear();
			$query->select('*, title' . $fieldSuffix . ' AS title')
				->from('#__eb_events')
				->where('id = ' . (int) $row->event_id);
			$db->setQuery($query);
			$rowEvent                   = $db->loadObject();
			$replaces['event_title']    = $rowEvent->title;
			$replaces['event_date']     = JHtml::_('date', $rowEvent->event_date, $config->event_date_format, null);
			$replaces['event_end_date'] = JHtml::_('date', $rowEvent->event_end_date, $config->event_date_format, null);

			if (strlen(trim($message->{'registrant_waitinglist_notification_subject' . $fieldSuffix})))
			{
				$subject = $message->{'registrant_waitinglist_notification_subject' . $fieldSuffix};
			}
			else
			{
				$subject = $message->registrant_waitinglist_notification_subject;
			}


			if (empty($subject))
			{
				//Admin has not entered email subject and email message for notification yet, simply return
				return false;
			}
			if (strlen(trim(strip_tags($message->{'registrant_waitinglist_notification_body' . $fieldSuffix}))))
			{
				$body = $message->{'registrant_waitinglist_notification_body' . $fieldSuffix};
			}
			else
			{
				$body = $message->registrant_waitinglist_notification_body;
			}
			foreach ($registrants as $registrant)
			{
				$message                = $body;
				$replaces['first_name'] = $registrant->first_name;
				$replaces['last_name']  = $registrant->last_name;
				foreach ($replaces as $key => $value)
				{
					$key     = strtoupper($key);
					$subject = str_replace("[$key]", $value, $subject);
					$message = str_replace("[$key]", $value, $message);
				}
				//Send email to waiting list users
				$mailer->sendMail($fromEmail, $fromName, $registrant->email, $subject, $message, 1);
				$mailer->ClearAllRecipients();
			}
		}
	}

	/**
	 * Helper function for sending emails to registrants and administrator
	 *
	 * @param RegistrantEventBooking $row
	 * @param object                 $config
	 */
	public static function sendEmails($row, $config)
	{
		$db          = JFactory::getDbo();
		$query       = $db->getQuery(true);
		$message     = self::getMessages();
		$fieldSuffix = self::getFieldSuffix($row->language);
		$mailer      = JFactory::getMailer();
		if ($config->from_name)
		{
			$fromName = $config->from_name;
		}
		else
		{
			$fromName = JFactory::getConfig()->get('from_name');
		}
		if ($config->from_email)
		{
			$fromEmail = $config->from_email;
		}
		else
		{
			$fromEmail = JFactory::getConfig()->get('mailfrom');
		}
		$query->select('*, title' . $fieldSuffix . ' AS title')
			->from('#__eb_events')
			->where('id=' . $row->event_id);
		$db->setQuery($query);
		$event = $db->loadObject();
		if ($config->multiple_booking)
		{
			$rowFields = self::getFormFields($row->id, 4);
		}
		elseif ($row->is_group_billing)
		{
			$rowFields = self::getFormFields($row->event_id, 1);
		}
		else
		{
			$rowFields = self::getFormFields($row->event_id, 0);
		}
		$form = new RADForm($rowFields);
		$data = self::getRegistrantData($row, $rowFields);
		$form->bind($data);
		$form->buildFieldsDependency();
		$replaces = self::buildTags($row, $form, $event, $config);
		// Need to get rowLocation from database
		$query->clear();
		$query->select('a.*')
			->from('#__eb_locations AS a')
			->innerJoin('#__eb_events AS b ON a.id=b.location_id')
			->where('b.id=' . $row->event_id);

		$db->setQuery($query);
		$rowLocation = $db->loadObject();

		// Notification email send to user
		if (strlen($message->{'user_email_subject' . $fieldSuffix}))
		{
			$subject = $message->{'user_email_subject' . $fieldSuffix};
		}
		else
		{
			$subject = $message->user_email_subject;
		}
		if (strpos($row->payment_method, 'os_offline') !== false)
		{
			if (strlen(trim(strip_tags($event->user_email_body_offline))))
			{
				$body = $event->user_email_body_offline;
			}
			elseif (strlen($message->{'user_email_body_offline' . $fieldSuffix}))
			{
				$body = $message->{'user_email_body_offline' . $fieldSuffix};
			}
			else
			{
				$body = $message->user_email_body_offline;
			}
		}
		else
		{
			if (strlen(trim(strip_tags($event->user_email_body))))
			{
				$body = $event->user_email_body;
			}
			elseif (strlen($message->{'user_email_body' . $fieldSuffix}))
			{
				$body = $message->{'user_email_body' . $fieldSuffix};
			}
			else
			{
				$body = $message->user_email_body;
			}
		}
		foreach ($replaces as $key => $value)
		{
			$key     = strtoupper($key);
			$subject = str_replace("[$key]", $value, $subject);
			$body    = str_replace("[$key]", $value, $body);
		}
		$body        = self::convertImgTags($body);
		$attachments = array();
		if ($config->activate_invoice_feature && $config->send_invoice_to_customer && EventbookingHelper::needInvoice($row))
		{
			if (!$row->invoice_number)
			{
				$row->invoice_number = self::getInvoiceNumber();
				$row->store();
			}
			self::generateInvoicePDF($row);
			$attachments[] = JPATH_ROOT . '/media/com_eventbooking/invoices/' . self::formatInvoiceNumber($row->invoice_number, $config) . '.pdf';
		}
		if ($config->multiple_booking)
		{
			$query->clear();
			$query->select('attachment')
				->from('#__eb_events')
				->where('id IN (SELECT event_id FROM #__eb_registrants AS a WHERE a.id=' . $row->id . ' OR a.cart_id=' . $row->id . ' ORDER BY a.id)');
			$db->setQuery($query);
			$attachmentFiles = $db->loadColumn();
			foreach ($attachmentFiles as $attachmentFile)
			{
				if ($attachmentFile)
				{
					$attachments[] = JPATH_ROOT . '/media/com_eventbooking/' . $attachmentFile;
				}
			}
		}
		else
		{
			if ($event->attachment)
			{
				$attachments[] = JPATH_ROOT . '/media/com_eventbooking/' . $event->attachment;
			}
		}

		//Generate and send ics file to registrants
		if ($config->send_ics_file)
		{
			$ics = new EventbookingHelperIcs();
			$ics->setName($event->title)
				->setDescription($event->short_description)
				->setOrganizer($fromEmail, $fromName)
				->setStart($event->event_date)
				->setEnd($event->event_end_date);

			if ($rowLocation)
			{
				$ics->setLocation($rowLocation->name);
			}
			$fileName      = JApplication::stringURLSafe($event->title) . '.ics';
			$attachments[] = $ics->save(JPATH_ROOT . '/media/com_eventbooking/icsfiles/', $fileName);
		}

		$mailer->sendMail($fromEmail, $fromName, $row->email, $subject, $body, 1, null, null, $attachments);

		if ($config->send_email_to_group_members && $row->is_group_billing)
		{
			$query->clear();
			$query->select('*')
				->from('#__eb_registrants')
				->where('group_id = '. $row->id)
				->order('id');
			$db->setQuery($query);
			$rowMembers = $db->loadObjectList();
			if (count($rowMembers))
			{
				$memberReplaces                      = array();
				$memberReplaces['event_title']       = $replaces['event_title'];
				$memberReplaces['event_date']        = $replaces['event_date'];
				$memberReplaces['transaction_id']    = $replaces['transaction_id'];
				$memberReplaces['date']              = $replaces['date'];
				$memberReplaces['short_description'] = $replaces['short_description'];
				$memberReplaces['description']       = $replaces['short_description'];
				$memberReplaces['location']          = $replaces['location'];
				$memberFormFields                    = self::getFormFields($row->event_id, 2);
				foreach ($rowMembers as $rowMember)
				{
					if (!$rowMember->email)
					{
						continue;
					}
					if (strlen($message->{'group_member_email_subject' . $fieldSuffix}))
					{
						$subject = $message->{'group_member_email_subject' . $fieldSuffix};
					}
					else
					{
						$subject = $message->group_member_email_subject;
					}
					if (strlen(strip_tags($message->{'group_member_email_body' . $fieldSuffix})))
					{
						$body = $message->{'group_member_email_body' . $fieldSuffix};
					}
					else
					{
						$body = $message->group_member_email_body;
					}
					if (!$subject)
					{
						break;
					}
					if (!$body)
					{
						break;
					}
					//Build the member form
					$memberForm = new RADForm($memberFormFields);
					$memberData = self::getRegistrantData($rowMember, $memberFormFields);
					$memberForm->bind($memberData);
					$memberForm->buildFieldsDependency();
					$fields = $memberForm->getFields();
					foreach ($fields as $field)
					{
						if ($field->hideOnDisplay)
						{
							$fieldValue = '';
						}
						else
						{
							if (is_string($field->value) && is_array(json_decode($field->value)))
							{
								$fieldValue = implode(', ', json_decode($field->value));
							}
							else
							{
								$fieldValue = $field->value;
							}
						}
						$memberReplaces[$field->name] = $fieldValue;
					}
					$memberReplaces['member_detail'] = self::getMemberDetails($config, $rowMember, $event, $rowLocation, true, $memberForm);
					foreach ($memberReplaces as $key => $value)
					{
						$key     = strtoupper($key);
						$body    = str_replace("[$key]", $value, $body);
						$subject = str_replace("[$key]", $value, $subject);
					}
					$body = self::convertImgTags($body);
					$mailer->ClearAllRecipients();
					$mailer->sendMail($fromEmail, $fromName, $rowMember->email, $subject, $body, 1, null);
				}
			}
		}

		// Clear attachments
		$mailer->ClearAttachments();

		// Add attachment to admin email if needed
		if ($config->send_invoice_to_admin)
		{
			$invoiceFilePath = JPATH_ROOT . '/media/com_eventbooking/invoices/' . self::formatInvoiceNumber($row->invoice_number, $config) . '.pdf';
			if (file_exists($invoiceFilePath))
			{
				$mailer->addAttachment($invoiceFilePath);
			}
		}

		//Send emails to notification emails
		if (strlen(trim($event->notification_emails)) > 0)
		{
			$config->notification_emails = $event->notification_emails;
		}
		if ($config->notification_emails == '')
		{
			$notificationEmails = $fromEmail;
		}
		else
		{
			$notificationEmails = $config->notification_emails;
		}
		$notificationEmails = str_replace(' ', '', $notificationEmails);
		$emails             = explode(',', $notificationEmails);
		if (strlen($message->{'admin_email_subject' . $fieldSuffix}))
		{
			$subject = $message->{'admin_email_subject' . $fieldSuffix};
		}
		else
		{
			$subject = $message->admin_email_subject;
		}
		if (strlen(strip_tags($message->{'admin_email_body' . $fieldSuffix})))
		{
			$body = $message->{'admin_email_body' . $fieldSuffix};
		}
		else
		{
			$body = $message->admin_email_body;
		}

		if ($row->payment_method == 'os_offline_creditcard')
		{
			$replaces['registration_detail'] = self::getEmailContent($config, $row, true, $form, true);
		}
		foreach ($replaces as $key => $value)
		{
			$key     = strtoupper($key);
			$subject = str_replace("[$key]", $value, $subject);
			$body    = str_replace("[$key]", $value, $body);
		}
		$body = self::convertImgTags($body);

		for ($i = 0, $n = count($emails); $i < $n; $i++)
		{
			$email = $emails[$i];
			$mailer->ClearAllRecipients();
			if ($email)
			{
				$mailer->sendMail($fromEmail, $fromName, $email, $subject, $body, 1);
			}
		}
	}

	/**
	 * Helper function for sending emails to registrants and administrator
	 *
	 * @param RegistrantEventBooking $row
	 * @param object                 $config
	 */
	public static function sendRegistrationApprovedEmail($row, $config)
	{
		$mailer = JFactory::getMailer();
		$db     = JFactory::getDbo();
		$query  = $db->getQuery(true);
		self::loadLanguage();
		$message     = self::getMessages();
		$fieldSuffix = self::getFieldSuffix($row->language);
		if ($config->from_name)
		{
			$fromName = $config->from_name;
		}
		else
		{
			$fromName = JFactory::getConfig()->get('fromname');
		}
		if ($config->from_email)
		{
			$fromEmail = $config->from_email;
		}
		else
		{
			$fromEmail = JFactory::getConfig()->get('mailfrom');
		}
		if ($config->multiple_booking)
		{
			$rowFields = self::getFormFields($row->id, 4);
		}
		elseif ($row->is_group_billing)
		{
			$rowFields = self::getFormFields($row->event_id, 1);
		}
		else
		{
			$rowFields = self::getFormFields($row->event_id, 0);
		}
		$form = new RADForm($rowFields);
		$data = self::getRegistrantData($row, $rowFields);
		$form->bind($data);
		$form->buildFieldsDependency();
		$query->select('*, title' . $fieldSuffix . ' AS title')
			->from('#__eb_events')
			->where('id=' . $row->event_id);
		$db->setQuery($query);
		$event    = $db->loadObject();
		$replaces = self::buildTags($row, $form, $event, $config);
		if (strlen(trim($event->registration_approved_email_subject)))
		{
			$subject = $event->registration_approved_email_subject;
		}
		elseif (strlen($message->{'registration_approved_email_subject' . $fieldSuffix}))
		{
			$subject = $message->{'registration_approved_email_subject' . $fieldSuffix};
		}
		else
		{
			$subject = $message->registration_approved_email_subject;
		}

		if (strlen(trim(strip_tags($event->registration_approved_email_body))))
		{
			$body = $event->registration_approved_email_body;
		}
		elseif (strlen($message->{'registration_approved_email_body' . $fieldSuffix}))
		{
			$body = $message->{'registration_approved_email_body' . $fieldSuffix};
		}
		else
		{
			$body = $message->registration_approved_email_body;
		}
		foreach ($replaces as $key => $value)
		{
			$key     = strtoupper($key);
			$subject = str_replace("[$key]", $value, $subject);
			$body    = str_replace("[$key]", $value, $body);
		}
		$body = self::convertImgTags($body);
		$mailer->sendMail($fromEmail, $fromName, $row->email, $subject, $body, 1);
	}

	/**
	 * Send email when users fill-in waitinglist
	 *
	 * @param  object $row
	 * @param object  $config
	 */
	public static function sendWaitinglistEmail($row, $config)
	{
		$db          = JFactory::getDbo();
		$mailer      = JFactory::getMailer();
		$query       = $db->getQuery(true);
		$message     = self::getMessages();
		$fieldSuffix = self::getFieldSuffix($row->language);
		if ($config->from_name)
		{
			$fromName = $config->from_name;
		}
		else
		{
			$fromName = JFactory::getConfig()->get('fromname');
		}
		if ($config->from_email)
		{
			$fromEmail = $config->from_email;
		}
		else
		{
			$fromEmail = JFactory::getConfig()->get('mailfrom');
		}

		$query->select('*, title' . $fieldSuffix . ' AS title')
			->from('#__eb_events')
			->where('id=' . $row->event_id);
		$db->setQuery($query);
		$event = $db->loadObject();
		if ($config->multiple_booking)
		{
			$rowFields = self::getFormFields($row->id, 4);
		}
		elseif ($row->is_group_billing)
		{
			$rowFields = self::getFormFields($row->event_id, 1);
		}
		else
		{
			$rowFields = self::getFormFields($row->event_id, 0);
		}
		$form = new RADForm($rowFields);
		$data = self::getRegistrantData($row, $rowFields);
		$form->bind($data);
		$form->buildFieldsDependency();
		$replaces = self::buildTags($row, $form, $event, $config);
		//Notification email send to user
		if (strlen($message->{'watinglist_confirmation_subject' . $fieldSuffix}))
		{
			$subject = $message->{'watinglist_confirmation_subject' . $fieldSuffix};
		}
		else
		{
			$subject = $message->watinglist_confirmation_subject;
		}
		if (strlen(strip_tags($message->{'watinglist_confirmation_body' . $fieldSuffix})))
		{
			$body = $message->{'watinglist_confirmation_body' . $fieldSuffix};
		}
		else
		{
			$body = $message->watinglist_confirmation_body;
		}
		$subject = str_replace('[EVENT_TITLE]', $event->title, $subject);
		foreach ($replaces as $key => $value)
		{
			$key  = strtoupper($key);
			$body = str_replace("[$key]", $value, $body);
		}
		$mailer->sendMail($fromEmail, $fromName, $row->email, $subject, $body, 1);
		//Send emails to notification emails
		if (strlen(trim($event->notification_emails)) > 0)
		{
			$config->notification_emails = $event->notification_emails;
		}
		if ($config->notification_emails == '')
		{
			$notificationEmails = $fromEmail;
		}
		else
		{
			$notificationEmails = $config->notification_emails;
		}
		$notificationEmails = str_replace(' ', '', $notificationEmails);
		$emails             = explode(',', $notificationEmails);
		if (strlen($message->{'watinglist_notification_subject' . $fieldSuffix}))
		{
			$subject = $message->{'watinglist_notification_subject' . $fieldSuffix};
		}
		else
		{
			$subject = $message->watinglist_notification_subject;
		}
		if (strlen(strip_tags($message->{'watinglist_notification_body' . $fieldSuffix})))
		{
			$body = $message->{'watinglist_notification_body' . $fieldSuffix};
		}
		else
		{
			$body = $message->watinglist_notification_body;
		}
		$subject = str_replace('[EVENT_TITLE]', $event->title, $subject);
		foreach ($replaces as $key => $value)
		{
			$key  = strtoupper($key);
			$body = str_replace("[$key]", $value, $body);
		}
		$body = self::convertImgTags($body);
		for ($i = 0, $n = count($emails); $i < $n; $i++)
		{
			$email = $emails[$i];
			$mailer->ClearAllRecipients();
			$mailer->sendMail($fromEmail, $fromName, $email, $subject, $body, 1);
		}
	}

	/**
	 * Get country code
	 *
	 * @param string $countryName
	 *
	 * @return string
	 */
	public static function getCountryCode($countryName)
	{
		$db = JFactory::getDbo();
		if (empty($countryName))
		{
			$countryName = self::getConfigValue('default_country');
		}
		$sql = 'SELECT country_2_code FROM #__eb_countries WHERE LOWER(name)="' . JString::strtolower($countryName) . '"';
		$db->setQuery($sql);
		$countryCode = $db->loadResult();
		if (!$countryCode)
			$countryCode = 'US';

		return $countryCode;
	}

	/**
	 * Get color code of an event based on in category
	 *
	 * @param int $eventId
	 *
	 * @return Array
	 */
	public static function getColorCodeOfEvent($eventId)
	{
		static $colors;
		if (!isset($colors[$eventId]))
		{
			$db  = JFactory::getDbo();
			$sql = 'SELECT color_code FROM #__eb_categories AS a INNER JOIN #__eb_event_categories AS b ON a.id = b.category_id WHERE b.event_id=' .
				$eventId . ' ORDER BY b.id DESC';
			$db->setQuery($sql);
			$colors[$eventId] = $db->loadResult();
		}

		return $colors[$eventId];
	}

	/**
	 * Get categories of the given events
	 *
	 * @param array $eventIds
	 *
	 * @return array
	 */
	public static function getCategories($eventIds = array())
	{
		if (count($eventIds))
		{
			$db          = JFactory::getDbo();
			$query       = $db->getQuery(true);
			$fieldSuffix = EventbookingHelper::getFieldSuffix();
			$query->select('a.id, a.name' . $fieldSuffix . ' AS name, a.color_code')
				->from('#__eb_categories AS a')
				->where('published = 1')
				->where('id IN (SELECT category_id FROM #__eb_event_categories WHERE event_id IN (' . implode(',', $eventIds) . ') AND main_category = 1)');
			$db->setQuery($query);

			return $db->loadObjectList();
		}
		else
		{
			return array();
		}
	}

	/**
	 * Get title of the given payment method
	 *
	 * @param string $methodName
	 */
	public static function getPaymentMethodTitle($methodName)
	{
		static $titles;
		if (!isset($titles[$methodName]))
		{
			$db  = JFactory::getDbo();
			$sql = 'SELECT title FROM #__eb_payment_plugins WHERE name="' . $methodName . '"';
			$db->setQuery($sql);
			$methodTitle = $db->loadResult();
			if ($methodTitle)
			{
				$titles[$methodName] = $methodTitle;
			}
			else
			{
				$titles[$methodName] = $methodName;
			}
		}

		return $titles[$methodName];
	}

	/**
	 * Display copy right information
	 *
	 */
	public static function displayCopyRight()
	{
		echo '<div class="copyright" style="text-align:center;margin-top: 5px;"><a href="http://joomdonation.com/joomla-extensions/events-booking-joomla-events-registration.html" target="_blank"><strong>Event Booking</strong></a> version ' .
			self::getInstalledVersion() . ', Copyright (C) 2010 - ' . date('Y') .
			' <a href="http://joomdonation.com" target="_blank"><strong>Ossolution Team</strong></a></div>';
	}

	/**
	 * Load jquery library
	 */
	public static function loadJQuery()
	{
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			JHtml::_('jquery.framework');
		}
		else
		{
			$document = JFactory::getDocument();
			$document->addScript(JUri::root(true) . '/media/com_eventbooking/assets/bootstrap/js/jquery.min.js');
			$document->addScript(JUri::root(true) . '/media/com_eventbooking/assets/bootstrap/js/jquery-noconflict.js');
		}
	}

	/**
	 * Load bootstrap css and javascript file
	 */
	public static function loadBootstrap($loadJs = true)
	{
		$app      = JFactory::getApplication();
		$document = JFactory::getDocument();
		$rootUrl  = JUri::root(true);
		$document->addStyleSheet($rootUrl . '/media/com_eventbooking/assets/bootstrap/css/bootstrap.css');

		// Load bootstrap tabs css
		if ($app->isAdmin())
		{
			$document->addStyleSheet($rootUrl . '/media/com_eventbooking/assets/bootstrap/css/bootstrap-tabs-backend.css');
		}
		else
		{
			$document->addStyleSheet($rootUrl . '/media/com_eventbooking/assets/bootstrap/css/bootstrap-tabs.css');
		}
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			if ($loadJs && $app->isAdmin())
			{
				JHtml::_('bootstrap.framework');
			}
			elseif ($loadJs && $app->isSite())
			{
				JHtml::_('script', 'jui/bootstrap.min.js', false, true, false, false, false);
			}
		}
		else
		{
			if ($loadJs && $app->isAdmin())
			{
				$document->addScript($rootUrl . '/media/com_eventbooking/assets/bootstrap/js/jquery.min.js');
				$document->addScript($rootUrl . '/media/com_eventbooking/assets/bootstrap/js/jquery-noconflict.js');
				$document->addScript($rootUrl . '/media/com_eventbooking/assets/bootstrap/js/bootstrap.min.js');
			}
			elseif ($loadJs && $app->isSite())
			{
				$document->addScript($rootUrl . '/media/com_eventbooking/assets/bootstrap/js/bootstrap.min.js');
			}
		}
	}

	/**
	 * Helper method to load bootstrap js using for bootstrap dropdown
	 */
	public static function loadBootstrapJs()
	{
		if (version_compare(JVERSION, '3.0.0', 'ge'))
		{
			JHtml::_('script', 'jui/bootstrap.min.js', false, true, false, false, false);
		}
		else
		{
			JFactory::getDbo()->addScript(JUri::root(true) . '/media/com_eventbooking/assets/bootstrap/js/bootstrap.min.js');
		}
	}

	/**
	 * Get version number of GD version installed
	 * Enter description here ...
	 *
	 * @param unknown_type $user_ver
	 */
	public static function getGDVersion($user_ver = 0)
	{
		if (!extension_loaded('gd'))
		{
			return 0;
		}

		static $gd_ver = 0;

		// just accept the specified setting if it's 1.
		if ($user_ver == 1)
		{
			$gd_ver = 1;

			return 1;
		}

		// use static variable if function was cancelled previously.
		if ($user_ver != 2 && $gd_ver > 0)
		{
			return $gd_ver;
		}

		// use the gd_info() function if posible.
		if (function_exists('gd_info'))
		{
			$ver_info = gd_info();
			$match    = null;
			preg_match('/\d/', $ver_info['GD Version'], $match);
			$gd_ver = $match[0];

			return $match[0];
		}

		// if phpinfo() is disabled use a specified / fail-safe choice...
		if (preg_match('/phpinfo/', ini_get('disable_functions')))
		{
			if ($user_ver == 2)
			{
				$gd_ver = 2;

				return 2;
			}
			else
			{
				$gd_ver = 1;

				return 1;
			}
		}
		// ...otherwise use phpinfo().
		ob_start();
		phpinfo(8);
		$info = ob_get_contents();
		ob_end_clean();
		$info  = stristr($info, 'gd version');
		$match = null;
		preg_match('/\d/', $info, $match);
		$gd_ver = $match[0];

		return $match[0];
	}

	/**
	 *
	 * Resize image to a pre-defined size
	 *
	 * @param string $srcFile
	 * @param string $desFile
	 * @param int    $thumbWidth
	 * @param int    $thumbHeight
	 * @param string $method gd1 or gd2
	 * @param int    $quality
	 */
	public static function resizeImage($srcFile, $desFile, $thumbWidth, $thumbHeight, $quality)
	{
		$app      = JFactory::getApplication();
		$imgTypes = array(
			1  => 'GIF',
			2  => 'JPG',
			3  => 'PNG',
			4  => 'SWF',
			5  => 'PSD',
			6  => 'BMP',
			7  => 'TIFF',
			8  => 'TIFF',
			9  => 'JPC',
			10 => 'JP2',
			11 => 'JPX',
			12 => 'JB2',
			13 => 'SWC',
			14 => 'IFF');
		$imgInfo  = getimagesize($srcFile);
		if ($imgInfo == null)
		{
			$app->enqueueMessage(JText::_('EB_IMAGE_NOT_FOUND', 'error'));

			return false;
		}
		$type             = strtoupper($imgTypes[$imgInfo[2]]);
		$gdSupportedTypes = array('JPG', 'PNG', 'GIF');
		if (!in_array($type, $gdSupportedTypes))
		{
			$app->enqueueMessage(JText::_('EB_ONLY_SUPPORT_TYPES'), 'error');

			return false;
		}
		$srcWidth  = $imgInfo[0];
		$srcHeight = $imgInfo[1];
		//Should canculate the ration
		$ratio     = max($srcWidth / $thumbWidth, $srcHeight / $thumbHeight, 1.0);
		$desWidth  = (int) $srcWidth / $ratio;
		$desHeight = (int) $srcHeight / $ratio;
		$gdVersion = EventbookingHelper::getGDVersion();
		if ($gdVersion <= 0)
		{
			//Simply copy the source to target folder
			jimport('joomla.filesystem.file');
			JFile::copy($srcFile, $desFile);

			return false;
		}
		else
		{
			if ($gdVersion == 1)
			{
				$method = 'gd1';
			}
			else
			{
				$method = 'gd2';
			}
		}
		switch ($method)
		{
			case 'gd1':
				if ($type == 'JPG')
					$srcImage = imagecreatefromjpeg($srcFile);
				elseif ($type == 'PNG')
					$srcImage = imagecreatefrompng($srcFile);
				else
					$srcImage = imagecreatefromgif($srcFile);
				$desImage = imagecreate($desWidth, $desHeight);
				imagecopyresized($desImage, $srcImage, 0, 0, 0, 0, $desWidth, $desHeight, $srcWidth, $srcHeight);
				imagejpeg($desImage, $desFile, $quality);
				imagedestroy($srcImage);
				imagedestroy($desImage);
				break;
			case 'gd2':
				if (!function_exists('imagecreatefromjpeg'))
				{
					echo JText::_('GD_LIB_NOT_INSTALLED');

					return false;
				}
				if (!function_exists('imagecreatetruecolor'))
				{
					echo JText::_('GD2_LIB_NOT_INSTALLED');

					return false;
				}
				if ($type == 'JPG' || $type == 'JPEG')
				{
					$desImage = imagecreatetruecolor($desWidth, $desHeight);
					$srcImage = imagecreatefromjpeg($srcFile);
					imagecopyresampled($desImage, $srcImage, 0, 0, 0, 0, $desWidth, $desHeight, $srcWidth, $srcHeight);
					imagejpeg($desImage, $desFile);
					imagedestroy($desImage);
					imagedestroy($srcImage);

				}
				elseif ($type == 'PNG')
				{
					$desImage = imagecreatetruecolor($desWidth, $desHeight);
					$srcImage = imagecreatefrompng($srcFile);
					imagealphablending($desImage, false);
					imagesavealpha($desImage, true);
					$transparent = imagecolorallocatealpha($desImage, 255, 255, 255, 127);
					imagefilledrectangle($desImage, 0, 0, $desWidth, $desHeight, $transparent);
					imagecopyresampled($desImage, $srcImage, 0, 0, 0, 0, $desWidth, $desHeight, $srcWidth, $srcHeight);
					imagepng($desImage, $desFile);
					imagedestroy($desImage);
					imagedestroy($srcImage);

				}
				elseif ($type == 'GIF')
				{
					$desImage = imagecreatetruecolor($desWidth, $desHeight);
					$srcImage = imagecreatefromgif($srcFile);
					imagecopyresampled($desImage, $srcImage, 0, 0, 0, 0, $desWidth, $desHeight, $srcWidth, $srcHeight);
					imagegif($desImage, $desImage);
					imagedestroy($desImage);
					imagedestroy($srcImage);

				}
				if (!$srcImage)
				{
					echo JText::_('JA_INVALID_IMAGE');

					return false;
				}
				break;
		}

		return true;
	}

	/**
	 * Generate User Input Select
	 *
	 * @param int $userId
	 */
	public static function getUserInput($userId, $fieldName = 'user_id', $registerId)
	{
		// Initialize variables.
		$html = array();
		$link = 'index.php?option=com_users&amp;view=users&amp;layout=modal&amp;tmpl=component&amp;field=user_id';
		// Initialize some field attributes.
		$attr = ' class="inputbox"';
		// Load the modal behavior script.
		JHtml::_('behavior.modal', 'a.modal_user_id');
		// Build the script.
		$script   = array();
		$script[] = '	function jSelectUser_user_id(id, title) {';
		$script[] = '		var old_id = document.getElementById("user_id").value;';
		$script[] = '		if (old_id != id) {';
		$script[] = '			document.getElementById("' . $fieldName . '").value = id;';
		$script[] = '			document.getElementById("user_id_name").value = title;';
		$script[] = '		}';
		if (!$registerId)
		{
			$script[] = ' populateRegisterData(id, document.getElementById("event_id").value, title); ';
		}
		$script[] = '		SqueezeBox.close();';
		$script[] = '	}';
		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));
		// Load the current username if available.
		$table = JTable::getInstance('user');
		if ($userId)
		{
			$table->load($userId);
		}
		else
		{
			$table->name = '';
		}
		// Create a dummy text field with the user name.
		$html[] = '<div class="fltlft">';
		$html[] = '	<input type="text" id="user_id_name"' . ' value="' . htmlspecialchars($table->name, ENT_COMPAT, 'UTF-8') . '"' .
			' disabled="disabled"' . $attr . ' />';
		$html[] = '</div>';
		// Create the user select button.
		$html[] = '<div class="button2-left">';
		$html[] = '<div class="blank">';
		$html[] = '<a class="modal_user_id" title="' . JText::_('JLIB_FORM_CHANGE_USER') . '"' . ' href="' . $link . '"' .
			' rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
		$html[] = '	' . JText::_('JLIB_FORM_CHANGE_USER') . '</a>';
		$html[] = '</div>';
		$html[] = '</div>';
		// Create the real field, hidden, that stored the user id.
		$html[] = '<input type="hidden" id="' . $fieldName . '" name="' . $fieldName . '" value="' . $userId . '" />';

		return implode("\n", $html);
	}

	/**
	 * Get the invoice number for this subscription record
	 */
	public static function getInvoiceNumber()
	{
		$db  = JFactory::getDbo();
		$sql = 'SELECT MAX(invoice_number) FROM #__eb_registrants';
		$db->setQuery($sql);
		$invoiceNumber = (int) $db->loadResult();
		if (!$invoiceNumber)
		{
			$invoiceNumber = (int) self::getConfigValue('invoice_start_number');
			if (!$invoiceNumber)
			{
				$invoiceNumber = 1;
			}
		}
		else
		{
			$invoiceNumber++;
		}

		return $invoiceNumber;
	}

	/**
	 * Format invoice number
	 *
	 * @param string $invoiceNumber
	 * @param Object $config
	 */
	public static function formatInvoiceNumber($invoiceNumber, $config)
	{
		return $config->invoice_prefix .
		str_pad($invoiceNumber, $config->invoice_number_length ? $config->invoice_number_length : 4, '0', STR_PAD_LEFT);
	}

	/**
	 * Generate invoice PDF
	 *
	 * @param object $row
	 */
	public static function generateInvoicePDF($row)
	{
		self::loadLanguage();
		$db          = JFactory::getDbo();
		$query       = $db->getQuery(true);
		$config      = self::getConfig();
		$fieldSuffix = EventbookingHelper::getFieldSuffix($row->language);
		$sitename    = JFactory::getConfig()->get("sitename");
		$query->select('*, title' . $fieldSuffix . ' AS title')
			->from('#__eb_events')
			->where('id = ' . (int) $row->event_id);
		$db->setQuery($query);
		$rowEvent = $db->loadObject();
		require_once JPATH_ROOT . "/components/com_eventbooking/tcpdf/tcpdf.php";
		require_once JPATH_ROOT . "/components/com_eventbooking/tcpdf/config/lang/eng.php";
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor($sitename);
		$pdf->SetTitle('Invoice');
		$pdf->SetSubject('Invoice');
		$pdf->SetKeywords('Invoice');
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		$pdf->SetMargins(PDF_MARGIN_LEFT, 0, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		//set auto page breaks
		$pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
		//set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$pdf->SetFont('times', '', 8);
		$pdf->AddPage();
		if ($config->multiple_booking)
		{
			$invoiceOutput = $config->invoice_format_cart;
		}
		else
		{
			$invoiceOutput = $config->invoice_format;
		}

		if ($config->multiple_booking)
		{
			$rowFields = self::getFormFields($row->id, 4);
		}
		elseif ($row->is_group_billing)
		{
			$rowFields = self::getFormFields($row->event_id, 1);
		}
		else
		{
			$rowFields = self::getFormFields($row->event_id, 0);
		}

		$form = new RADForm($rowFields);
		$data = self::getRegistrantData($row, $rowFields);
		$form->bind($data);
		$form->buildFieldsDependency();

		$replaces                   = self::buildTags($row, $form, $rowEvent, $config);
		$replaces['invoice_number'] = self::formatInvoiceNumber($row->invoice_number, $config);
		$replaces['invoice_date']   = date($config->date_format);

		if ($row->published == 0)
		{
			$invoiceStatus = JText::_('EB_INVOICE_STATUS_PENDING');
		}
		elseif ($row->published == 1)
		{
			$invoiceStatus = JText::_('EB_INVOICE_STATUS_PAID');
		}
		else
		{
			$invoiceStatus = JText::_('EB_INVOICE_STATUS_UNKNOWN');
		}
		$replaces['INVOICE_STATUS'] = $invoiceStatus;
		unset($replaces['total_amount']);
		unset($replaces['discount_amount']);
		unset($replaces['tax_amount']);
		if ($config->multiple_booking)
		{
			$sql = 'SELECT a.title' . $fieldSuffix . ' AS title, a.event_date, b.* FROM #__eb_events AS a INNER JOIN #__eb_registrants AS b ' . ' ON a.id = b.event_id ' .
				' WHERE b.id=' . $row->id . ' OR b.cart_id=' . $row->id;
			$db->setQuery($sql);
			$rowEvents                          = $db->loadObjectList();
			$subTotal                           = $replaces['amt_total_amount'];
			$taxAmount                          = $replaces['amt_tax_amount'];
			$discountAmount                     = $replaces['amt_discount_amount'];
			$total                              = $replaces['amt_amount'];
			$paymentProcessingFee               = $replaces['amt_payment_processing_fee'];
			$replaces['EVENTS_LIST']            = EventbookingHelperHtml::loadCommonLayout(
				JPATH_ROOT . '/components/com_eventbooking/emailtemplates/invoice_items.php',
				array(
					'rowEvents'      => $rowEvents,
					'subTotal'       => $subTotal,
					'taxAmount'      => $taxAmount,
					'discountAmount' => $discountAmount,
					'total'          => $total,
					'config'         => $config));
			$replaces['SUB_TOTAL']              = EventbookingHelper::formatCurrency($subTotal, $config);
			$replaces['DISCOUNT_AMOUNT']        = EventbookingHelper::formatCurrency($discountAmount, $config);
			$replaces['TAX_AMOUNT']             = EventbookingHelper::formatCurrency($taxAmount, $config);
			$replaces['TOTAL_AMOUNT']           = EventbookingHelper::formatCurrency($total, $config);
			$replaces['PAYMENT_PROCESSING_FEE'] = EventbookingHelper::formatCurrency($paymentProcessingFee, $config);
		}
		else
		{
			$replaces['ITEM_QUANTITY']          = 1;
			$replaces['ITEM_AMOUNT']            = $replaces['ITEM_SUB_TOTAL'] = self::formatCurrency($row->total_amount, $config);
			$replaces['DISCOUNT_AMOUNT']        = self::formatCurrency($row->discount_amount, $config);
			$replaces['SUB_TOTAL']              = self::formatCurrency($row->total_amount - $row->discount_amount, $config);
			$replaces['TAX_AMOUNT']             = self::formatCurrency($row->tax_amount, $config);
			$replaces['PAYMENT_PROCESSING_FEE'] = self::formatCurrency($row->payment_processing_fee, $config);
			$replaces['TOTAL_AMOUNT']           = self::formatCurrency($row->total_amount - $row->discount_amount + $row->payment_processing_fee + $row->tax_amount, $config);
			$itemName                           = JText::_('EB_EVENT_REGISTRATION');
			$itemName                           = str_replace('[EVENT_TITLE]', $rowEvent->title, $itemName);
			$replaces['ITEM_NAME']              = $itemName;
		}
		foreach ($replaces as $key => $value)
		{
			$key           = strtoupper($key);
			$invoiceOutput = str_replace("[$key]", $value, $invoiceOutput);
		}

		$invoiceOutput = self::convertImgTags($invoiceOutput);
		$v             = $pdf->writeHTML($invoiceOutput, true, false, false, false, '');
		//Filename
		$filePath = JPATH_ROOT . '/media/com_eventbooking/invoices/' . $replaces['invoice_number'] . '.pdf';
		$pdf->Output($filePath, 'F');
	}

	public static function downloadInvoice($id)
	{
		JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_eventbooking/table');
		$config = self::getConfig();
		$row    = JTable::getInstance('EventBooking', 'Registrant');
		$row->load($id);
		$invoiceStorePath = JPATH_ROOT . '/media/com_eventbooking/invoices/';
		if ($row)
		{
			if (!$row->invoice_number)
			{
				$row->invoice_number = self::getInvoiceNumber();
				$row->store();
			}
			$invoiceNumber = self::formatInvoiceNumber($row->invoice_number, $config);
			self::generateInvoicePDF($row);
			$invoicePath = $invoiceStorePath . $invoiceNumber . '.pdf';
			$fileName    = $invoiceNumber . '.pdf';
			while (@ob_end_clean()) ;
			self::processDownload($invoicePath, $fileName);
		}
	}

	/**
	 * Convert all img tags to use absolute URL
	 *
	 * @param string $html_content
	 */
	public static function convertImgTags($html_content)
	{
		$patterns     = array();
		$replacements = array();
		$i            = 0;
		$src_exp      = "/src=\"(.*?)\"/";
		$link_exp     = "[^http:\/\/www\.|^www\.|^https:\/\/|^http:\/\/]";
		$siteURL      = JUri::root();
		preg_match_all($src_exp, $html_content, $out, PREG_SET_ORDER);
		foreach ($out as $val)
		{
			$links = preg_match($link_exp, $val[1], $match, PREG_OFFSET_CAPTURE);
			if ($links == '0')
			{
				$patterns[$i]     = $val[1];
				$patterns[$i]     = "\"$val[1]";
				$replacements[$i] = $siteURL . $val[1];
				$replacements[$i] = "\"$replacements[$i]";
			}
			$i++;
		}
		$mod_html_content = str_replace($patterns, $replacements, $html_content);

		return $mod_html_content;
	}

	/**
	 * Process download a file
	 *
	 * @param string $file : Full path to the file which will be downloaded
	 */
	public static function processDownload($filePath, $filename, $detectFilename = false)
	{
		$fsize    = @filesize($filePath);
		$mod_date = date('r', filemtime($filePath));
		$cont_dis = 'attachment';
		if ($detectFilename)
		{
			$pos      = strpos($filename, '_');
			$filename = substr($filename, $pos + 1);
		}
		$ext  = JFile::getExt($filename);
		$mime = self::getMimeType($ext);
		// required for IE, otherwise Content-disposition is ignored
		if (ini_get('zlib.output_compression'))
		{
			ini_set('zlib.output_compression', 'Off');
		}
		header("Pragma: public");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Expires: 0");
		header("Content-Transfer-Encoding: binary");
		header(
			'Content-Disposition:' . $cont_dis . ';' . ' filename="' . $filename . '";' . ' modification-date="' . $mod_date . '";' . ' size=' . $fsize .
			';'); //RFC2183
		header("Content-Type: " . $mime); // MIME type
		header("Content-Length: " . $fsize);

		if (!ini_get('safe_mode'))
		{ // set_time_limit doesn't work in safe mode
			@set_time_limit(0);
		}

		self::readfile_chunked($filePath);
	}

	/**
	 * Get mimetype of a file
	 *
	 * @return string
	 */
	public static function getMimeType($ext)
	{
		require_once JPATH_ROOT . "/components/com_eventbooking/helper/mime.mapping.php";
		foreach ($mime_extension_map as $key => $value)
		{
			if ($key == $ext)
			{
				return $value;
			}
		}

		return "";
	}

	/**
	 * Read file
	 *
	 * @param string $filename
	 * @param        $retbytes
	 *
	 * @return unknown
	 */
	public static function readfile_chunked($filename, $retbytes = true)
	{
		$chunksize = 1 * (1024 * 1024); // how many bytes per chunk
		$cnt       = 0;
		$handle    = fopen($filename, 'rb');
		if ($handle === false)
		{
			return false;
		}
		while (!feof($handle))
		{
			$buffer = fread($handle, $chunksize);
			echo $buffer;
			@ob_flush();
			flush();
			if ($retbytes)
			{
				$cnt += strlen($buffer);
			}
		}
		$status = fclose($handle);
		if ($retbytes && $status)
		{
			return $cnt; // return num. bytes delivered like readfile() does.
		}

		return $status;
	}

	/**
	 * Check to see whether the current user can
	 *
	 * @param int $eventId
	 */
	public static function checkEventAccess($eventId)
	{
		$user  = JFactory::getUser();
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('`access`')
			->from('#__eb_events')
			->where('id=' . $eventId);
		$db->setQuery($query);
		$access = (int) $db->loadResult();
		if (!in_array($access, $user->getAuthorisedViewLevels()))
		{
			JFactory::getApplication()->redirect('index.php', JText::_('NOT_AUTHORIZED'));
		}
	}

	/**
	 * Check to see whether a users to access to registration history
	 * Enter description here
	 */
	public static function checkAccessHistory()
	{
		$user = JFactory::getUser();
		if (!$user->get('id'))
		{
			JFactory::getApplication()->redirect('index.php?option=com_eventbooking', JText::_('NOT_AUTHORIZED'));
		}
	}

	/**
	 * Check to see whether the current users can access View List function
	 */
	public static function canViewRegistrantList()
	{
		return JFactory::getUser()->authorise('eventbooking.view_registrants_list', 'com_eventbooking');
	}

	/**
	 *
	 * Check to see whether this users has permission to edit registrant
	 */
	public static function checkEditRegistrant($rowRegistrant)
	{
		$user = JFactory::getUser();
		if ($user->authorise('eventbooking.registrants_management', 'com_eventbooking') || ($user->get('id') == $rowRegistrant->user_id) ||
			($user->get('email') == $rowRegistrant->email)
		)
		{
			$canAccess = true;
		}
		else
		{
			$canAccess = false;
		}
		if (!$canAccess)
		{
			JFactory::getApplication()->redirect('index.php', JText::_('NOT_AUTHORIZED'));
		}
	}

	/**
	 * Check to see whether this event can be cancelled
	 *
	 * @param int $eventId
	 *
	 * @return bool
	 */
	public static function canCancel($eventId)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('COUNT(*)')
			->from('#__eb_events')
			->where('id = ' . $eventId)
			->where(' enable_cancel_registration = 1')
			->where('(DATEDIFF(cancel_before_date, NOW()) >=0)');
		$db->setQuery($query);
		$total = $db->loadResult();
		if ($total)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function canExportRegistrants($eventId = 0)
	{
		$user = JFactory::getUser();
		if ($eventId)
		{
			$config = EventbookingHelper::getConfig();
			$db     = JFactory::getDbo();
			$query  = $db->getQuery(true);
			$query->select('created_by')
				->from('#__eb_events')
				->where('id = ' . (int) $eventId);
			$db->setQuery($query);
			$createdBy = (int) $db->loadResult();
			if ($config->only_show_registrants_of_event_owner)
			{
				return $createdBy > 0 && $createdBy == $user->id;
			}
			else
			{
				return (($createdBy > 0 && $createdBy == $user->id) || $user->authorise('eventbooking.registrants_management', 'com_eventbooking'));
			}

		}
		else
		{
			return $user->authorise('eventbooking.registrants_management', 'com_eventbooking');
		}
	}

	public static function canChangeEventStatus($eventId)
	{
		$user  = JFactory::getUser();
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		if (!$eventId)
		{
			return false;
		}
		$query->select('*')
			->from('#__eb_events')
			->where('id = ' . $eventId);
		$db->setQuery($query);
		$rowEvent = $db->loadObject();
		if (!$rowEvent)
		{
			return false;
		}
		if ($user->get('guest'))
		{
			return false;
		}
		if ($user->authorise('core.edit.state', 'com_eventbooking') || ($rowEvent->created_by == $user->get('id')))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Check to see whether the users can cancel registration
	 *
	 * @param int $eventId
	 */
	public static function canCancelRegistration($eventId)
	{
		$db     = JFactory::getDbo();
		$user   = JFactory::getUser();
		$userId = $user->get('id');
		$email  = $user->get('email');
		if (!$userId)
			return false;
		$sql = 'SELECT id FROM #__eb_registrants WHERE event_id=' . $eventId . ' AND (user_id=' . $userId . ' OR email="' . $email .
			'") AND (published=1 OR (payment_method LIKE "os_offline%" AND published NOT IN (2,3)))';
		$db->setQuery($sql);
		$registrantId = $db->loadResult();
		if (!$registrantId)
			return false;

		$sql = 'SELECT COUNT(*) FROM #__eb_events WHERE id=' . $eventId .
			' AND enable_cancel_registration = 1 AND (DATEDIFF(cancel_before_date, NOW()) >=0) ';
		$db->setQuery($sql);
		$total = $db->loadResult();

		if (!$total)
			return false;

		return $registrantId;
	}

	/**
	 * Check to see whether the current user can edit registrant
	 *
	 * @param int $eventId
	 *
	 * @return boolean
	 */
	public static function checkEditEvent($eventId)
	{
		$user = JFactory::getUser();
		$db   = JFactory::getDbo();
		if (!$eventId)
		{
			return false;
		}
		$sql = 'SELECT * FROM #__eb_events WHERE id=' . $eventId;
		$db->setQuery($sql);
		$rowEvent = $db->loadObject();
		if (!$rowEvent)
		{
			return false;
		}
		if ($user->get('guest'))
		{
			return false;
		}
		if ($user->authorise('core.edit', 'com_eventbooking') || ($rowEvent->created_by == $user->get('id')))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function isGroupRegistration($id)
	{
		if (!$id)
			return false;
		$db  = JFactory::getDbo();
		$sql = 'SELECT is_group_billing FROM #__eb_registrants WHERE id=' . $id;
		$db->setQuery($sql);
		$isGroupBilling = (int) $db->loadResult();

		return $isGroupBilling > 0 ? true : false;
	}

	public static function updateGroupRegistrationRecord($groupId)
	{
		$db     = JFactory::getDbo();
		$config = EventbookingHelper::getConfig();
		if ($config->collect_member_information)
		{
			$row = JTable::getInstance('EventBooking', 'Registrant');
			$row->load($groupId);
			if ($row->id)
			{
				$sql = "UPDATE #__eb_registrants SET published=$row->published, transaction_id='$row->transaction_id', payment_method='$row->payment_method' WHERE group_id=" .
					$row->id;
				$db->setQuery($sql);
				$db->execute();
			}
		}
	}

	/**
	 * Check to see whether the current users can add events from front-end
	 *
	 */
	public static function checkAddEvent()
	{
		$user = JFactory::getUser();

		return ($user->id > 0 && $user->authorise('eventbooking.addevent', 'com_eventbooking'));
	}

	/**
	 * Create a user account
	 *
	 * @param array $data
	 *
	 * @return int Id of created user
	 */
	public static function saveRegistration($data)
	{
		//Need to load com_users language file
		$lang = JFactory::getLanguage();
		$tag  = $lang->getTag();
		if (!$tag)
			$tag = 'en-GB';
		$lang->load('com_users', JPATH_ROOT, $tag);
		$data['name']     = $data['first_name'] . ' ' . $data['last_name'];
		$data['password'] = $data['password2'] = $data['password1'];
		$data['email1']   = $data['email2'] = $data['email'];
		require_once JPATH_ROOT . '/components/com_users/models/registration.php';
		$model = new UsersModelRegistration();
		$ret   = $model->register($data);
		$db    = JFactory::getDbo();
		//Need to get the user ID based on username
		$sql = 'SELECT id FROM #__users WHERE username="' . $data['username'] . '"';
		$db->setQuery($sql);

		return (int) $db->loadResult();
	}

	/**
	 * Get list of recurring event dates
	 *
	 * @param DateTime $startDate
	 * @param DateTime $endDate
	 * @param int      $dailyFrequency
	 * @param int      $numberOccurencies
	 *
	 * @return array
	 */
	public static function getDailyRecurringEventDates($startDate, $endDate, $dailyFrequency, $numberOccurencies)
	{
		$eventDates = array($startDate);
		if (version_compare(PHP_VERSION, '5.3.0', 'ge'))
		{
			$timeZone     = new DateTimeZone(JFactory::getConfig()->get('offset'));
			$date         = new DateTime($startDate, $timeZone);
			$dateInterval = new DateInterval('P' . $dailyFrequency . 'D');
			if ($numberOccurencies)
			{
				for ($i = 1; $i < $numberOccurencies; $i++)
				{
					$date->add($dateInterval);
					$eventDates[] = $date->format('Y-m-d H:i:s');
				}
			}
			else
			{
				$recurringEndDate = new DateTime($endDate . ' 23:59:59', $timeZone);
				while (true)
				{
					$date->add($dateInterval);
					if ($date <= $recurringEndDate)
					{
						$eventDates[] = $date->format('Y-m-d H:i:s');
					}
					else
					{
						break;
					}
				}
			}
		}
		else
		{
			// Convert to unix timestamp for easili maintenance
			$startTime = strtotime($startDate);
			$endTime   = strtotime($endDate . ' 23:59:59');
			if ($numberOccurencies)
			{
				$count = 1;
				$i     = 1;
				while ($count < $numberOccurencies)
				{
					$i++;
					$count++;
					$nextEventDate = $startTime + ($i - 1) * $dailyFrequency * 24 * 3600;
					$eventDates[]  = strftime('%Y-%m-%d %H:%M:%S', $nextEventDate);
				}
			}
			else
			{
				$i = 1;
				while (true)
				{
					$i++;
					$nextEventDate = $startTime + ($i - 1) * 24 * $dailyFrequency * 3600;
					if ($nextEventDate <= $endTime)
					{
						$eventDates[] = strftime('%Y-%m-%d %H:%M:%S', $nextEventDate);
					}
					else
					{
						break;
					}
				}
			}
		}

		return $eventDates;
	}

	/**
	 * Get weekly recurring event dates
	 *
	 * @param DateTime $startDate
	 * @param DateTime $endDate
	 * @param Int      $weeklyFrequency
	 * @param int      $numberOccurrences
	 * @param array    $weekDays
	 *
	 * @return array
	 */
	public static function getWeeklyRecurringEventDates($startDate, $endDate, $weeklyFrequency, $numberOccurrences, $weekDays)
	{
		$eventDates = array();
		if (version_compare(PHP_VERSION, '5.3.0', 'ge'))
		{
			$timeZone           = new DateTimeZone(JFactory::getConfig()->get('offset'));
			$recurringStartDate = new Datetime($startDate, $timeZone);
			$hour               = $recurringStartDate->format('H');
			$minutes            = $recurringStartDate->format('i');
			$startWeek          = clone $recurringStartDate->modify(('Sunday' == $recurringStartDate->format('l')) ? 'Sunday this week' : 'Sunday last week');
			$startWeek->setTime($hour, $minutes, 0);
			$dateInterval = new DateInterval('P' . $weeklyFrequency . 'W');
			if ($numberOccurrences)
			{
				$count = 0;
				while ($count < $numberOccurrences)
				{
					foreach ($weekDays as $weekDay)
					{
						$date = clone $startWeek;
						if ($weekDay > 0)
						{
							$date->add(new DateInterval('P' . $weekDay . 'D'));
						}
						if (($date >= $recurringStartDate) && ($count < $numberOccurrences))
						{
							$eventDates[] = $date->format('Y-m-d H:i:s');
							$count++;
						}
					}
					$startWeek->add($dateInterval);
				}
			}
			else
			{
				$recurringEndDate = new DateTime($endDate . ' 23:59:59', $timeZone);
				while (true)
				{
					foreach ($weekDays as $weekDay)
					{
						$date = clone $startWeek;
						if ($weekDay > 0)
						{
							$date->add(new DateInterval('P' . $weekDay . 'D'));
						}
						if (($date >= $recurringStartDate) && ($date <= $recurringEndDate))
						{
							$eventDates[] = $date->format('Y-m-d H:i:s');
						}
					}
					if ($date > $recurringEndDate)
					{
						break;
					}

					$startWeek->add($dateInterval);
				}
			}
		}
		else
		{
			$startTime         = strtotime($startDate);
			$originalStartTime = $startTime;
			$endTime           = strtotime($endDate . ' 23:59:59');
			if ($numberOccurrences)
			{
				$count     = 0;
				$i         = 0;
				$weekDay   = date('w', $startTime);
				$startTime = $startTime - $weekDay * 24 * 3600;
				while ($count < $numberOccurrences)
				{
					$i++;
					$startWeekTime = $startTime + ($i - 1) * $weeklyFrequency * 7 * 24 * 3600;
					foreach ($weekDays as $weekDay)
					{
						$nextEventDate = $startWeekTime + $weekDay * 24 * 3600;
						if (($nextEventDate >= $originalStartTime) && ($count < $numberOccurrences))
						{
							$eventDates[] = strftime('%Y-%m-%d %H:%M:%S', $nextEventDate);
							$count++;
						}
					}
				}
			}
			else
			{
				$weekDay   = date('w', $startTime);
				$startTime = $startTime - $weekDay * 24 * 3600;
				while ($startTime < $endTime)
				{
					foreach ($weekDays as $weekDay)
					{
						$nextEventDate = $startTime + $weekDay * 24 * 3600;;
						if ($nextEventDate < $originalStartTime)
							continue;
						if ($nextEventDate <= $endTime)
						{
							$eventDates[] = strftime('%Y-%m-%d %H:%M:%S', $nextEventDate);
						}
						else
						{
							break;
						}
					}
					$startTime += $weeklyFrequency * 7 * 24 * 3600;
				}
			}
		}

		return $eventDates;
	}

	/**
	 * Get list of monthly recurring
	 *
	 * @param DateTime $startDate
	 * @param DateTime $endDate
	 * @param int      $monthlyFrequency
	 * @param int      $numberOccurrences
	 * @param string   $monthDays
	 *
	 * @return array
	 */
	public static function getMonthlyRecurringEventDates($startDate, $endDate, $monthlyFrequency, $numberOccurrences, $monthDays)
	{
		$eventDates = array();
		if (version_compare(PHP_VERSION, '5.3.0', 'ge'))
		{
			$timeZone           = new DateTimeZone(JFactory::getConfig()->get('offset'));
			$recurringStartDate = new Datetime($startDate, $timeZone);
			$date               = clone $recurringStartDate;
			$dateInterval       = new DateInterval('P' . $monthlyFrequency . 'M');
			$monthDays          = explode(',', $monthDays);
			if ($numberOccurrences)
			{
				$count = 0;
				while ($count < $numberOccurrences)
				{
					$currentMonth = $date->format('m');
					$currentYear  = $date->format('Y');
					foreach ($monthDays as $day)
					{
						$date->setDate($currentYear, $currentMonth, $day);
						if (($date >= $recurringStartDate) && ($count < $numberOccurrences))
						{
							$eventDates[] = $date->format('Y-m-d H:i:s');
							$count++;
						}
					}
					$date->add($dateInterval);
				}
			}
			else
			{
				$recurringEndDate = new DateTime($endDate . ' 23:59:59', $timeZone);
				while (true)
				{
					$currentMonth = $date->format('m');
					$currentYear  = $date->format('Y');
					foreach ($monthDays as $day)
					{
						$date->setDate($currentYear, $currentMonth, $day);
						if (($date >= $recurringStartDate) && ($date <= $recurringEndDate))
						{
							$eventDates[] = $date->format('Y-m-d H:i:s');
						}
					}
					if ($date > $recurringEndDate)
					{
						break;
					}
					$date->add(new DateInterval('P' . $monthlyFrequency . 'M'));
				}
			}
		}
		else
		{
			$startTime         = strtotime($startDate);
			$hour              = date('H', $startTime);
			$minute            = date('i', $startTime);
			$originalStartTime = $startTime;
			$endTime           = strtotime($endDate . ' 23:59:59');
			$monthDays         = explode(',', $monthDays);
			if ($numberOccurrences)
			{
				$count        = 0;
				$currentMonth = date('m', $startTime);
				$currentYear  = date('Y', $startTime);
				while ($count < $numberOccurrences)
				{
					foreach ($monthDays as $day)
					{
						$nextEventDate = mktime($hour, $minute, 0, $currentMonth, $day, $currentYear);
						if (($nextEventDate >= $originalStartTime) && ($count < $numberOccurrences))
						{
							$eventDates[] = strftime('%Y-%m-%d %H:%M:%S', $nextEventDate);
							$count++;
						}
					}
					$currentMonth += $monthlyFrequency;
					if ($currentMonth > 12)
					{
						$currentMonth -= 12;
						$currentYear++;
					}
				}
			}
			else
			{
				$currentMonth = date('m', $startTime);
				$currentYear  = date('Y', $startTime);
				while ($startTime < $endTime)
				{
					foreach ($monthDays as $day)
					{
						$nextEventDate = mktime($hour, $minute, 0, $currentMonth, $day, $currentYear);
						if (($nextEventDate >= $originalStartTime) && ($nextEventDate <= $endTime))
						{
							$eventDates[] = strftime('%Y-%m-%d %H:%M:%S', $nextEventDate);
						}
					}
					$currentMonth += $monthlyFrequency;
					if ($currentMonth > 12)
					{
						$currentMonth -= 12;
						$currentYear++;
					}
					$startTime = mktime(0, 0, 0, $currentMonth, 1, $currentYear);
				}
			}
		}


		return $eventDates;
	}


	/**
	 * Get list of event dates for recurring events happen on specific date in a month
	 *
	 * @param $startDate
	 * @param $endDate
	 * @param $monthlyFrequency
	 * @param $numberOccurrences
	 * @param $n
	 * @param $day
	 *
	 * @return array
	 */
	public static function getMonthlyRecurringAtDayInWeekEventDates($startDate, $endDate, $monthlyFrequency, $numberOccurrences, $n, $day)
	{
		$eventDates = array();
		$timeZone           = new DateTimeZone(JFactory::getConfig()->get('offset'));
		$recurringStartDate = new Datetime($startDate, $timeZone);
		$date               = clone $recurringStartDate;
		$dateInterval       = new DateInterval('P' . $monthlyFrequency . 'M');

		if ($numberOccurrences)
		{
			$count = 0;
			while ($count < $numberOccurrences)
			{
				$currentMonth = $date->format('M');
				$currentYear  = $date->format('Y');
				$timeString = "$n $day";
				$timeString .= " of $currentMonth $currentYear";
				$date->setTimestamp(strtotime($timeString));
				$date->setTime($recurringStartDate->format('H'), $recurringStartDate->format('i'), 0);
				if (($date >= $recurringStartDate) && ($count < $numberOccurrences))
				{
					$eventDates[] = $date->format('Y-m-d H:i:s');
					$count++;
				}

				$date->add($dateInterval);
			}
		}
		else
		{
			$recurringEndDate = new DateTime($endDate . ' 23:59:59', $timeZone);
			while (true)
			{
				$currentMonth = $date->format('M');
				$currentYear  = $date->format('Y');
				$timeString = "$n $day";
				$timeString .= " of $currentMonth $currentYear";
				$date->setTimestamp(strtotime($timeString));
				$date->setTime($recurringStartDate->format('H'), $recurringStartDate->format('i'), 0);
				if (($date >= $recurringStartDate) && ($date <= $recurringEndDate))
				{
					$eventDates[] = $date->format('Y-m-d H:i:s');
				}
				if ($date > $recurringEndDate)
				{
					break;
				}
				$date->add(new DateInterval('P' . $monthlyFrequency . 'M'));
			}
		}

		return $eventDates;
	}


	public static function getDeliciousButton($title, $link)
	{
		$img_url = JUri::root(true) . "/media/com_eventbooking/assets/images/socials/delicious.png";

		return '<a href="http://del.icio.us/post?url=' . rawurlencode($link) . '&amp;title=' . rawurlencode($title) . '" title="Submit ' . $title . ' in Delicious" target="blank" >
		<img src="' . $img_url . '" alt="Submit ' . $title . ' in Delicious" />
		</a>';
	}

	public static function getDiggButton($title, $link)
	{
		$img_url = JUri::root(true) . "/media/com_eventbooking/assets/images/socials/digg.png";

		return '<a href="http://digg.com/submit?url=' . rawurlencode($link) . '&amp;title=' . rawurlencode($title) . '" title="Submit ' . $title . ' in Digg" target="blank" >
        <img src="' . $img_url . '" alt="Submit ' . $title . ' in Digg" />
        </a>';
	}

	public static function getFacebookButton($title, $link)
	{
		$img_url = JUri::root(true) . "/media/com_eventbooking/assets/images/socials/facebook.png";

		return '<a href="http://www.facebook.com/sharer.php?u=' . rawurlencode($link) . '&amp;t=' . rawurlencode($title) . '" title="Submit ' . $title . ' in FaceBook" target="blank" >
        <img src="' . $img_url . '" alt="Submit ' . $title . ' in FaceBook" />
        </a>';
	}

	public static function getGoogleButton($title, $link)
	{
		$img_url = JUri::root(true) . "/media/com_eventbooking/assets/images/socials/google.png";

		return '<a href="http://www.google.com/bookmarks/mark?op=edit&bkmk=' . rawurlencode($link) . '" title="Submit ' . $title . ' in Google Bookmarks" target="blank" >
        <img src="' . $img_url . '" alt="Submit ' . $title . ' in Google Bookmarks" />
        </a>';
	}

	public static function getStumbleuponButton($title, $link)
	{
		$img_url = JUri::root(true) . "/media/com_eventbooking/assets/images/socials/stumbleupon.png";

		return '<a href="http://www.stumbleupon.com/submit?url=' . rawurlencode($link) . '&amp;title=' . rawurlencode($title) . '" title="Submit ' .
		$title . ' in Stumbleupon" target="blank" >
        <img src="' . $img_url . '" alt="Submit ' . $title . ' in Stumbleupon" />
        </a>';
	}

	public static function getTechnoratiButton($title, $link)
	{
		$img_url = JUri::root(true) . "/media/com_eventbooking/assets/images/socials/technorati.png";

		return '<a href="http://technorati.com/faves?add=' . rawurlencode($link) . '" title="Submit ' . $title . ' in Technorati" target="blank" >
        <img src="' . $img_url . '" alt="Submit ' . $title . ' in Technorati" />
        </a>';
	}

	public static function getTwitterButton($title, $link)
	{
		$img_url = JUri::root(true) . "/media/com_eventbooking/assets/images/socials/twitter.png";

		return '<a href="http://twitter.com/?status=' . rawurlencode($title . " " . $link) . '" title="Submit ' . $title . ' in Twitter" target="blank" >
        <img src="' . $img_url . '" alt="Submit ' . $title . ' in Twitter" />
        </a>';
	}

	public static function getLinkedInButton($title, $link)
	{
		$img_url = JUri::root(true) . "/media/com_eventbooking/assets/images/socials/linkedin.png";

		return '<a href="http://www.linkedin.com/shareArticle?mini=true&amp;url=' . $link . '&amp;title=' . $title . '" title="Submit ' . $title . ' in LinkedIn" target="_blank" ><img src="' . $img_url . '" alt="Submit ' . $title . ' in LinkedIn" /></a>';
	}

	/**
	 *
	 * @param string $vName
	 */
	public static function addSubMenus($vName = 'dashboard')
	{
		JSubMenuHelper::addEntry(JText::_('Dashboard'), 'index.php?option=com_eventbooking&view=dashboard', $vName == 'dashboard');
		JSubMenuHelper::addEntry(JText::_('Categories'), 'index.php?option=com_eventbooking&view=categories', $vName == 'categories');
		JSubMenuHelper::addEntry(JText::_('Events'), 'index.php?option=com_eventbooking&view=events', $vName == 'events');
		JSubMenuHelper::addEntry(JText::_('Registrants'), 'index.php?option=com_eventbooking&view=registrants', $vName == 'registrants');
		JSubMenuHelper::addEntry(JText::_('Custom Fields'), 'index.php?option=com_eventbooking&view=fields', $vName == 'fields');
		JSubMenuHelper::addEntry(JText::_('Locations'), 'index.php?option=com_eventbooking&view=locations', $vName == 'locations');
		JSubMenuHelper::addEntry(JText::_('Coupons'), 'index.php?option=com_eventbooking&view=coupons', $vName == 'coupons');
		JSubMenuHelper::addEntry(JText::_('Payment Plugins'), 'index.php?option=com_eventbooking&view=plugins', $vName == 'plugins');
		JSubMenuHelper::addEntry(JText::_('Emails & Messages'), 'index.php?option=com_eventbooking&view=message', $vName == 'language');
		JSubMenuHelper::addEntry(JText::_('Translation'), 'index.php?option=com_eventbooking&view=language', $vName == 'language');
		JSubMenuHelper::addEntry(JText::_('Configuration'), 'index.php?option=com_eventbooking&view=configuration', $vName == 'configuration');
	}
}

?>