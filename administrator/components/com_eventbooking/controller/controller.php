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
defined('_JEXEC') or die();

class EventbookingController extends RADControllerAdmin
{

	public function display($cachable = false, array $urlparams = array())
	{
		JFactory::getDocument()->addStyleSheet(JURI::base(true) . '/components/com_eventbooking/assets/css/style.css');

		parent::display($cachable, $urlparams);

		if (version_compare(JVERSION, '3.0', 'le'))
		{
			EventbookingHelper::loadJQuery();
			EventbookingHelper::loadBootstrap();
		}

		if ($this->input->getCmd('format', 'html') != 'raw')
		{
			EventbookingHelper::displayCopyRight();
		}
	}

	/**
	 * Resend confirmation email to registrants in case they didn't receive it
	 */
	public function resend_email()
	{
		$cid   = $this->input->get('cid', array(), 'array');
		$id    = (int) $cid[0];
		$model = $this->getModel('Registrant');
		$ret   = $model->resendEmail($id);
		if ($ret)
		{
			$this->setMessage(JText::_('EB_EMAIL_SUCCESSFULLY_RESENT'));
		}
		else
		{
			$this->setMessage(JText::_('EB_COULD_NOT_RESEND_EMAIL_TO_GROUP_MEMBER'), 'notice');
		}

		$this->setRedirect('index.php?option=com_eventbooking&view=registrants');
	}

	/**
	 * Export registrants into a CSV file
	 */
	public function csv_export()
	{
		set_time_limit(0);
		require_once JPATH_ROOT . '/components/com_eventbooking/helper/data.php';
		$db      = JFactory::getDBO();
		$config  = EventbookingHelper::getConfig();
		$eventId = JRequest::getInt('filter_event_id');
		$where   = array();
		$where[] = '(a.published = 1 OR (a.payment_method LIKE "os_offline%" AND a.published NOT IN (2,3)))';
		if ($eventId)
		{
			$where[] = ' a.event_id=' . $eventId;
		}
		if (isset($config->include_group_billing_in_csv_export) && !$config->include_group_billing_in_csv_export)
		{
			$where[] = ' a.is_group_billing = 0 ';
		}
		if (!$config->include_group_members_in_csv_export)
		{
			$where[] = ' a.group_id = 0 ';
		}
		if ($config->show_coupon_code_in_registrant_list)
		{
			$sql = 'SELECT a.*, b.event_date, b.title AS event_title, c.code AS coupon_code FROM #__eb_registrants AS a INNER JOIN #__eb_events AS b ON a.event_id = b.id LEFT JOIN #__eb_coupons AS c ON a.coupon_id=c.id WHERE ' .
				implode(' AND ', $where) . ' ORDER BY a.id ';
		}
		else
		{
			$sql = 'SELECT a.*, b.event_date, b.title AS event_title FROM #__eb_registrants AS a INNER JOIN #__eb_events AS b ON a.event_id = b.id WHERE ' .
				implode(' AND ', $where) . ' ORDER BY a.id ';
		}
		$db->setQuery($sql);
		$rows = $db->loadObjectList();
		if ($eventId)
		{
			if ($config->custom_field_by_category)
			{
				//Select main category
				$sql = 'SELECT category_id FROM #__eb_event_categories WHERE event_id=' . $eventId . ' AND main_category = 1';
				$db->setQuery($sql);
				$categoryId = (int) $db->loadResult();

				$sql = 'SELECT id, name, title, is_core FROM #__eb_fields WHERE published=1 AND (category_id = -1 OR id IN (SELECT field_id FROM #__eb_field_categories WHERE category_id=' . $categoryId . ')) ORDER BY ordering';
				$db->setQuery($sql);
				$rowFields = $db->loadObjectList();
			}
			else
			{
				$sql = 'SELECT id, name, title, is_core FROM #__eb_fields WHERE published=1 AND (event_id = -1 OR id IN (SELECT field_id FROM #__eb_field_events WHERE event_id=' .
					$eventId . ')) ORDER BY ordering';
				$db->setQuery($sql);
				$rowFields = $db->loadObjectList();
			}
		}
		else
		{
			//Get all published custom fields
			$sql = 'SELECT id, name, title, is_core FROM #__eb_fields WHERE published=1 ORDER BY ordering';
			$db->setQuery($sql);
			$rowFields = $db->loadObjectList();
		}
		//Get the custom fields value and store them into an array
		$sql = 'SELECT id FROM #__eb_registrants AS a WHERE ' . implode(' AND ', $where);
		$db->setQuery($sql);
		$registrantIds = array(0);
		$registrantIds = array_merge($registrantIds, $db->loadColumn());
		$sql           = 'SELECT registrant_id, field_id, field_value FROM #__eb_field_values WHERE registrant_id IN (' . implode(',', $registrantIds) . ')';
		$db->setQuery($sql);
		$rowFieldValues = $db->loadObjectList();
		$fieldValues    = array();
		for ($i = 0, $n = count($rowFieldValues); $i < $n; $i++)
		{
			$rowFieldValue                                                        = $rowFieldValues[$i];
			$fieldValues[$rowFieldValue->registrant_id][$rowFieldValue->field_id] = $rowFieldValue->field_value;
		}
		//Get name of groups
		$groupNames = array();
		$sql        = 'SELECT id, first_name, last_name FROM #__eb_registrants AS a WHERE is_group_billing = 1' .
			(COUNT($where) ? ' AND ' . implode(' AND ', $where) : '');
		$db->setQuery($sql);
		$rowGroups = $db->loadObjectList();
		if (count($rowGroups))
		{
			foreach ($rowGroups as $rowGroup)
			{
				$groupNames[$rowGroup->id] = $rowGroup->first_name . ' ' . $rowGroup->last_name;
			}
		}
		if (count($rows))
		{
			EventbookingHelperData::csvExport($rows, $config, $rowFields, $fieldValues, $groupNames);
		}
		else
		{
			$this->setRedirect('index.php?option=com_eventbooking&view=registrants', JText::_('There are no registrants to export'));
		}
	}

	function download_invoice()
	{
		$id = JRequest::getInt('id');
		EventbookingHelper::downloadInvoice($id);
	}

	/**
	 * This method is implemented to help calling by typing the url on web browser to update database schema to latest version
	 */
	function upgrade()
	{
		$this->update_db_schema();
	}

	/**
	 * Update database schema when users update from old version to 1.6.4.
	 * We need to implement this function outside the installation script to avoid timeout during upgrade
	 */
	function update_db_schema()
	{
		jimport('joomla.filesystem.folder');
		$db = JFactory::getDbo();
		//Setup menus
		$menuSql = JPATH_ADMINISTRATOR . '/components/com_eventbooking/sql/menus.eventbooking.sql';
		$sql     = JFile::read($menuSql);
		$queries = $db->splitSql($sql);
		if (count($queries))
		{
			foreach ($queries as $query)
			{
				$query = trim($query);
				if ($query != '' && $query{0} != '#')
				{
					$db->setQuery($query);
					$db->execute();
				}
			}
		}
		###Setup default configuration data                
		$sql = 'SELECT COUNT(*) FROM #__eb_configs';
		$db->setQuery($sql);
		$total = $db->loadResult();
		if (!$total)
		{
			$configSql = JPATH_ADMINISTRATOR . '/components/com_eventbooking/sql/config.eventbooking.sql';
			$sql       = JFile::read($configSql);
			$queries   = $db->splitSql($sql);
			if (count($queries))
			{
				foreach ($queries as $query)
				{
					$query = trim($query);
					if ($query != '' && $query{0} != '#')
					{
						$db->setQuery($query);
						$db->execute();
					}
				}
			}
			$sql = 'UPDATE #__eb_configs SET config_value="m-d-Y" WHERE config_key="date_format"';
			$db->setQuery($sql);
			$db->execute();
			$sql = 'UPDATE #__eb_configs SET config_value="m-d-Y g:i a" WHERE config_key="event_date_format"';
			$db->setQuery($sql);
			$db->execute();
			$sql = 'UPDATE #__eb_configs SET config_value="g:i a" WHERE config_key="event_time_format"';
			$db->setQuery($sql);
			$db->execute();
		}
		$config = EventbookingHelper::getConfig();
		//Set up default payment plugins table
		$sql = 'SELECT COUNT(*) FROM #__eb_payment_plugins';
		$db->setQuery($sql);
		$total = $db->loadResult();
		if (!$total)
		{
			$configSql = JPATH_ADMINISTRATOR . '/components/com_eventbooking/sql/plugins.eventbooking.sql';
			$sql       = JFile::read($configSql);
			$queries   = $db->splitSql($sql);
			if (count($queries))
			{
				foreach ($queries as $query)
				{
					$query = trim($query);
					if ($query != '' && $query{0} != '#')
					{
						$db->setQuery($query);
						$db->execute();
					}
				}
			}
		}

		// Add access field for payment plugin
		$fields = array_keys($db->getTableColumns('#__eb_payment_plugins'));
		if (!in_array('access', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_payment_plugins` ADD  `access` INT NOT NULL DEFAULT  '1';";
			$db->setQuery($sql);
			$db->execute();

			$sql = 'UPDATE #__eb_payment_plugins SET `access` = 1';
			$db->setQuery($sql);
			$db->execute();
		}

		// Update author email to tuanpn@joomdoantion.com as contact@joomdonation.com is not available anymore
		$sql = 'UPDATE #__eb_payment_plugins SET author_email="tuanpn@joomdonation.com" WHERE author_email="contact@joomdonation.com"';
		$db->setQuery($sql);
		$db->execute();

		// Countries and states management
		$fields = array_keys($db->getTableColumns('#__eb_countries'));
		if (!in_array('id', $fields))
		{
			//Change the name of the name of column from country_id to ID
			$sql = 'ALTER TABLE `#__eb_countries` CHANGE `country_id` `id` INT(11) NOT NULL AUTO_INCREMENT;';
			$db->setQuery($sql);
			$db->execute();

			//Add country ID column back for BC
			$sql = "ALTER TABLE  `#__eb_countries` ADD  `country_id` INT(11) NOT NULL DEFAULT '0';";
			$db->setQuery($sql);
			$db->execute();

			//Set country_id value the same with id
			$sql = 'UPDATE #__eb_countries SET country_id=id';
			$db->setQuery($sql);
			$db->execute();

		}

		$fields = array_keys($db->getTableColumns('#__eb_states'));

		if (!in_array('id', $fields))
		{
			//Change the name of the name of column from country_id to ID
			$sql = 'ALTER TABLE `#__eb_states` CHANGE `state_id` `id` INT(11) NOT NULL AUTO_INCREMENT;';
			$db->setQuery($sql);
			$db->execute();

			//Add state ID column back for BC
			$sql = "ALTER TABLE  `#__eb_states` ADD  `state_id` INT(11) NOT NULL DEFAULT '0';";
			$db->setQuery($sql);
			$db->execute();

			//Set country_id value the same with id
			$sql = 'UPDATE #__eb_states SET state_id=id';
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('published', $fields))
		{
			$db->setQuery("ALTER TABLE `#__eb_states` ADD `published` TINYINT( 4 ) NOT NULL DEFAULT '1'");
			$db->execute();
			$db->setQuery("UPDATE `#__eb_states` SET `published` = 1");
			$db->execute();
		}

		$sql = "SELECT COUNT(*) FROM #__eb_currencies WHERE currency_code='RUB'";
		$db->setQuery($sql);
		$total = $db->loadResult();
		if (!$total)
		{
			$sql = "INSERT INTO #__eb_currencies(currency_code, currency_name) VALUES('RUB', 'Russian Rubles')";
			$db->setQuery($sql);
			$db->execute();
		}

		//Change field type of some fields
		$sql = 'ALTER TABLE  `#__eb_events` CHANGE  `short_description`  `short_description` MEDIUMTEXT  NULL DEFAULT NULL';
		$db->setQuery($sql);
		$db->execute();

		$sql = "ALTER TABLE  `#__eb_events` CHANGE  `discount`  `discount` DECIMAL( 10, 2 ) NULL DEFAULT  '0'";
		$db->setQuery($sql);
		$db->execute();

		$sql = "ALTER TABLE  `#__eb_locations` CHANGE  `lat`  `lat` DECIMAL( 10, 6 ) NULL DEFAULT '0'";
		$db->setQuery($sql);
		$db->execute();

		$sql = "ALTER TABLE  `#__eb_locations` CHANGE  `long`  `long` DECIMAL( 10, 6 ) NULL DEFAULT '0'";
		$db->setQuery($sql);
		$db->execute();

		$sql = "ALTER TABLE  `#__eb_coupons` CHANGE  `valid_from`  `valid_from` DATETIME NULL";
		$db->setQuery($sql);
		$db->execute();

		$sql = "ALTER TABLE  `#__eb_coupons` CHANGE  `valid_to`  `valid_to` DATETIME NULL";
		$db->setQuery($sql);
		$db->execute();

		$sql = "ALTER TABLE  `#__eb_coupons` CHANGE `used` `used` INT( 11 ) NULL DEFAULT  '0'";
		$db->setQuery($sql);
		$db->execute();

		$sql = 'UPDATE #__eb_coupons SET `used` = 0 WHERE `used` IS NULL';
		$db->setQuery($sql);
		$db->execute();
		$sql = 'ALTER TABLE  `#__eb_fields` CHANGE  `description`  `description` MEDIUMTEXT  NULL DEFAULT NULL';
		$db->setQuery($sql);
		$db->execute();
		##Locations table
		$fields = array_keys($db->getTableColumns('#__eb_locations'));
		if (!in_array('user_id', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_locations` ADD  `user_id` INT NOT NULL DEFAULT  '0' ;";
			$db->setQuery($sql);
			$db->execute();
		}
		if (!in_array('language', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_locations` ADD  `language` VARCHAR( 50 ) NULL DEFAULT  '*';";
			$db->setQuery($sql);
			$db->execute();

			$sql = 'UPDATE #__eb_locations SET `language`="*" ';
			$db->setQuery($sql);
			$db->execute();
		}

		$fields = array_keys($db->getTableColumns('#__eb_configs'));
		if (!in_array('language', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_configs` ADD  `language` VARCHAR( 50 ) NULL DEFAULT  '*';";
			$db->setQuery($sql);
			$db->execute();
		}
		//Joomla default language
		$defaultLanguage = JComponentHelper::getParams('com_languages')->get('site', 'en-GB');
		$sql             = 'SELECT COUNT(*) FROM #__eb_configs WHERE language="' . $defaultLanguage . '"';
		$db->setQuery($sql);
		$total = $db->loadResult();
		if (!$total)
		{
			$sql = 'UPDATE #__eb_configs SET language="' . $defaultLanguage . '" WHERE language="*"';
			$db->setQuery($sql);
			$db->execute();
		}
		else
		{
			//Delete the old one
			$sql = 'DELETE FROM #__eb_configs WHERE language="*"';
			$db->setQuery($sql);
			$db->execute();
		}
		###Custom fields table
		$fields = array_keys($db->getTableColumns('#__eb_fields'));
		if (!in_array('language', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_fields` ADD  `language` VARCHAR( 50 ) NULL DEFAULT  '*';";
			$db->setQuery($sql);
			$db->execute();

			$sql = 'UPDATE #__eb_fields SET `language`="*" ';
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('datatype_validation', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_fields` ADD  `datatype_validation` TINYINT NOT NULL DEFAULT  '0' ;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('extra_attributes', $fields))
		{
			if (!in_array('extra', $fields))
			{
				$sql = "ALTER TABLE  `#__eb_fields` ADD  `extra_attributes` VARCHAR( 255 ) NULL;";
				$db->setQuery($sql);
				$db->execute();
			}
			else
			{
				$sql = "ALTER TABLE  `#__eb_fields` CHANGE `extra` `extra_attributes` VARCHAR( 255 ) NULL;";
				$db->setQuery($sql);
				$db->execute();
			}
		}

		if (!in_array('access', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_fields` ADD  `access` INT NOT NULL DEFAULT  '1';";
			$db->setQuery($sql);
			$db->execute();

			$sql = 'UPDATE #__eb_fields SET `access` = 1';
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('show_in_list_view', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_fields` ADD  `show_in_list_view` TINYINT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('depend_on_field_id', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_fields` ADD  `depend_on_field_id` INT NOT NULL DEFAULT '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('depend_on_options', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_fields` ADD  `depend_on_options` TEXT NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('max_length', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_fields` ADD  `max_length` INT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('place_holder', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_fields` ADD   `place_holder` VARCHAR( 255 ) NULL;";
			$db->setQuery($sql);
			$db->execute();
		}
		if (!in_array('multiple', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_fields` ADD  `multiple` TINYINT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('validation_rules', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_fields` ADD  `validation_rules` VARCHAR( 255 ) NULL;";
			$db->setQuery($sql);
			$db->execute();
		}
		if (!in_array('validation_error_message', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_fields` ADD  `validation_error_message` VARCHAR( 255 ) NULL;";
			$db->setQuery($sql);
			$db->execute();
		}


		// Quantity field
		if (!in_array('quantity_field', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_fields` ADD  `quantity_field` TINYINT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('quantity_values', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_fields` ADD  `quantity_values` TEXT NULL;";
			$db->setQuery($sql);
			$db->execute();
		}
		
		if (!in_array('only_show_for_first_member', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_fields` ADD  `only_show_for_first_member` TINYINT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('only_require_for_first_member', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_fields` ADD  `only_require_for_first_member` TINYINT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();
		}
		
		//Events table
		$fields = array_keys($db->getTableColumns('#__eb_events'));

		// Discounts
		if (!in_array('discount_groups', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `discount_groups` VARCHAR( 255 ) NULL;";
			$db->setQuery($sql);
			$db->execute();
			$discountGroups = EventbookingHelper::getConfigValue('member_discount_groups');
			if ($discountGroups)
			{
				$sql = 'UPDATE #__eb_events SET discount_groups=' . $db->quote($discountGroups);
				$db->setQuery($sql);
				$db->execute();
			}
		}

		if (!in_array('discount_amounts', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `discount_amounts` VARCHAR( 255 ) NULL;";
			$db->setQuery($sql);
			$db->execute();

			$sql = 'UPDATE `#__eb_events` SET `discount_amounts` = `discount` WHERE `discount` > 0';
			$db->setQuery($sql);
			$db->execute();
		}


		if (!in_array('event_end_date', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `event_end_date` DATETIME NULL AFTER  `event_date` ;";
			$db->setQuery($sql);
			$db->execute();
		}


		if (!in_array('registration_start_date', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `registration_start_date` DATETIME NULL DEFAULT '0000-00-00 00:00:00';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('access', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `access` TINYINT NOT NULL DEFAULT  '0' ;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('registration_access', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `registration_access` TINYINT NOT NULL DEFAULT  '0' ;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('max_group_number', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `max_group_number` INT NOT NULL DEFAULT  '0' ;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('min_group_number', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `min_group_number` TINYINT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('paypal_email', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `paypal_email` VARCHAR( 255 ) NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('registration_handle_url', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `registration_handle_url` VARCHAR( 255 ) NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('api_login', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `api_login` VARCHAR( 255 ) NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('transaction_key', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `transaction_key` VARCHAR( 255 ) NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('fixed_group_price', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `fixed_group_price` DECIMAL( 10, 2 ) NULL DEFAULT '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('paypal_email', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `paypal_email` VARCHAR( 255 ) NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('attachment', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `attachment` VARCHAR( 255 ) NULL;";
			$db->setQuery($sql);
			$db->execute();
			//Need to create com_eventbooking folder under media folder
			if (!JFolder::exists(JPATH_ROOT . '/media/com_eventbooking'))
			{
				JFolder::create(JPATH_ROOT . '/media/com_eventbooking');
			}
		}

		if (!in_array('notification_emails', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `notification_emails` VARCHAR( 255 ) NULL;";
			$db->setQuery($sql);
			$db->execute();
		}


		if (!in_array('user_email_body', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `user_email_body` TEXT NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('user_email_body_offline', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `user_email_body_offline` TEXT NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('thanks_message', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `thanks_message` TEXT NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('thanks_message_offline', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `thanks_message_offline` TEXT NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		//Adding some new fields for supporting recurring events
		if (!in_array('enable_cancel_registration', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `enable_cancel_registration` TINYINT NOT NULL DEFAULT  '0' ;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('cancel_before_date', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `cancel_before_date` DATETIME NULL ;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('enable_auto_reminder', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `enable_auto_reminder` TINYINT NOT NULL DEFAULT  '0' ;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('remind_before_x_days', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `remind_before_x_days` TINYINT NOT NULL DEFAULT  '0' ;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('early_bird_discount_type', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `early_bird_discount_type` TINYINT NOT NULL DEFAULT  '0' ;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('early_bird_discount_date', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `early_bird_discount_date` DATETIME NULL ;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('early_bird_discount_amount', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `early_bird_discount_amount` DECIMAL( 10, 2 ) NULL DEFAULT '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		// Late Fee date
		if (!in_array('late_fee_type', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `late_fee_type` TINYINT NOT NULL DEFAULT  '0' ;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('late_fee_date', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `late_fee_date` DATETIME NULL DEFAULT NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('late_fee_amount', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `late_fee_amount` DECIMAL( 10, 2 ) NULL DEFAULT '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('parent_id', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `parent_id` INT NOT NULL DEFAULT  '0' ;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('created_by', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `created_by` INT NOT NULL DEFAULT  '0' ;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('event_type', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `event_type` TINYINT NOT NULL DEFAULT  '0' ;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('recurring_type', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `recurring_type` TINYINT NOT NULL DEFAULT  '0' ;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('recurring_frequency', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `recurring_frequency` INT NOT NULL DEFAULT  '0' ;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('article_id', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `article_id` INT NOT NULL DEFAULT  '0' ;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('weekdays', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `weekdays` VARCHAR( 50 ) NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('monthdays', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `monthdays` VARCHAR( 50 ) NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('recurring_end_date', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `recurring_end_date` DATETIME NULL ;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('recurring_occurrencies', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `recurring_occurrencies` INT NOT NULL DEFAULT  '0' ;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('recurring_occurrencies', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `recurring_occurrencies` INT NOT NULL DEFAULT  '0' ;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('custom_fields', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `custom_fields` TEXT NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		#Support deposit payment
		if (!in_array('deposit_type', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `deposit_type` TINYINT NOT NULL DEFAULT  '0' ;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('deposit_amount', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `deposit_amount` DECIMAL( 10, 2 ) NULL DEFAULT '0' ;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('registration_type', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `registration_type` TINYINT NOT NULL DEFAULT  '0' AFTER  `enable_group_registration` ;";
			$db->setQuery($sql);
			$db->execute();
			$updateDb = true;
		}
		else
		{
			$updateDb = false;
		}
		if ($updateDb)
		{
			$sql = 'UPDATE #__eb_events SET registration_type = 1 WHERE enable_group_registration = 0';
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('custom_field_ids', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `custom_field_ids` VARCHAR( 255 ) NULL;";
			$db->setQuery($sql);
			$db->execute();
		}
		if (!in_array('event_password', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `event_password` VARCHAR( 255 ) NULL;";
			$db->setQuery($sql);
			$db->execute();
		}
		#Support Payment method based on event
		if (!in_array('payment_methods', $fields))
		{
			$sql = "ALTER TABLE `#__eb_events` ADD `payment_methods` VARCHAR( 255 ) NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('currency_code', $fields))
		{
			$sql = "ALTER TABLE `#__eb_events` ADD `currency_code` VARCHAR( 10 ) NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('currency_symbol', $fields))
		{
			$sql = "ALTER TABLE `#__eb_events` ADD `currency_symbol` VARCHAR( 20 ) NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		//Thumb image for event
		if (!in_array('thumb', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `thumb` VARCHAR(60) NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('registration_approved_email_body', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD `registration_approved_email_body` TEXT NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('fixed_daylight_saving_time', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `fixed_daylight_saving_time`  TINYINT NOT NULL DEFAULT  '0' ;";
			$db->setQuery($sql);
			$db->execute();
		}
		/**
		 * Add support for multilingual
		 */
		if (!in_array('language', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `language` VARCHAR( 50 ) NULL DEFAULT  '*';";
			$db->setQuery($sql);
			$db->execute();

			$sql = 'UPDATE #__eb_events SET `language`="*" ';
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('meta_keywords', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `meta_keywords` VARCHAR( 255 ) NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('meta_description', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `meta_description` VARCHAR( 255 ) NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('enable_coupon', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `enable_coupon` TINYINT NOT NULL DEFAULT  '0' ;";
			$db->setQuery($sql);
			$db->execute();
			require_once JPATH_ROOT . '/components/com_eventbooking/helper/helper.php';
			$enableCoupon = EventbookingHelper::getConfigValue('enable_coupon');
			if ($enableCoupon == 1)
			{
				$sql = 'UPDATE #__eb_events SET enable_coupon=3';
				$db->setQuery($sql);
				$db->execute();
			}
		}
		if (!in_array('alias', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `alias` VARCHAR( 255 ) NULL;";
			$db->setQuery($sql);
			$db->execute();
			$sql = 'SELECT id, parent_id, title, event_date FROM #__eb_events';
			$db->setQuery($sql);
			$rowEvents = $db->loadObjectList();
			if (count($rowEvents))
			{
				foreach ($rowEvents as $rowEvent)
				{
					if ($rowEvent->parent_id > 0)
					{
						$alias = JApplication::stringURLSafe(
							$rowEvent->title . '-' . JHtml::_('date', $rowEvent->event_date, $config->date_format, null));
					}
					else
					{
						$alias = JApplication::stringURLSafe($rowEvent->title);
					}
					//Check to see if this alias existing or not. If the alias exist, we will append id of the event at the beginning
					$sql = 'SELECT COUNT(*) FROM #__eb_events WHERE alias=' . $db->quote($alias);
					$db->setQuery($sql);
					$total = $db->loadResult();
					if ($total)
					{
						$alias = $rowEvent->id . '-' . $alias;
					}
					$sql = 'UPDATE #__eb_events SET `alias`=' . $db->quote($alias) . ' WHERE id=' . $rowEvent->id;
					$db->setQuery($sql);
					$db->execute();
				}
			}
		}

		if (!in_array('tax_rate', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `tax_rate` DECIMAL( 10, 2 ) NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();
			//Set tax rate for the plan from configuration
			$taxRate = (float) $config->tax_rate;
			if ($taxRate > 0)
			{
				$sql = 'UPDATE #__eb_events SET tax_rate=' . $taxRate;
				$db->setQuery($sql);
				$db->execute();
			}
		}

		//The Categories table
		$fields = array_keys($db->getTableColumns('#__eb_categories'));
		if (!in_array('access', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_categories` ADD  `access` TINYINT NOT NULL DEFAULT  '0' ;";
			$db->setQuery($sql);
			$db->execute();
		}
		if (!in_array('color_code', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_categories` ADD  `color_code` VARCHAR( 20 ) NULL;";
			$db->setQuery($sql);
			$db->execute();
		}
		if (!in_array('language', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_categories` ADD  `language` VARCHAR( 50 ) NULL DEFAULT  '*';";
			$db->setQuery($sql);
			$db->execute();

			$sql = 'UPDATE #__eb_categories SET `language`="*" ';
			$db->setQuery($sql);
			$db->execute();
		}
		if (!in_array('meta_keywords', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_categories` ADD  `meta_keywords` VARCHAR( 255 ) NULL;";
			$db->setQuery($sql);
			$db->execute();
		}
		if (!in_array('meta_description', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_categories` ADD  `meta_description` VARCHAR( 255 ) NULL;";
			$db->setQuery($sql);
			$db->execute();
		}
		if (!in_array('alias', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_categories` ADD  `alias` VARCHAR( 255 ) NULL;";
			$db->setQuery($sql);
			$db->execute();
			$sql = 'SELECT id, name FROM #__eb_categories';
			$db->setQuery($sql);
			$rowCategories = $db->loadObjectList();
			if (count($rowCategories))
			{
				foreach ($rowCategories as $rowCategory)
				{
					$alias = JApplication::stringURLSafe($rowCategory->name);
					$sql   = 'UPDATE #__eb_categories SET `alias`=' . $db->quote($alias) . ' WHERE id=' . $rowCategory->id;
					$db->setQuery($sql);
					$db->execute();
				}
			}
		}
		//Registrants table
		$fields = array_keys($db->getTableColumns('#__eb_registrants'));
		if (!in_array('total_amount', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_registrants` ADD  `total_amount` DECIMAL( 10, 6 ) NULL DEFAULT '0';";
			$db->setQuery($sql);
			$db->execute();

			$sql = "ALTER TABLE  `#__eb_registrants` ADD  `discount_amount` DECIMAL( 10, 6 ) NULL DEFAULT '0';";
			$db->setQuery($sql);
			$db->execute();

			$sql = 'UPDATE #__eb_registrants  SET total_amount=`amount`';
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('payment_processing_fee', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_registrants` ADD  `payment_processing_fee` DECIMAL( 10, 6 ) NULL DEFAULT '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('late_fee', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_registrants` ADD  `late_fee` DECIMAL( 10, 6 ) NULL DEFAULT '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('cart_id', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_registrants` ADD  `cart_id`  INT NOT NULL DEFAULT  '0' ;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('notified', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_registrants` ADD  `notified`  TINYINT NOT NULL DEFAULT  '0' ;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('deposit_amount', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_registrants` ADD `deposit_amount` DECIMAL( 10, 2 ) NULL DEFAULT '0' ;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('payment_status', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_registrants` ADD  `payment_status`  TINYINT NOT NULL DEFAULT  '1' ;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('coupon_id', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_registrants` ADD  `coupon_id`  INT NOT NULL DEFAULT  '0' ;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('check_coupon', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_registrants` ADD  `check_coupon`  TINYINT NOT NULL DEFAULT  '0' ;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('tax_amount', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_registrants` ADD  `tax_amount` DECIMAL( 10, 6 ) NULL DEFAULT '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		$sql = "ALTER TABLE `#__eb_registrants` CHANGE `tax_amount` `tax_amount` DECIMAL(10,2) NULL DEFAULT '0.00';";
		$db->setQuery($sql);
		$db->execute();

		if (!in_array('registration_code', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_registrants` ADD  `registration_code` VARCHAR( 15 ) NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('params', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_registrants` ADD  `params` TEXT NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('is_reminder_sent', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_registrants` ADD  `is_reminder_sent` TINYINT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('is_group_billing', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_registrants` ADD  `is_group_billing` TINYINT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();

			//Update all other records
			$sql = 'SELECT DISTINCT group_id FROM #__eb_registrants WHERE group_id > 0';
			$db->setQuery($sql);
			$groupIds = $db->loadColumn();
			if (count($groupIds))
			{
				$sql = 'UPDATE #__eb_registrants SET is_group_billing=1 WHERE id IN (' . implode(',', $groupIds) . ') OR number_registrants > 1';
				$db->setQuery($sql);
				$db->execute();
				//Need to update the published field
				$sql = 'SELECT id, payment_method, transaction_id, published FROM #__eb_registrants WHERE id IN (' .
					implode(',', $groupIds) . ') OR number_registrants > 1';
				$db->setQuery($sql);
				$rowGroups = $db->loadObjectList();
				foreach ($rowGroups as $rowGroup)
				{
					$id            = $rowGroup->id;
					$paymentMethod = $rowGroup->payment_method;
					$transactionId = $rowGroup->transaction_id;
					$published     = $rowGroup->published;
					$sql           = "UPDATE  #__eb_registrants SET payment_method='$paymentMethod', transaction_id='$transactionId', published='$published', number_registrants=1 WHERE group_id=$id";
					$db->setQuery($sql);
					$db->execute();
				}
			}
		}

		$sql = "ALTER TABLE  `#__eb_registrants` CHANGE  `group_id`  `group_id` INT( 11 ) NULL DEFAULT  '0';";
		$db->setQuery($sql);
		$db->execute();

		$sql = 'UPDATE #__eb_registrants SET group_id = 0 WHERE group_id IS NULL';
		$db->setQuery($sql);
		$db->execute();

		if (!in_array('language', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_registrants` ADD  `language` VARCHAR( 50 ) NULL DEFAULT  '*';";
			$db->setQuery($sql);
			$db->execute();

			$sql = 'UPDATE #__eb_registrants SET `language`="*" ';
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('invoice_number', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_registrants` ADD  `invoice_number` INT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();

			//Update membership Id field
			$sql = 'SELECT id FROM #__eb_registrants WHERE group_id=0 AND (published=1 OR payment_method LIKE "%os_offline%") ORDER BY id';
			$db->setQuery($sql);
			$rows = $db->loadObjectList();
			if (count($rows))
			{
				$start = 1;
				foreach ($rows as $row)
				{
					$sql = 'UPDATE #__eb_registrants SET invoice_number=' . $start . ' WHERE id=' . $row->id;
					$db->setQuery($sql);
					$db->execute();
					$start++;
				}
			}
			//Need to insert default data into the system
			$invoiceFormat = '<table border="0" width="100%" cellspacing="0" cellpadding="2">
			<tbody>
			<tr>
			<td align="left" width="100%">
			<table border="0" width="100%" cellspacing="0" cellpadding="2">
			<tbody>
			<tr>
			<td width="100%">
			<table style="width: 100%;" border="0" cellspacing="0" cellpadding="2">
			<tbody>
			<tr>
			<td align="left" valign="top" width="50%">
			<table style="width: 100%;" border="0" cellspacing="0" cellpadding="2">
			<tbody>
			<tr>
			<td align="left" width="50%">Company Name:</td>
			<td align="left">Ossolution Team</td>
			</tr>
			<tr>
			<td align="left" width="50%">URL:</td>
			<td align="left">http://www.joomdonation.com</td>
			</tr>
			<tr>
			<td align="left" width="50%">Phone:</td>
			<td align="left">84-972409994</td>
			</tr>
			<tr>
			<td align="left" width="50%">E-mail:</td>
			<td align="left">contact@joomdonation.com</td>
			</tr>
			<tr>
			<td align="left" width="50%">Address:</td>
			<td align="left">Lang Ha - Ba Dinh - Ha Noi</td>
			</tr>
			</tbody>
			</table>
			</td>
			<td align="right" valign="middle" width="50%"><img style="border: 0;" src="media/com_eventbooking/invoice_logo.png" alt="" /></td>
			</tr>
			<tr>
			<td colspan="2" align="left" width="100%">
			<table style="width: 100%;" border="0" cellspacing="0" cellpadding="2">
			<tbody>
			<tr>
			<td align="left" valign="top" width="50%">
			<table style="width: 100%;" border="0" cellspacing="0" cellpadding="2">
			<tbody>
			<tr>
			<td style="background-color: #d6d6d6;" colspan="2" align="left">
			<h4 style="margin: 0px;">Customer Information</h4>
			</td>
			</tr>
			<tr>
			<td align="left" width="50%">Name:</td>
			<td align="left">[NAME]</td>
			</tr>
			<tr>
			<td align="left" width="50%">Company:</td>
			<td align="left">[ORGANIZATION]</td>
			</tr>
			<tr>
			<td align="left" width="50%">Phone:</td>
			<td align="left">[PHONE]</td>
			</tr>
			<tr>
			<td align="left" width="50%">Email:</td>
			<td align="left">[EMAIL]</td>
			</tr>
			<tr>
			<td align="left" width="50%">Address:</td>
			<td align="left">[ADDRESS], [CITY], [STATE], [COUNTRY]</td>
			</tr>
			</tbody>
			</table>
			</td>
			<td align="left" valign="top" width="50%">
			<table style="width: 100%;" border="0" cellspacing="0" cellpadding="2">
			<tbody>
			<tr>
			<td style="background-color: #d6d6d6;" colspan="2" align="left">
			<h4 style="margin: 0px;">Invoice Information</h4>
			</td>
			</tr>
			<tr>
			<td align="left" width="50%">Invoice Number:</td>
			<td align="left">[INVOICE_NUMBER]</td>
			</tr>
			<tr>
			<td align="left" width="50%">Invoice Date:</td>
			<td align="left">[INVOICE_DATE]</td>
			</tr>
			<tr>
			<td align="left" width="50%">Invoice Status:</td>
			<td align="left">[INVOICE_STATUS]</td>
			</tr>
			</tbody>
			</table>
			</td>
			</tr>
			</tbody>
			</table>
			</td>
			</tr>
			<tr>
			<td style="background-color: #d6d6d6;" colspan="2" align="left">
			<h4 style="margin: 0px;">Order Items</h4>
			</td>
			</tr>
			<tr>
			<td colspan="2" align="left" width="100%">
			<table border="0" width="100%" cellspacing="0" cellpadding="2">
			<tbody>
			<tr>
			<td align="left" valign="top" width="10%">#</td>
			<td align="left" valign="top" width="60%">Name</td>
			<td align="right" valign="top" width="20%">Price</td>
			<td align="left" valign="top" width="10%">Sub Total</td>
			</tr>
			<tr>
			<td align="left" valign="top" width="10%">1</td>
			<td align="left" valign="top" width="60%">[ITEM_NAME]</td>
			<td align="right" valign="top" width="20%">[ITEM_AMOUNT]</td>
			<td align="left" valign="top" width="10%">[ITEM_SUB_TOTAL]</td>
			</tr>
			<tr>
			<td colspan="3" align="right" valign="top" width="90%">Discount :</td>
			<td align="left" valign="top" width="10%">[DISCOUNT_AMOUNT]</td>
			</tr>
			<tr>
			<td colspan="3" align="right" valign="top" width="90%">Subtotal :</td>
			<td align="left" valign="top" width="10%">[SUB_TOTAL]</td>
			</tr>
			<tr>
			<td colspan="3" align="right" valign="top" width="90%">Tax :</td>
			<td align="left" valign="top" width="10%">[TAX_AMOUNT]</td>
			</tr>
			<tr>
			<td colspan="3" align="right" valign="top" width="90%">Total :</td>
			<td align="left" valign="top" width="10%">[TOTAL_AMOUNT]</td>
			</tr>
			</tbody>
			</table>
			</td>
			</tr>
			</tbody>
			</table>
			</td>
			</tr>
			</tbody>
			</table>
			</td>
			</tr>
			</tbody>
			</table>';
			$sql           = 'INSERT INTO #__eb_configs(config_key, config_value) VALUES ("invoice_format", ' . $db->quote($invoiceFormat) . ')';
			$db->setQuery($sql);
			$db->execute();

			$invoiceFormat = '<table border="0" width="100%" cellspacing="0" cellpadding="2">
			<tbody>
			<tr>
			<td align="left" width="100%">
			<table border="0" width="100%" cellspacing="0" cellpadding="2">
			<tbody>
			<tr>
			<td width="100%">
			<table style="width: 100%;" border="0" cellspacing="0" cellpadding="2">
			<tbody>
			<tr>
			<td align="left" valign="top" width="50%">
			<table style="width: 100%;" border="0" cellspacing="0" cellpadding="2">
			<tbody>
			<tr>
			<td align="left" width="50%">Company Name:</td>
			<td align="left">Ossolution Team</td>
			</tr>
			<tr>
			<td align="left" width="50%">URL:</td>
			<td align="left">http://www.joomdonation.com</td>
			</tr>
			<tr>
			<td align="left" width="50%">Phone:</td>
			<td align="left">84-972409994</td>
			</tr>
			<tr>
			<td align="left" width="50%">E-mail:</td>
			<td align="left">contact@joomdonation.com</td>
			</tr>
			<tr>
			<td align="left" width="50%">Address:</td>
			<td align="left">Lang Ha - Ba Dinh - Ha Noi</td>
			</tr>
			</tbody>
			</table>
			</td>
			<td align="right" valign="middle" width="50%"><img style="border: 0;" src="media/com_eventbooking/invoice_logo.png" alt="" /></td>
			</tr>
			<tr>
			<td colspan="2" align="left" width="100%">
			<table style="width: 100%;" border="0" cellspacing="0" cellpadding="2">
			<tbody>
			<tr>
			<td align="left" valign="top" width="50%">
			<table style="width: 100%;" border="0" cellspacing="0" cellpadding="2">
			<tbody>
			<tr>
			<td style="background-color: #d6d6d6;" colspan="2" align="left">
			<h4 style="margin: 0px;">Customer Information</h4>
			</td>
			</tr>
			<tr>
			<td align="left" width="50%">Name:</td>
			<td align="left">[NAME]</td>
			</tr>
			<tr>
			<td align="left" width="50%">Company:</td>
			<td align="left">[ORGANIZATION]</td>
			</tr>
			<tr>
			<td align="left" width="50%">Phone:</td>
			<td align="left">[PHONE]</td>
			</tr>
			<tr>
			<td align="left" width="50%">Email:</td>
			<td align="left">[EMAIL]</td>
			</tr>
			<tr>
			<td align="left" width="50%">Address:</td>
			<td align="left">[ADDRESS], [CITY], [STATE], [COUNTRY]</td>
			</tr>
			</tbody>
			</table>
			</td>
			<td align="left" valign="top" width="50%">
			<table style="width: 100%;" border="0" cellspacing="0" cellpadding="2">
			<tbody>
			<tr>
			<td style="background-color: #d6d6d6;" colspan="2" align="left">
			<h4 style="margin: 0px;">Invoice Information</h4>
			</td>
			</tr>
			<tr>
			<td align="left" width="50%">Invoice Number:</td>
			<td align="left">[INVOICE_NUMBER]</td>
			</tr>
			<tr>
			<td align="left" width="50%">Invoice Date:</td>
			<td align="left">[INVOICE_DATE]</td>
			</tr>
			<tr>
			<td align="left" width="50%">Invoice Status:</td>
			<td align="left">[INVOICE_STATUS]</td>
			</tr>
			</tbody>
			</table>
			</td>
			</tr>
			</tbody>
			</table>
			</td>
			</tr>
			<tr>
			<td style="background-color: #d6d6d6;" colspan="2" align="left">
			<h4 style="margin: 0px;">Order Items</h4>
			</td>
			</tr>
			<tr>
			<td colspan="2" align="left" width="100%">[EVENTS_LIST]</td>
			</tr>
			</tbody>
			</table>
			</td>
			</tr>
			</tbody>
			</table>
			</td>
			</tr>
			</tbody>
			</table>';
			$sql           = 'INSERT INTO #__eb_configs(config_key, config_value) VALUES ("invoice_format_cart", ' . $db->quote($invoiceFormat) . ')';
			$db->setQuery($sql);
			$db->execute();

			$query = $db->getQuery(true);
			$query->insert('#__eb_configs')
				->columns('config_key, config_value')
				->values('"activate_invoice_feature", 0')
				->values('"send_invoice_to_customer", 0')
				->values('"invoice_start_number", 1')
				->values('"invoice_prefix", "IV"')
				->values('"invoice_number_length", 5');
			$db->setQuery($query);
			$db->execute();
		}

		//Update to use event can be assigned to multiple categories feature
		$sql = 'SELECT COUNT(id) FROM #__eb_event_categories';
		$db->setQuery($sql);
		$total = $db->loadResult();
		if ($total == 0)
		{
			$sql = 'INSERT INTO #__eb_event_categories(event_id, category_id)
				SELECT id, category_id FROM #__eb_events
			';
			$db->setQuery($sql);
			$db->execute();
		}
		//Field Events table
		$sql = 'SELECT COUNT(*) FROM #__eb_field_events';
		$db->setQuery($sql);
		$total = $db->loadResult();
		if (!$total)
		{
			$sql = 'UPDATE #__eb_fields SET event_id = -1 WHERE event_id = 0';
			$db->setQuery($sql);
			$db->execute();
			$sql = 'INSERT INTO #__eb_field_events(field_id, event_id) SELECT id, event_id FROM #__eb_fields WHERE event_id != -1 ';
			$db->setQuery($sql);
			$db->execute();
		}

		$fields = array_keys($db->getTableColumns('#__eb_event_categories'));
		if (!in_array('main_category', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_event_categories` ADD  `main_category` TINYINT NOT NULL DEFAULT  '0' ;";
			$db->setQuery($sql);
			$db->execute();
			$sql = 'SELECT * FROM #__eb_event_categories ORDER BY id DESC';
			$db->setQuery($sql);
			$rowEventCategories = $db->loadObjectList('event_id');
			if (count($rowEventCategories))
			{
				foreach ($rowEventCategories as $rowEventCategory)
				{
					$sql = 'UPDATE #__eb_event_categories SET main_category=1 WHERE id=' . $rowEventCategory->id;
					$db->setQuery($sql);
					$db->execute();
				}
			}
		}

		$fields = array_keys($db->getTableColumns('#__eb_fields'));
		if (!in_array('is_core', $fields))
		{
			$sql = "ALTER TABLE `#__eb_fields` ADD `is_core` TINYINT NOT NULL DEFAULT '0' ";
			$db->setQuery($sql);
			$db->execute();
			$sql = "ALTER TABLE  `#__eb_fields` ADD  `fieldtype` VARCHAR( 50 ) NULL;";
			$db->setQuery($sql);
			$db->execute();
			//Setup core fields
			$sql = 'UPDATE #__eb_fields SET id=id+13, ordering = ordering + 13 ORDER BY id DESC';
			$db->setQuery($sql);
			$db->execute();
			$sql = 'UPDATE #__eb_field_values SET field_id=field_id + 13';
			$db->setQuery($sql);
			$db->execute();
			$sql = 'UPDATE #__eb_field_events SET field_id=field_id + 13';
			$db->setQuery($sql);
			$db->execute();
			$coreFieldsSql = JPATH_ADMINISTRATOR . '/components/com_eventbooking/sql/fields.eventbooking.sql';
			$sql           = JFile::read($coreFieldsSql);
			$queries       = $db->splitSql($sql);
			if (count($queries))
			{
				foreach ($queries as $query)
				{
					$query = trim($query);
					if ($query != '' && $query{0} != '#')
					{
						$db->setQuery($query);
						$db->execute();
					}
				}
			}
			$sql = 'SELECT MAX(id) FROM #__eb_fields';
			$db->setQuery($sql);
			$maxId         = (int) $db->loadResult();
			$autoincrement = $maxId + 1;
			$sql           = 'ALTER TABLE #__eb_fields AUTO_INCREMENT=' . $autoincrement;
			$db->setQuery($sql);
			$db->execute();
			//Update field type , change it to something meaningful
			$typeMapping = array(
				1 => 'Text',
				2 => 'Textarea',
				3 => 'List',
				5 => 'Checkboxes',
				6 => 'Radio',
				7 => 'Date',
				8 => 'Heading',
				9 => 'Message');

			foreach ($typeMapping as $key => $value)
			{
				$sql = "UPDATE #__eb_fields SET fieldtype='$value' WHERE field_type='$key'";
				$db->setQuery($sql);
				$db->execute();
			}

			$sql = "UPDATE #__eb_fields SET fieldtype='List', multiple=1 WHERE field_type='4'";
			$db->setQuery($sql);
			$db->execute();

			$sql = 'UPDATE #__eb_fields SET fieldtype="Countries" WHERE name="country"';
			$db->setQuery($sql);
			$db->execute();
			//MySql, convert data to Json
			$sql = 'SELECT id, field_value FROM #__eb_field_values WHERE field_id IN (SELECT id FROM #__eb_fields WHERE field_type=4 OR field_type=5)';
			$db->setQuery($sql);
			$rowFieldValues = $db->loadObjectList();
			if (count($rowFieldValues))
			{
				foreach ($rowFieldValues as $rowFieldValue)
				{
					$fieldValue = $rowFieldValue->field_value;
					if (strpos($fieldValue, ',') !== false)
					{
						$fieldValue = explode(',', $fieldValue);
					}
					$fieldValue = json_encode($fieldValue);
					$sql        = 'UPDATE #__eb_field_values SET field_value=' . $db->quote($fieldValue) . ' WHERE id=' . $rowFieldValue->id;
					$db->setQuery($sql);
					$db->execute();
				}
			}
			if ($config->display_state_dropdown)
			{
				$sql = 'UPDATE #__eb_fields SET fieldtype="State" WHERE name="state"';
				$db->setQuery($sql);
				$db->execute();
			}
			$sql = 'SELECT * FROM #__eb_events WHERE published =1 ORDER BY id DESC';
			$db->setQuery($sql);
			$event = $db->loadObject();
			if ($event)
			{
				$params = new JRegistry($event->params);
				$keys   = array(
					's_lastname',
					'r_lastname',
					's_organization',
					'r_organization',
					's_address',
					'r_address',
					's_address2',
					'r_address2',
					's_city',
					'r_city',
					's_state',
					'r_state',
					's_zip',
					'r_zip',
					's_country',
					'r_country',
					's_phone',
					'r_phone',
					's_fax',
					'r_fax',
					's_comment',
					'r_comment',
					'gs_lastname',
					'gs_organization',
					'gs_address',
					'gs_address2',
					'gs_city',
					'gs_state',
					'gs_zip',
					'gs_country',
					'gs_phone',
					'gs_fax',
					'gs_email',
					'gs_comment');
				foreach ($keys as $key)
				{
					$config->$key = $params->get($key, 0);
				}
			}
			//Process publish status of core fields
			$publishStatus = array(
				'first_name'   => 1,
				'last_name'    => $config->s_lastname,
				'organization' => $config->s_organization,
				'address'      => $config->s_address,
				'address2'     => $config->s_address2,
				'city'         => $config->s_city,
				'state'        => $config->s_state,
				'zip'          => $config->s_zip,
				'country'      => $config->s_country,
				'phone'        => $config->s_phone,
				'fax'          => $config->s_fax,
				'comment'      => $config->s_comment,
				'email'        => 1);

			foreach ($publishStatus as $key => $value)
			{
				$value = (int) $value;
				$sql   = 'UPDATE #__eb_fields SET published=' . $value . ' WHERE name=' . $db->quote($key);
				$db->setQuery($sql);
				$db->execute();
			}

			$requiredStatus = array(
				'first_name'   => 1,
				'last_name'    => $config->r_lastname,
				'organization' => $config->r_organization,
				'address'      => $config->r_address,
				'address2'     => $config->r_address2,
				'city'         => $config->r_city,
				'state'        => $config->r_state,
				'zip'          => $config->r_zip,
				'country'      => $config->r_country,
				'phone'        => $config->r_phone,
				'fax'          => $config->r_fax,
				'comment'      => $config->r_comment,
				'email'        => 1);

			foreach ($requiredStatus as $key => $value)
			{
				$value = (int) $value;
				$sql   = 'UPDATE #__eb_fields SET required=' . $value . ' WHERE name=' . $db->quote($key);
				$db->setQuery($sql);
				$db->execute();
			}
			//Now, we will need to change display settings for core fields
			$groupMemberFields = array(
				'last_name'    => $config->gs_lastname,
				'organization' => $config->gs_organization,
				'address'      => $config->gs_address,
				'address2'     => $config->gs_address2,
				'city'         => $config->gs_city,
				'state'        => $config->gs_state,
				'zip'          => $config->gs_zip,
				'country'      => $config->gs_country,
				'phone'        => $config->gs_phone,
				'fax'          => $config->gs_fax,
				'comment'      => $config->gs_comment);
			foreach ($groupMemberFields as $fieldName => $showed)
			{
				$showed = (int) $showed;
				if ($showed)
				{
					$displayIn = 0;
				}
				else
				{
					$displayIn = 3;
				}
				$sql = "UPDATE #__eb_fields SET display_in=" . $db->quote($displayIn) . ' WHERE name=' . $db->quote($fieldName);
				$db->setQuery($sql);
				$db->execute();
			}
		}
		if (!in_array('category_id', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_fields` ADD  `category_id` INT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();
			//Migrate fields mapping data
			$sql = 'UPDATE #__eb_fields SET category_id=0 WHERE event_id=-1';
			$db->setQuery($sql);
			$db->execute();
			$sql = 'SELECT id FROM #__eb_fields WHERE event_id != - 1';
			$db->setQuery($sql);
			$rowFields = $db->loadObjectList();
			if (count($rowFields))
			{
				foreach ($rowFields as $rowField)
				{
					//Get the event which this custom field is assigned to
					$sql = 'SELECT event_id FROM #__eb_field_events WHERE field_id=' . $rowField->id . ' ORDER BY id DESC LIMIT 1';
					$db->setQuery($sql);
					$eventId = (int) $db->loadResult();
					if ($eventId)
					{
						//Get main category
						$sql = 'SELECT category_id FROM #__eb_event_categories WHERE event_id=' . $eventId .
							' AND main_category=1';
						$db->setQuery($sql);
						$categoryId = (int) $db->loadResult();
						if ($categoryId)
						{
							$sql = 'UPDATE #__eb_fields SET category_id=' . $categoryId . ' WHERE id=' . $rowField->id;
							$db->setQuery($sql);
							$db->execute();
						}
						else
						{
							//This field is not assigned to any events, just unpublish it
							$sql = 'UPDATE #__eb_fields SET published=0 WHERE id=' . $rowField->id;
							$db->setQuery($sql);
							$db->execute();
						}
					}
					else
					{
						//This field is not assigned to any events, just unpublish it
						$sql = 'UPDATE #__eb_fields SET published=0 WHERE id=' . $rowField->id;
						$db->setQuery($sql);
						$db->execute();
					}
				}
			}
		}
		$sql = "SELECT id, validation_rules FROM #__eb_fields WHERE required = 1";
		$db->setQuery($sql);
		$fields = $db->loadObjectList();
		foreach ($fields as $field)
		{
			if (empty($field->validation_rules))
			{
				$sql = 'UPDATE #__eb_fields SET validation_rules = "validate[required]" WHERE id=' . $field->id;
				$db->setQuery($sql);
				$db->execute();
			}
		}
		//Make sure validation is empty when required=0
		$sql = 'UPDATE #__eb_fields SET validation_rules = "" WHERE required=0 AND validation_rules="validate[required]"';
		$db->setQuery($sql);
		$db->execute();
		//Add show price for free event config option
		$sql = 'SELECT COUNT(id) FROM #__eb_configs WHERE config_key="show_price_for_free_event"';
		$db->setQuery($sql);
		$total = $db->loadResult();
		if (!$total)
		{
			$sql = 'INSERT INTO #__eb_configs(config_key, config_value) VALUES("show_price_for_free_event", 1)';
			$db->setQuery($sql);
			$db->execute();
		}

		$sql = 'CREATE TABLE IF NOT EXISTS `#__eb_messages` (
					`id` INT NOT NULL AUTO_INCREMENT,
		  `message_key` VARCHAR(50) NULL,
		  `message` TEXT NULL,
		  PRIMARY KEY(`id`)
				  ) CHARACTER SET `utf8`;';

		$db->setQuery($sql);
		$db->execute();

		$sql = 'SELECT COUNT(*) FROM #__eb_messages';
		$db->setQuery($sql);
		$total = $db->loadResult();

		if (!$total)
		{
			require_once JPATH_ROOT . '/components/com_eventbooking/helper/helper.php';
			require_once JPATH_ADMINISTRATOR . '/components/com_eventbooking/libraries/rad/table/table.php';
			$row  = new RADTable('#__eb_messages', 'id', $db);
			$keys = array(
				'admin_email_subject',
				'admin_email_body',
				'user_email_subject',
				'user_email_body',
				'user_email_body_offline',
				'registration_form_message',
				'registration_form_message_group',
				'number_members_form_message',
				'member_information_form_message',
				'confirmation_message',
				'thanks_message',
				'thanks_message_offline',
				'cancel_message',
				'registration_cancel_message_free',
				'registration_cancel_message_paid',
				'invitation_form_message',
				'invitation_email_subject',
				'invitation_email_body',
				'invitation_complete',
				'reminder_email_subject',
				'reminder_email_body',
				'registration_cancel_email_subject',
				'registration_cancel_email_body',
				'registration_approved_email_subject',
				'registration_approved_email_body',
				'waitinglist_form_message',
				'waitinglist_complete_message',
				'watinglist_confirmation_subject',
				'watinglist_confirmation_body',
				'watinglist_notification_subject',
				'watinglist_notification_body');
			foreach ($keys as $key)
			{
				$row->id          = 0;
				$row->message_key = $key;
				$row->message     = $config->{$key};
				$row->store();
			}
		}

		//Update ACL field, from 1.4.1 and before to 1.4.2
		$sql = 'UPDATE #__eb_categories SET `access` = 1 WHERE `access` = 0';
		$db->setQuery($sql);
		$db->execute();

		$sql = 'UPDATE #__eb_events SET `access` = 1 WHERE `access` = 0';
		$db->setQuery($sql);
		$db->execute();

		$sql = 'UPDATE #__eb_events SET `registration_access` = 1 WHERE `registration_access` = 0';
		$db->setQuery($sql);
		$db->execute();

		//Update SEF setting
		$sql = 'SELECT COUNT(*) FROM #__eb_configs WHERE config_key="insert_event_id"';
		$db->setQuery($sql);
		$total = $db->loadResult();
		if (!$total)
		{
			$sql = "INSERT INTO #__eb_configs(config_key, config_value) VALUES('insert_event_id', '0') ";
			$db->setQuery($sql);
			$db->execute();

			$sql = "INSERT INTO #__eb_configs(config_key, config_value) VALUES('insert_category', '0') ";
			$db->setQuery($sql);
			$db->execute();
		}
		//SEF urls table
		$sql = "CREATE TABLE IF NOT EXISTS `#__eb_urls` (
					`id` int(11) NOT NULL AUTO_INCREMENT,
          `md5_key` text,
          `query` text,
          PRIMARY KEY (`id`)
				  	) DEFAULT CHARSET=utf8;
				  	";
		$db->setQuery($sql);
		$db->execute();
		$db->truncateTable('#__eb_urls');

		// Migrate waiting list data
		$sql = 'SELECT COUNT(*) FROM #__eb_waiting_lists';
		$db->setQuery($sql);
		$total = $db->loadResult();
		if ($total)
		{
			$sql = "INSERT INTO #__eb_registrants(
				user_id, event_id, first_name, last_name, organization, address, address2, city,
		 		state, country, zip, phone, fax, email, number_registrants, register_date, notified, published
			)
		 	SELECT user_id, event_id, first_name, last_name, organization, address, address2, city,
		 	state, country, zip, phone, fax, email, number_registrants, register_date, notified, 3
		 	FROM #__eb_waiting_lists ORDER BY id
		 	";
			$db->setQuery($sql);
			$db->execute();
		}
		$db->truncateTable('#__eb_waiting_lists');
		
		// Update old links from older version to 2.0.x
		$query = $db->getQuery(true);
		$query->update('#__menu')
			->set($db->quoteName('link') . '=' . $db->quote('index.php?option=com_eventbooking&view=locations'))
			->where($db->quoteName('link') . '=' . $db->quote('index.php?option=com_eventbooking&view=locationlist'));
		$db->setQuery($query);
		$db->execute();

		$query->clear();
		$query = $db->getQuery(true);
		$query->update('#__menu')
			->set($db->quoteName('link') . '=' . $db->quote('index.php?option=com_eventbooking&view=location&layout=form'))
			->where($db->quoteName('link') . '=' . $db->quote('index.php?option=com_eventbooking&view=addlocation'));
		$db->setQuery($query);
		$db->execute();

		// Field categories table
		$sql = "CREATE TABLE IF NOT EXISTS `#__eb_field_categories` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `field_id` int(11) DEFAULT NULL,
		  `category_id` int(11) DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) CHARACTER SET `utf8`;";

		$db->setQuery($sql);
		$db->execute();

		$sql = 'SELECT COUNT(*) FROM #__eb_field_categories';
		$db->setQuery($sql);
		$total = $db->loadResult();
		if (!$total)
		{
			$sql = 'UPDATE #__eb_fields SET category_id = -1 WHERE category_id = 0';
			$db->setQuery($sql);
			$db->execute();
			$sql = 'INSERT INTO #__eb_field_categories(field_id, category_id) SELECT id, category_id FROM #__eb_field_categories WHERE category_id != -1 ';
			$db->setQuery($sql);
			$db->execute();
		}

		// Coupon events
		$sql = "CREATE TABLE IF NOT EXISTS `#__eb_coupon_events` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `coupon_id` int(11) DEFAULT NULL,
		  `event_id` int(11) DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) CHARACTER SET `utf8`;";
		$db->setQuery($sql);
		$db->execute();

		$sql = 'SELECT COUNT(*) FROM #__eb_coupon_events';
		$db->setQuery($sql);
		$total = $db->loadResult();
		if (!$total)
		{
			$sql = 'UPDATE #__eb_coupons SET event_id = -1 WHERE event_id = 0';
			$db->setQuery($sql);
			$db->execute();
			$sql = 'INSERT INTO #__eb_coupon_events(coupon_id, event_id) SELECT id, event_id FROM #__eb_coupons WHERE event_id != -1 ';
			$db->setQuery($sql);
			$db->execute();
		}

		// Try to delete the file com_eventbooking.zip from tmp folder
		$tmpFolder = JFactory::getConfig()->get('tmp_path');
		if (!JFolder::exists($tmpFolder))
		{
			$tmpFolder = JPATH_ROOT . '/tmp';
		}
		if (file_exists($tmpFolder . '/com_eventbooking.zip'))
		{
			JFile::delete($tmpFolder . '/com_eventbooking.zip');
		}

		// Try to clean tmp folders
		$folders = JFolder::folders($tmpFolder);
		if (count($folders))
		{
			foreach ($folders as $installFolder)
			{
				if (strpos($installFolder, 'install_') !== false)
				{
					JFolder::delete($tmpFolder . '/' . $installFolder);
				}
			}
		}

		// Files, Folders clean up
		$deleteFiles = array(
			JPATH_ADMINISTRATOR . '/components/com_eventbooking/model/daylightsaving.php',
			JPATH_ADMINISTRATOR . '/components/com_eventbooking/controller/daylightsaving.php',
			JPATH_ADMINISTRATOR . '/components/com_eventbooking/controller/event.php',
			JPATH_ROOT . '/components/com_eventbooking/helper/os_cart.php',
			JPATH_ROOT . '/components/com_eventbooking/helper/fields.php',
			JPATH_ROOT . '/components/com_eventbooking/helper/captcha.php',
			JPATH_ROOT . '/components/com_eventbooking/views/register/tmpl/group_member.php',
			JPATH_ROOT . '/components/com_eventbooking/views/waitinglist/tmpl/complete.php',
			JPATH_ROOT . '/components/com_eventbooking/models/waitinglist.php',
			JPATH_ADMINISTRATOR . '/components/com_eventbooking/model/waitings.php',
			JPATH_ADMINISTRATOR . '/components/com_eventbooking/model/waiting.php',
			JPATH_ROOT . '/media/com_eventbooking/.htaccess'
		);

		$deleteFolders = array(
			JPATH_ADMINISTRATOR . '/components/com_eventbooking/models',
			JPATH_ADMINISTRATOR . '/components/com_eventbooking/views',
			JPATH_ADMINISTRATOR . '/components/com_eventbooking/view/daylightsaving',
			JPATH_ROOT . '/components/com_eventbooking/views/confirmation',
			JPATH_ADMINISTRATOR . '/components/com_eventbooking/view/waiting',
			JPATH_ADMINISTRATOR . '/components/com_eventbooking/view/waitings',			
			JPATH_ROOT . '/components/com_eventbooking/models',
			JPATH_ROOT . '/components/com_eventbooking/assets'
		);

		foreach ($deleteFiles as $file)
		{
			if (JFile::exists(JPATH_ROOT . $file))
			{
				JFile::delete(JPATH_ROOT . $file);
			}
		}

		foreach ($deleteFolders as $folder)
		{
			if (JFolder::exists(JPATH_ROOT . $folder))
			{
				JFolder::delete(JPATH_ROOT . $folder);
			}
		}

		// We don't need views folder for Joomla 3
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			if (JFolder::exists(JPATH_ROOT . '/components/com_eventbooking/views'))
			{
				JFolder::delete(JPATH_ROOT . '/components/com_eventbooking/views');
			}
		}
		// Redirect to dashboard view
		$installType = $this->input->getCmd('install_type', '');
		if ($installType == 'install')
		{
			$msg = JText::_('The extension was successfully installed');
		}
		else
		{
			$msg = JText::_('The extension was successfully updated');
		}
		//Redirecting users to dasdboard
		JFactory::getApplication()->redirect('index.php?option=com_eventbooking&view=dashboard', $msg);
	}

	/**
	 * Check to see the installed version is up to date or not
	 *
	 * @return int 0 : error, 1 : Up to date, 2 : outof date
	 */
	function check_update()
	{
		$installedVersion = EventbookingHelper::getInstalledVersion();
		$result           = array();
		$result['status'] = 0;
		if (function_exists('curl_init'))
		{
			$url = 'http://joomdonationdemo.com/versions/eventbooking.txt';
			$ch  = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$latestVersion = curl_exec($ch);
			curl_close($ch);
			if ($latestVersion)
			{
				if (version_compare($latestVersion, $installedVersion, 'gt'))
				{
					$result['status']  = 2;
					$result['message'] = JText::sprintf('EB_UPDATE_CHECKING_UPDATEFOUND', $latestVersion);
				}
				else
				{
					$result['status']  = 1;
					$result['message'] = JText::_('EB_UPDATE_CHECKING_UPTODATE');
				}
			}
		}
		echo json_encode($result);
		JFactory::getApplication()->close();
	}

	/**
	 * Reset the urls table
	 */
	public function reset_urls()
	{
		JFactory::getDbo()->truncateTable('#__eb_urls');
		$this->setRedirect('index.php?option=com_eventbooking&view=dashboard', JText::_('Urls have successfully reset'));
	}

	/**
	 * Process download a file
	 */
	public function download_file()
	{
		$filePath = JPATH_ROOT . '/media/com_eventbooking/files';
		$fileName = JRequest::getVar('file_name', '');
		if (file_exists($filePath . '/' . $fileName))
		{
			while (@ob_end_clean()) ;
			EventbookingHelper::processDownload($filePath . '/' . $fileName, $fileName, true);
			JFactory::getApplication()->close();
		}
		else
		{
			JFactory::getApplication()->redirect('index.php?option=com_eventbooking&view=dashboard', JText::_('File does not exist'));
		}
	}

	/**
	 * Get profile data of the registrant, return reson format using for ajax request
	 *
	 */
	function get_profile_data()
	{
		$config  = EventbookingHelper::getConfig();
		$input   = JFactory::getApplication()->input;
		$userId  = $input->getInt('user_id', 0);
		$eventId = $input->getInt('event_id');
		$data    = array();
		if ($userId && $eventId)
		{
			$rowFields = EventbookingHelper::getFormFields($eventId, 0);
			$db        = JFactory::getDbo();
			$query     = $db->getQuery(true);
			$query->clear();
			$query->select('*')
				->from('#__eb_registrants')
				->where('user_id=' . $userId);
			$db->setQuery($query);
			$rowProfile = $db->loadObject();
			if ($rowProfile)
			{
				$data = EventbookingHelper::getFormData($rowFields, $eventId, $userId, $config);
			}
			elseif (JPluginHelper::isEnabled('user', 'profile') && !$config->cb_integration)
			{
				$syncronizer = new RADSynchronizerJoomla();
				$mappings    = array();
				foreach ($rowFields as $rowField)
				{
					if ($rowField->profile_field_mapping)
					{
						$mappings[$rowField->name] = $rowField->profile_field_mapping;
					}
				}
				$data = $syncronizer->getData($userId, $mappings);
			}
			elseif ($config->cb_integration == 1)
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

		}

		if ($userId && !isset($data['first_name']))
		{
			//Load the name from Joomla default name
			$user = JFactory::getUser($userId);
			$name = $user->name;
			if ($name)
			{
				$pos = strpos($name, ' ');
				if ($pos !== false)
				{
					$data['first_name'] = substr($name, 0, $pos);
					$data['last_name']  = substr($name, $pos + 1);
				}
				else
				{
					$data['first_name'] = $name;
					$data['last_name']  = '';
				}
			}
		}
		if ($userId && !isset($data['email']))
		{
			if (empty($user))
			{
				$user = JFactory::getUser($userId);
			}
			$data['email'] = $user->email;
		}
		echo json_encode($data);
		JFactory::getApplication()->close();
	}
}