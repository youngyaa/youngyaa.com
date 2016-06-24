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
 * Membership Pro controller
 *
 * @package        Joomla
 * @subpackage     Membership Pro
 */
class OSMembershipController extends MPFControllerAdmin
{

	/**
	 * Display information
	 */
	public function display($cachable = false, array $urlparams = Array())
	{
		JFactory::getDocument()->addStyleSheet(JUri::base(true) . '/components/com_osmembership/assets/css/style.css');

		if (version_compare(JVERSION, '3.0', 'lt'))
		{
			OSMembershipHelper::loadBootstrap();
		}

		OSMembershipHelper::loadJQuery();

		JHtml::_('script', OSMembershipHelper::getSiteUrl() . 'components/com_osmembership/assets/js/jquery-noconflict.js', false, false);

		parent::display($cachable, $urlparams);

		if ($this->input->getCmd('format', 'html') == 'html')
		{
			OSMembershipHelper::displayCopyRight();
		}
	}
	
    /**
     * Check to see the installed version is up to date or not
     *
     * @return int 0 : error, 1 : Up to date, 2 : outof date
     */
    public function check_update()
    {
        $installedVersion = OSMembershipHelper::getInstalledVersion();
        $result = array();
        $result['status'] = 0;
        if (function_exists('curl_init'))
        {
            $url = 'http://joomdonationdemo.com/versions/membershippro.txt';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $latestVersion = curl_exec($ch);
            curl_close($ch);
            if ($latestVersion)
            {
                if (version_compare($latestVersion, $installedVersion, 'gt'))
                {
                    $result['status'] = 2;
                    $result['message'] = JText::sprintf('OSM_UPDATE_CHECKING_UPDATEFOUND', $latestVersion);
                }
                else
                {
                    $result['status'] = 1;
                    $result['message'] = JText::_('OSM_UPDATE_CHECKING_UPTODATE');
                }
            }
        }
        echo json_encode($result);
        JFactory::getApplication()->close();
    }

	/**
	 * Download invoice
	 */
	public function download_invoice()
	{
		$id = $this->input->getInt('id');
		OSMembershipHelper::downloadInvoice($id);
	}

	/**
	 * Download file uploaded by subscriber
	 */
	public function download_file()
	{
		$filePath = 'media/com_osmembership/upload';
		$fileName = $this->input->getString('file_name', '');

		if (file_exists(JPATH_ROOT . '/' . $filePath . '/' . $fileName))
		{
			while (@ob_end_clean());
			OSMembershipHelper::processDownload(JPATH_ROOT . '/' . $filePath . '/' . $fileName, $fileName, true);
			exit();
		}
		else
		{
			$this->setRedirect('index.php?option=com_osmembership', JText::_('OSM_FILE_NOT_EXIST'));
		}
	}

	/**
	 * Method to allow sharing language files for Events Booking
	 */
	public function share_translation()
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('lang_code')
			->from('#__languages')
			->where('published = 1')
			->where('lang_code != "en-GB"')
			->order('ordering');
		$db->setQuery($query);
		$languages = $db->loadObjectList();

		if (count($languages))
		{
			$mailer   = JFactory::getMailer();
			$jConfig  = JFactory::getConfig();
			$mailFrom = $jConfig->get('mailfrom');
			$fromName = $jConfig->get('fromname');
			$mailer->setSender(array($mailFrom, $fromName));
			$mailer->addRecipient('tuanpn@joomdonation.com');
			$mailer->setSubject('Language Packages for Membership Pro shared by ' . JUri::root());
			$mailer->setBody('Dear Tuan \n. I am happy to share my language packages for Membership Pro.\n Enjoy!');
			foreach ($languages as $language)
			{
				$tag = $language->lang_code;
				if (file_exists(JPATH_ROOT . '/language/' . $tag . '/' . $tag . '.com_osmembership.ini'))
				{
					$mailer->addAttachment(JPATH_ROOT . '/language/' . $tag . '/' . $tag . '.com_osmembership.ini', $tag . '.com_osmembership.ini');
				}

				if (file_exists(JPATH_ADMINISTRATOR . '/language/' . $tag . '/' . $tag . '.com_osmembership.ini'))
				{
					echo JPATH_ADMINISTRATOR . '/language/' . $tag . '/' . $tag . '.com_osmembership.ini';
					$mailer->addAttachment(JPATH_ADMINISTRATOR . '/language/' . $tag . '/' . $tag . '.com_osmembership.ini', 'admin.' . $tag . '.com_osmembership.ini');
				}
			}

			require_once JPATH_COMPONENT . '/libraries/vendor/dbexporter/dumper.php';

			$tables = array($db->replacePrefix('#__eb_fields'), $db->replacePrefix('#__eb_messages'));

			try
			{

				$sqlFile = $tag . '.com_osmembership.sql';
				$options = array(
					'host'           => $jConfig->get('host'),
					'username'       => $jConfig->get('user'),
					'password'       => $jConfig->get('password'),
					'db_name'        => $jConfig->get('db'),
					'include_tables' => $tables
				);
				$dumper  = Shuttle_Dumper::create($options);
				$dumper->dump(JPATH_ROOT . '/tmp/' . $sqlFile);

				$mailer->addAttachment(JPATH_ROOT . '/tmp/' . $sqlFile, $sqlFile);

			}
			catch (Exception $e)
			{
				//Do nothing
			}

			$mailer->Send();

			$msg = 'Thanks so much for sharing your language files to Membership Pro Community';
		}
		else
		{
			$msg = 'Thanks so willing to share your language files to Membership Pro Community. However, you don"t have any none English language file to share';
		}

		$this->setRedirect('index.php?option=com_osmembership&view=dashboard', $msg);
	}

	/**
	 * Reset SEF urls
	 */
	public function reset_urls()
	{
		$db = JFactory::getDbo();
		$db->truncateTable('#__osmembership_sefurls');
		$this->setRedirect('index.php?option=com_osmembership&view=dashboard', JText::_('SEF urls has successfully been reset'));
	}

	/**
	 * Get profile data of the subscriber, using for json format
	 *
	 */
	public function get_profile_data()
	{
		$config = OSMembershipHelper::getConfig();
		$input = JFactory::getApplication()->input;
		$userId = $input->getInt('user_id', 0);
		$planId = $input->getInt('plan_id');
		$data = array();
		if ($userId && $planId)
		{
			$rowFields = OSMembershipHelper::getProfileFields($planId, true);
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->clear();
			$query->select('*')
				->from('#__osmembership_subscribers')
				->where('user_id=' . $userId.' AND is_profile=1');
			$db->setQuery($query);
			$rowProfile = $db->loadObject();
			$data = array();
			if ($rowProfile)
			{
				$data = OSMembershipHelper::getProfileData($rowProfile, $planId, $rowFields);
			}
			else
			{
				// Trigger plugin to get data
				$mappings = array();
				foreach ($rowFields as $rowField)
				{
					if ($rowField->field_mapping)
					{
						$mappings[$rowField->name] = $rowField->field_mapping;
					}
				}
				JPluginHelper::importPlugin( 'osmembership' );
				$dispatcher = JDispatcher::getInstance();
				$results = $dispatcher->trigger( 'onGetProfileData', array($userId, $mappings));
				if (count($results))
				{
					foreach($results as $res)
					{
						if (is_array($res) && count($res))
						{
							$data = $res;
							break;
						}
					}
				}
			}
			if (! count($data) && JPluginHelper::isEnabled('user', 'profile') && !$config->cb_integration)
			{
				$syncronizer = new RADSynchronizerJoomla();
				$mappings = array();
				foreach ($rowFields as $rowField)
				{
					if ($rowField->profile_field_mapping)
					{
						$mappings[$rowField->name] = $rowField->profile_field_mapping;
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
					$data['last_name'] =  substr($name, $pos + 1);
				}
				else
				{
					$data['first_name'] = $name;
					$data['last_name'] = '';
				}
			}
		}
		if ($userId && !isset($data['email']))
		{
			$user = JFactory::getUser($userId);
			$data['email'] = $user->email;
		}
		echo json_encode($data);
		JFactory::getApplication()->close();
	}

	/**
	 * Build EU tax rules
	 */
	public function build_eu_tax_rules()
	{
		$db = JFactory::getDbo();
		$db->truncateTable('#__osmembership_taxes');
		$defaultCountry     = OSmembershipHelper::getConfigValue('default_country');
		$defaultCountryCode = OSMembershipHelper::getCountryCode($defaultCountry);
		// Without VAT number, use local tax rate
		foreach (OSMembershipHelperEuvat::$europeanUnionVATInformation as $countryCode => $vatInfo)
		{
			$countryName    = $db->quote($vatInfo[0]);
			$countryTaxRate = OSMembershipHelperEuvat::getEUCountryTaxRate($countryCode);
			$sql            = "INSERT INTO #__osmembership_taxes(plan_id, country, rate, vies, published) VALUES(0, $countryName, $countryTaxRate, 0, 1)";
			$db->setQuery($sql);
			$db->execute();

			if ($countryCode == $defaultCountryCode)
			{
				$localTaxRate = OSMembershipHelperEuvat::getEUCountryTaxRate($defaultCountryCode);
				$sql          = "INSERT INTO #__osmembership_taxes(plan_id, country, rate, vies, published) VALUES(0, $countryName, $localTaxRate, 1, 1)";
				$db->setQuery($sql);
				$db->execute();
			}
		}

		$this->setRedirect('index.php?option=com_osmembership&view=taxes', JText::_('EU Tax Rules were successfully created'));
	}

	/**
	 * Update db scheme when users upgrade from old version to new version
	 */
	public function upgrade()
	{
		error_reporting(0);
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		$db = JFactory::getDbo();
		require_once JPATH_ROOT . '/components/com_osmembership/helper/helper.php';
		//First, we will need to create additional database tables which was not available in old version
		$prefix = $db->getPrefix();
		$tables = $db->getTableList();
		if (!in_array($prefix . 'osmembership_categories', $tables))
		{
			//Create the categories table, added in version 1.1.1
			$sql = "CREATE TABLE IF NOT EXISTS `#__osmembership_categories` (
		        `id` INT NOT NULL AUTO_INCREMENT,
		        `title` VARCHAR(255) NULL,
		        `description` TEXT NULL,
		        `published` TINYINT UNSIGNED NULL,
		        PRIMARY KEY(`id`)
		        ) DEFAULT CHARSET=utf8 ;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array($prefix . 'osmembership_field_plan', $tables))
		{
			//Create the categories table, added in version 1.1.1
			$sql = "CREATE TABLE IF NOT EXISTS `#__osmembership_field_plan` (
		            `id` int(11) NOT NULL AUTO_INCREMENT,
		            `field_id` int(11) DEFAULT NULL,
		            `plan_id` int(11) DEFAULT NULL,
		            PRIMARY KEY (`id`)
		          ) DEFAULT CHARSET=utf8;";
			$db->setQuery($sql);
			$db->execute();
			//Need to migrate data here
			$sql = 'INSERT INTO #__osmembership_field_plan(field_id, plan_id)
                SELECT id, plan_id FROM #__osmembership_fields WHERE plan_id > 0
                ';
			$db->setQuery($sql);
			$db->execute();

			$sql = 'UPDATE #__osmembership_fields SET plan_id=1 WHERE plan_id > 0';
			$db->setQuery($sql);
			$db->execute();
		}
		if (!in_array($prefix . 'osmembership_messages', $tables))
		{
			$sql = 'CREATE TABLE IF NOT EXISTS `#__osmembership_messages` (
				  `id` INT NOT NULL AUTO_INCREMENT,
				  `message_key` VARCHAR(50) NULL,
				  `message` TEXT NULL,
				  PRIMARY KEY(`id`)
				) CHARACTER SET `utf8`;';
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array($prefix . 'osmembership_states', $tables))
		{
			$statesSql = JPATH_ADMINISTRATOR . '/components/com_osmembership/sql/states.osmembership.sql';
			$sql       = JFile::read($statesSql);
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

		$configSql = JPATH_ADMINISTRATOR . '/components/com_osmembership/sql/menus.osmembership.sql';
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

		$sql = 'SELECT COUNT(*) FROM #__osmembership_configs';
		$db->setQuery($sql);
		$total = $db->loadResult();
		if (!$total)
		{
			$configSql = JPATH_ADMINISTRATOR . '/components/com_osmembership/sql/config.osmembership.sql';
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
		//Change coupon code data type
		$sql = 'ALTER TABLE  `#__osmembership_coupons` CHANGE  `valid_from`	`valid_from` datetime DEFAULT NULL;';
		$db->setQuery($sql);
		$db->execute();

		$sql = "ALTER TABLE  `#__osmembership_coupons` CHANGE  `valid_to`	`valid_to` datetime DEFAULT NULL;";
		$db->setQuery($sql);
		$db->execute();


		$sql = 'SELECT COUNT(*) FROM #__osmembership_plugins';
		$db->setQuery($sql);
		$total = $db->loadResult();
		if (!$total)
		{
			$pluginsSql = JPATH_ADMINISTRATOR . '/components/com_osmembership/sql/plugins.osmembership.sql';
			$sql        = JFile::read($pluginsSql);
			$queries    = $db->splitSql($sql);
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

		//Invoice data
		$sql = 'SELECT COUNT(*) FROM #__osmembership_configs WHERE config_key="invoice_format"';
		$db->setQuery($sql);
		$total = $db->loadResult();
		if (!$total)
		{
			$configSql = JPATH_ADMINISTRATOR . '/components/com_osmembership/sql/config.invoice.sql';
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

		$sql = "SELECT COUNT(*) FROM #__osmembership_currencies WHERE currency_code='RUB'";
		$db->setQuery($sql);
		$total = $db->loadResult();
		if (!$total)
		{
			$sql = "INSERT INTO #__osmembership_currencies(currency_code, currency_name) VALUES('RUB', 'Russian Rubles')";
			$db->setQuery($sql);
			$db->execute();
		}


		$fields = array_keys($db->getTableColumns('#__osmembership_countries'));
		if (!in_array('id', $fields))
		{
			//Change the name of the name of column from country_id to ID
			$sql = 'ALTER TABLE `#__osmembership_countries` CHANGE `country_id` `id` INT(11) NOT NULL AUTO_INCREMENT;';
			$db->setQuery($sql);
			$db->execute();

			//Add country ID column back for BC
			$sql = "ALTER TABLE  `#__osmembership_countries` ADD  `country_id` INT(11) NOT NULL DEFAULT '0';";
			$db->setQuery($sql);
			$db->execute();

			//Set country_id value the same with id
			$sql = 'UPDATE #__osmembership_countries SET country_id=id';
			$db->setQuery($sql);
			$db->execute();

		}

		$fields = array_keys($db->getTableColumns('#__osmembership_states'));

		if (!in_array('published', $fields))
		{
			$db->setQuery("ALTER TABLE `#__osmembership_states` ADD `published` TINYINT( 4 ) NOT NULL DEFAULT '1'");
			$db->execute();
			$db->setQuery("UPDATE `#__osmembership_states` SET `published` = 1");
			$db->execute();
		}
		if (!in_array('id', $fields))
		{
			//Change the name of the name of column from country_id to ID
			$sql = 'ALTER TABLE `#__osmembership_states` CHANGE `state_id` `id` INT(11) NOT NULL AUTO_INCREMENT;';
			$db->setQuery($sql);
			$db->execute();

			//Add country ID column back for BC
			$sql = "ALTER TABLE  `#__osmembership_states` ADD  `state_id` INT(11) NOT NULL DEFAULT '0';";
			$db->setQuery($sql);
			$db->execute();

			//Set country_id value the same with id
			$sql = 'UPDATE #__osmembership_states SET state_id=id';
			$db->setQuery($sql);
			$db->execute();
		}
		#Custom Fields table
		$fields = array_keys($db->getTableColumns('#__osmembership_fields'));
		if (!in_array('hide_on_membership_renewal', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_fields` ADD  `hide_on_membership_renewal` TINYINT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();
		}


		if (!in_array('show_on_members_list', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_fields` ADD  `show_on_members_list` TINYINT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();

			$defaultShowedFields = array("first_name", "last_name", "email", "organization");
			$sql                 = 'UPDATE #__osmembership_fields SET show_on_members_list = 1 WHERE name IN ("' . implode('","', $defaultShowedFields) . '")';
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('fee_field', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_fields` ADD  `fee_field` TINYINT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();
		}
		if (!in_array('fee_values', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_fields` ADD  `fee_values` TEXT NULL;";
			$db->setQuery($sql);
			$db->execute();
		}
		if (!in_array('fee_formula', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_fields` ADD  `fee_formula` VARCHAR( 255 ) NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('profile_field_mapping', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_fields` ADD  `profile_field_mapping` VARCHAR( 50 ) NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('depend_on_field_id', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_fields` ADD  `depend_on_field_id` INT NOT NULL DEFAULT '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('depend_on_options', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_fields` ADD  `depend_on_options` TEXT NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('max_length', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_fields` ADD  `max_length` INT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('place_holder', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_fields` ADD   `place_holder` VARCHAR( 255 ) NULL;";
			$db->setQuery($sql);
			$db->execute();
		}
		if (!in_array('multiple', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_fields` ADD  `multiple` TINYINT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('validation_rules', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_fields` ADD  `validation_rules` VARCHAR( 255 ) NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('validation_error_message', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_fields` ADD  `validation_error_message` VARCHAR( 255 ) NULL;";
			$db->setQuery($sql);
			$db->execute();
		}
		$replace = false;
		if (!in_array('fieldtype', $fields))
		{
			$replace = true;
			$sql     = "ALTER TABLE  `#__osmembership_fields` ADD  `fieldtype` VARCHAR( 50 ) NULL;";
			$db->setQuery($sql);
			$db->execute();

			//Update field type , change it to something meaningful
			$typeMapping = array(
				0 => 'Text',
				1 => 'Textarea',
				2 => 'List',
				3 => 'Checkboxes',
				4 => 'Radio',
				5 => 'Date',
				6 => 'Heading',
				7 => 'Message',
				9 => 'File');

			foreach ($typeMapping as $key => $value)
			{
				$sql = "UPDATE #__osmembership_fields SET fieldtype='$value' WHERE field_type='$key'";
				$db->setQuery($sql);
				$db->execute();
			}

			$sql = "UPDATE #__osmembership_fields SET fieldtype='List', multiple=1 WHERE field_type='8'";
			$db->setQuery($sql);
			$db->execute();

			$sql = 'UPDATE #__osmembership_fields SET fieldtype="countries" WHERE name="country"';
			$db->setQuery($sql);
			$db->execute();
			//MySql, convert data to Json
			$sql = 'SELECT id, field_value FROM #__osmembership_field_value WHERE field_id IN (SELECT id FROM #__osmembership_fields WHERE field_type=3 OR field_type=8)';
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
					$sql        = 'UPDATE #__osmembership_field_value SET field_value=' . $db->quote($fieldValue) . ' WHERE id=' . $rowFieldValue->id;
					$db->setQuery($sql);
					$db->execute();
				}
			}
		}

		########1.6.3, migrate data to new fields API ###############################################
		$sql = 'SELECT COUNT(*) FROM #__osmembership_fields';
		$db->setQuery($sql);
		$total = $db->loadResult();
		if ($total)
		{

			$sql = 'SELECT name, published FROM #__osmembership_fields WHERE is_core=1';
			$db->setQuery($sql);
			$coreFields = $db->loadObjectList('name');
		}
		if (!$total || $replace)
		{
			$coreFieldsSql = JPATH_ADMINISTRATOR . '/components/com_osmembership/sql/fields.osmembership.sql';
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
		}

		if ($replace && $total)
		{
			foreach ($coreFields as $name => $field)
			{
				$sql = 'UPDATE #__osmembership_fields SET published=' . (int) $field->published . ' WHERE name=' . $db->quote($name);
				$db->setQuery($sql);
				$db->execute();
			}
		}

		$sql = "SELECT id, validation_rules FROM #__osmembership_fields WHERE required = 1";
		$db->setQuery($sql);
		$fields = $db->loadObjectList();
		foreach ($fields as $field)
		{
			if (empty($field->validation_rules))
			{
				$sql = 'UPDATE #__osmembership_fields SET validation_rules = "validate[required]" WHERE id=' . $field->id;
				$db->setQuery($sql);
				$db->execute();
			}
		}

		// Allow access level for custom field
		$fields = array_keys($db->getTableColumns('#__osmembership_fields'));
		if (!in_array('access', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_fields` ADD  `access` TINYINT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();

			$sql = 'UPDATE  #__osmembership_fields SET `access` = 1';
			$db->setQuery($sql);
			$db->execute();
		}

		####This code below is used for fixing the bugs in with not required fields in initial released of version 1.6.3##########
		$sql = "SELECT id, validation_rules FROM #__osmembership_fields WHERE required = 0";
		$db->setQuery($sql);
		$fields = $db->loadObjectList();
		foreach ($fields as $field)
		{
			if ($field->validation_rules == 'validate[required]')
			{
				$sql = 'UPDATE #__osmembership_fields SET validation_rules = "" WHERE id=' . $field->id;
				$db->setQuery($sql);
				$db->execute();
			}
		}

		$fields = array_keys($db->getTableColumns('#__osmembership_categories'));
		if (!in_array('access', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_categories` ADD  `access` TINYINT NOT NULL DEFAULT  '1';";
			$db->setQuery($sql);
			$db->execute();

			$sql = 'UPDATE #__osmembership_categories SET `access`=1';
			$db->setQuery($sql);
			$db->execute();
		}
		if (!in_array('ordering', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_categories` ADD  `ordering` INT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();

			$sql = 'UPDATE #__osmembership_categories SET `ordering`=id';
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('parent_id', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_categories` ADD  `parent_id` INT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('level', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_categories` ADD  `level` TINYINT( 4 ) NOT NULL DEFAULT '1';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('alias', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_categories` ADD  `alias` varchar(255) NOT NULL DEFAULT '';";
			$db->setQuery($sql);
			$db->execute();

			$sql = 'SELECT id, title FROM #__osmembership_categories';
			$db->setQuery($sql);
			$rows = $db->loadObjectList();
			if (count($rows))
			{
				foreach ($rows as $row)
				{
					$alias = JApplication::stringURLSafe($row->title);
					$sql   = 'UPDATE #__osmembership_categories SET `alias`="' . $alias . '" WHERE id=' . $row->id;
					$db->setQuery($sql);
					$db->execute();
				}
			}
		}
		#Subscription plans table
		$fields = array_keys($db->getTableColumns('#__osmembership_plans'));
		if (!in_array('subscription_length_unit', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_plans` ADD  `subscription_length_unit` CHAR(1) NULL;";
			$db->setQuery($sql);
			$db->execute();


			//Need to update the length to reflect new unit
			$sql = 'SELECT id, subscription_length FROM #__osmembership_plans';
			$db->setQuery($sql);
			$rowPlans = $db->loadObjectList();
			for ($i = 0, $n = count($rowPlans); $i < $n; $i++)
			{
				$rowPlan = $rowPlans[$i];
				list($frequency, $length) = OSMembershipHelper::getRecurringSettingOfPlan($rowPlan->subscription_length);
				$sql = 'UPDATE #__osmembership_plans SET subscription_length=' . (int) $length . ', subscription_length_unit="' . $frequency . '" WHERE id=' . $rowPlan->id;
				$db->setQuery($sql);
				$db->execute();
			}
		}
		if (!in_array('access', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_plans` ADD  `access` TINYINT NOT NULL DEFAULT  '1';";
			$db->setQuery($sql);
			$db->execute();

			$sql = 'UPDATE #__osmembership_plans SET `access`=1';
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('lifetime_membership', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_plans` ADD  `lifetime_membership` TINYINT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('expired_date', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_plans` ADD  `expired_date` DATETIME NULL AFTER  `price` ;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('recurring_subscription', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_plans` ADD  `recurring_subscription` TINYINT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('enable_renewal', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_plans` ADD  `enable_renewal` TINYINT NOT NULL DEFAULT  '1';";
			$db->setQuery($sql);
			$db->execute();

			$sql = 'UPDATE `#__osmembership_plans` SET `enable_renewal`=1 ';
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('trial_amount', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_plans` ADD  `trial_amount` DECIMAL( 10, 2 ) NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('trial_duration', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_plans` ADD  `trial_duration` INT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('trial_duration_unit', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_plans` ADD  `trial_duration_unit` CHAR(1) NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('number_payments', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_plans` ADD  `number_payments` INT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('subscription_complete_url', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_plans` ADD  `subscription_complete_url` TEXT NULL ;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('category_id', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_plans` ADD  `category_id` INT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('send_third_reminder', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_plans` ADD  `send_third_reminder` INT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('alias', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_plans` ADD  `alias` varchar(255) NOT NULL DEFAULT '';";
			$db->setQuery($sql);
			$db->execute();

			$sql = 'SELECT id, title FROM #__osmembership_plans';
			$db->setQuery($sql);
			$rows = $db->loadObjectList();
			if (count($rows))
			{
				foreach ($rows as $row)
				{
					$alias = JApplication::stringURLSafe($row->title);
					$sql   = 'UPDATE #__osmembership_plans SET `alias`="' . $alias . '" WHERE id=' . $row->id;
					$db->setQuery($sql);
					$db->execute();
				}
			}
		}
		if (!in_array('tax_rate', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_plans` ADD  `tax_rate` DECIMAL( 10, 2 ) NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();
			//Set tax rate for the plan from configuration
			$taxRate = (float) OSMembershipHelper::getConfigValue('tax_rate');
			if ($taxRate > 0)
			{
				$sql = 'UPDATE #__osmembership_plans SET tax_rate=' . $taxRate;
				$db->setQuery($sql);
				$db->execute();
			}
		}

		if (!in_array('notification_emails', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_plans` ADD  `notification_emails` varchar(255) NOT NULL DEFAULT '';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('paypal_email', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_plans` ADD  `paypal_email` varchar(255) NOT NULL DEFAULT '';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('terms_and_conditions_article_id', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_plans` ADD  `terms_and_conditions_article_id` INT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('payment_methods', $fields))
		{
			$sql = "ALTER TABLE `#__osmembership_plans` ADD `payment_methods` VARCHAR( 255 ) NULL;";
			$db->setQuery($sql);
			$db->execute();
		}


		if (!in_array('number_group_members', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_plans` ADD  `number_group_members` INT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();

			$sql = 'SELECT id, `params` FROM #__osmembership_plans';
			$db->setQuery($sql);
			$rowPlans = $db->loadObjectList();
			if (count($rowPlans))
			{
				foreach ($rowPlans as $rowPlan)
				{
					$params             = new JRegistry($rowPlan->params);
					$numberGroupMembers = (int) $params->get('max_number_group_members', 0);
					$sql                = 'UPDATE #__osmembership_plans SET number_group_members = ' . $numberGroupMembers . ' WHERE id = ' . $rowPlan->id;
					$db->setQuery($sql);
					$db->execute();
				}
			}
		}

		if (!in_array('login_redirect_menu_id', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_plans` ADD  `login_redirect_menu_id` INT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('currency', $fields))
		{
			$sql = "ALTER TABLE `#__osmembership_plans` ADD `currency` VARCHAR( 10 ) NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('currency_symbol', $fields))
		{
			$sql = "ALTER TABLE `#__osmembership_plans` ADD `currency_symbol` VARCHAR( 20 ) NULL;";
			$db->setQuery($sql);
			$db->execute();
		}
		//Change data type of short description to text, avoid support

		$sql = 'ALTER TABLE  `#__osmembership_plans` CHANGE  `short_description`  `short_description` MEDIUMTEXT  NULL DEFAULT NULL';
		$db->setQuery($sql);
		$db->execute();

		$sql = 'ALTER TABLE  `#__osmembership_fields` CHANGE  `description`  `description` MEDIUMTEXT  NULL DEFAULT NULL';
		$db->setQuery($sql);
		$db->execute();

		// Custom messages per plan
		if (!in_array('subscription_form_message', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_plans` ADD  `subscription_form_message` TEXT NULL ;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('user_email_subject', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_plans` ADD  `user_email_subject` varchar(255) NOT NULL DEFAULT '';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('user_email_body', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_plans` ADD  `user_email_body` TEXT NULL ;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('user_email_body_offline', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_plans` ADD  `user_email_body_offline` TEXT NULL ;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('subscription_approved_email_subject', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_plans` ADD  `subscription_approved_email_subject` varchar(255) NOT NULL DEFAULT '';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('subscription_approved_email_body', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_plans` ADD  `subscription_approved_email_body` TEXT NULL ;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('thanks_message', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_plans` ADD  `thanks_message` TEXT NULL ;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('thanks_message_offline', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_plans` ADD  `thanks_message_offline` TEXT NULL ;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('user_renew_email_subject', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_plans` ADD  `user_renew_email_subject` varchar(255) NOT NULL DEFAULT '';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('user_renew_email_body', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_plans` ADD  `user_renew_email_body` TEXT NULL ;";
			$db->setQuery($sql);
			$db->execute();
		}

		// Subscribers table
		$fields = array_keys($db->getTableColumns('#__osmembership_subscribers'));
		if (!in_array('payment_made', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_subscribers` ADD  `payment_made` INT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('params', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_subscribers` ADD  `params` TEXT NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('recurring_profile_id', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_subscribers` ADD  `recurring_profile_id` varchar(255) NOT NULL DEFAULT '';";
			$db->setQuery($sql);
			$db->execute();
		}

		$insertCancelRecurringMessages = false;
		if (!in_array('subscription_id', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_subscribers` ADD  `subscription_id` varchar(255) NOT NULL DEFAULT '';";
			$db->setQuery($sql);
			$db->execute();

			$insertCancelRecurringMessages = true;
		}

		if (!in_array('recurring_subscription_cancelled', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_subscribers` ADD  `recurring_subscription_cancelled` TINYINT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('renewal_count', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_subscribers` ADD  `renewal_count` INT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('from_plan_id', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_subscribers` ADD  `from_plan_id` INT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();

			$sql = 'SELECT id, from_plan_id FROM #__osmembership_upgraderules WHERE published = 1';
			$db->setQuery($sql);
			$upgradeRules = $db->loadObjectList();
			foreach($upgradeRules as $rule)
			{
				$sql = 'UPDATE #__osmembership_subscribers SET from_plan_id = '.$rule->from_plan_id.' WHERE upgrade_option_id='.$rule->id;
				$db->setQuery($sql);
				$db->execute();
			}
		}

		if (!in_array('membership_id', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_subscribers` ADD  `membership_id` INT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();

			//Update membership Id field
			$sql = 'SELECT id FROM #__osmembership_subscribers ORDER BY id';
			$db->setQuery($sql);
			$rows = $db->loadObjectList();
			if (count($rows))
			{
				$start = 1000;
				foreach ($rows as $row)
				{
					$sql = 'UPDATE #__osmembership_subscribers SET membership_id=' . $start . ' WHERE id=' . $row->id;
					$db->setQuery($sql);
					$db->execute();
					$start++;
				}
			}
		}

		if (!in_array('invoice_year', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_subscribers` ADD  `invoice_year` INT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();

			$sql = 'UPDATE #__osmembership_subscribers SET `invoice_year` = YEAR(`created_date`)';
			$db->setQuery($sql);
			$db->execute();
		}
		if (!in_array('is_profile', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_subscribers` ADD  `is_profile` TINYINT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();

			$sql = 'SELECT MIN(id) AS id FROM #__osmembership_subscribers WHERE user_id > 0 GROUP BY user_id';
			$db->setQuery($sql);
			$profileIds = $db->loadColumn();
			if (count($profileIds))
			{
				$sql = 'UPDATE #__osmembership_subscribers SET is_profile=1 WHERE id IN (' . implode(',', $profileIds) . ')';
				$db->setQuery($sql);
				$db->execute();
			}

			$sql = 'SELECT MIN(id) AS id FROM #__osmembership_subscribers WHERE user_id = 0 AND is_profile=0 GROUP BY email';
			$db->setQuery($sql);
			$profileIds = $db->loadColumn();
			if (count($profileIds))
			{
				$sql = 'UPDATE #__osmembership_subscribers SET is_profile=1 WHERE id IN (' . implode(',', $profileIds) . ')';
				$db->setQuery($sql);
				$db->execute();
			}
		}

		if (!in_array('invoice_number', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_subscribers` ADD  `invoice_number` INT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();

			//Update membership Id field
			$sql = 'SELECT id FROM #__osmembership_subscribers ORDER BY id';
			$db->setQuery($sql);
			$rows = $db->loadObjectList();
			if (count($rows))
			{
				$start = 1;
				foreach ($rows as $row)
				{
					$sql = 'UPDATE #__osmembership_subscribers SET invoice_number=' . $start . ' WHERE id=' . $row->id;
					$db->setQuery($sql);
					$db->execute();
					$start++;
				}
			}
		}


		if (!in_array('profile_id', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_subscribers` ADD  `profile_id` INT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();

			$sql = 'SELECT id, user_id, email FROM #__osmembership_subscribers WHERE is_profile=1';
			$db->setQuery($sql);
			$rowSubscribers = $db->loadObjectList();
			if (count($rowSubscribers))
			{
				foreach ($rowSubscribers as $rowSubscriber)
				{
					if ($rowSubscriber->user_id > 0)
					{
						$sql = 'UPDATE #__osmembership_subscribers SET profile_id=' . $rowSubscriber->id . ' WHERE email=' . $db->quote($rowSubscriber->email) . ' OR user_id=' . $rowSubscriber->user_id;
					}
					else
					{
						$sql = 'UPDATE #__osmembership_subscribers SET profile_id=' . $rowSubscriber->id . ' WHERE email=' . $db->quote($rowSubscriber->email);
					}
					$db->setQuery($sql);
					$db->execute();
				}
			}
		}

		if (!in_array('language', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_subscribers` ADD  `language` VARCHAR( 10 ) NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('username', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_subscribers` ADD  `username` VARCHAR( 50 ) NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('user_password', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_subscribers` ADD  `user_password` VARCHAR(255) NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('payment_processing_fee', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_subscribers` ADD  `payment_processing_fee` DECIMAL( 10, 2 ) NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('group_admin_id', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_subscribers` ADD  `group_admin_id` INT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('third_reminder_sent', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_subscribers` ADD  `third_reminder_sent` TINYINT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('first_reminder_sent_at', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_subscribers` ADD  `first_reminder_sent_at` DATETIME NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('second_reminder_sent_at', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_subscribers` ADD  `second_reminder_sent_at` DATETIME NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('third_reminder_sent_at', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_subscribers` ADD  `third_reminder_sent_at` DATETIME NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		#Payment Plugins table
		$fields = array_keys($db->getTableColumns('#__osmembership_plugins'));
		if (!in_array('support_recurring_subscription', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_plugins` ADD  `support_recurring_subscription` TINYINT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('access', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_plugins` ADD  `access` TINYINT NOT NULL DEFAULT  '1';";
			$db->setQuery($sql);
			$db->execute();

			$sql = 'UPDATE `#__osmembership_plugins` SET `access` = 1';
			$db->setQuery($sql);
			$db->execute();
		}

		$recurringSupportedPlugins = array('os_paypal', 'os_authnet');
		$sql                       = 'UPDATE #__osmembership_plugins SET support_recurring_subscription=1 WHERE name IN ("' . implode('","', $recurringSupportedPlugins) . '")';
		$db->setQuery($sql);
		$db->execute();

		$sql = 'SELECT COUNT(*) FROM #__osmembership_messages';
		$db->setQuery($sql);
		$total = $db->loadResult();

		if (!$total)
		{
			$pluginsSql = JPATH_ADMINISTRATOR . '/components/com_osmembership/sql/install.messages.sql';
			$sql        = JFile::read($pluginsSql);
			$queries    = $db->splitSql($sql);
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

		if ($insertCancelRecurringMessages)
		{
			// Insert the cancel recurring messages to database
			$sql = "INSERT INTO `#__osmembership_messages` (`message_key`, `message`) VALUES
				('recurring_subscription_cancel_message', '<p>Your subscription for the subscription <strong>[PLAN_TITLE]</strong> has just been cancelled. Your subscription won''t be renewed anymore and will be expired at <strong>[SUBSCRIPTION_END_DATE]</strong></p>\r\n<p>Regards,</p>\r\n<p>Company Name</p>'),
				('user_recurring_subscription_cancel_subject', 'Recurring subscription cancelled confirmation'),
				('user_recurring_subscription_cancel_body', '<p>Dear <strong>[FIRST_NAME] [LAST_NAME]</strong></p>\r\n<p>Your recurring subscription for plan <strong>[PLAN_TITLE]</strong> has just been cancelled. Your subscription won''t be renewed anymore and will be expired at <strong>[SUBSCRIPTION_END_DATE]</strong></p>\r\n<p>Regards,</p>\r\n<p>Company Name</p>'),
				('admin_recurring_subscription_cancel_subject', 'Recurring subscription cancelled'),
				('admin_recurring_subscription_cancel_body', '<p>Dear Administrator</p>\r\n<p>User <strong>[FIRST_NAME] [LAST_NAME]</strong> has just cancelled his recurring subscription for <strong>[PLAN_TITLE]</strong>. His subscription will be expired at <strong>[SUBSCRIPTION_END_DATE]</strong></p>\r\n<p>Regards,</p>\r\n<p>Company Name</p>\r\n<p> </p>');
				";
			$db->setQuery($sql);
			$db->execute();
		}
		//Delete some files
		if (JFolder::exists(JPATH_ROOT . '/administrator/components/com_osmembership/libraries/legacy'))
		{
			JFolder::delete(JPATH_ROOT . '/administrator/components/com_osmembership/libraries/legacy');
		}
		if (JFile::exists(JPATH_ROOT . '/administrator/components/com_osmembership/libraries/factory.php'))
		{
			JFile::delete(JPATH_ROOT . '/administrator/components/com_osmembership/libraries/factory.php');
		}

		$publishedItems = array(
			'osmembership' => array(
				'user',
				'invoice',
			),
			'system'       => array(
				'osmembershipreminder',
				'osmembershipupdatestatus'
			)
		);

		foreach ($publishedItems as $folder => $plugins)
		{
			foreach ($plugins as $plugin)
			{
				$query = "SELECT COUNT(*) FROM  #__extensions WHERE element=" . $db->Quote($plugin) . " AND folder=" . $db->Quote($folder);
				$db->setQuery($query);
				$count = $db->loadResult();
				if ($count)
				{
					$query = "UPDATE #__extensions SET enabled=1 WHERE element=" . $db->Quote($plugin) . " AND folder=" . $db->Quote($folder);
					$db->setQuery($query);
					$db->execute();
				}
			}
		}
		$sql = "CREATE TABLE IF NOT EXISTS `#__osmembership_sefurls` (
		          `id` int(11) NOT NULL AUTO_INCREMENT,
		          `md5_key` text,
		          `query` text,
		          PRIMARY KEY (`id`)
		        ) DEFAULT CHARSET=utf8;
		        ";
		$db->setQuery($sql);
		$db->execute();
		$db->truncateTable('#__osmembership_sefurls');


		if (!in_array($prefix . 'osmembership_taxes', $tables))
		{
			// Tax rules table
			$sql = "CREATE TABLE IF NOT EXISTS `#__osmembership_taxes` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `plan_id` int(11) DEFAULT NULL,
				  `country` varchar(255) DEFAULT NULL,
				  `rate` decimal(10,2) DEFAULT NULL,
				  `vies` tinyint(3) unsigned DEFAULT 0,
				  `published` tinyint(3) unsigned DEFAULT 0,
				  PRIMARY KEY (`id`)
				) DEFAULT CHARSET=utf8;
				";
			$db->setQuery($sql);
			$db->execute();

			$sql = 'SELECT id, tax_rate FROM #__osmembership_plans WHERE tax_rate > 0';
			$db->setQuery($sql);
			$taxRates = $db->loadObjectList();
			if (count($taxRates) > 0)
			{
				foreach ($taxRates as $taxRate)
				{
					$sql = "INSERT INTO #__osmembership_taxes(plan_id, country, rate, vies, published) VALUES($taxRate->id, '', $taxRate->tax_rate, 0, 1)";
					$db->setQuery($sql);
					$db->execute();
				}
			}
		}
		$fields = array_keys($db->getTableColumns('#__osmembership_taxes'));
		if (!in_array('vies', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_taxes` ADD  `vies` TINYINT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('state', $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_taxes` ADD  `state` VARCHAR(255) DEFAULT '';";
			$db->setQuery($sql);
			$db->execute();

			$sql = "UPDATE #__osmembership_taxes SET `state` = ''";
			$db->setQuery($sql);
			$db->execute();
		}

		// Create articles table
		$sql = "CREATE TABLE IF NOT EXISTS `#__osmembership_articles` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `plan_id` int(11) DEFAULT NULL,
		  `article_id` int(11) DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) DEFAULT CHARSET=utf8 ;";
		$db->setQuery($sql);
		$db->execute();

		// Try to delete the file com_osmembership.zip from tmp folder
		$tmpFolder = JFactory::getConfig()->get('tmp_path');
		if (!JFolder::exists($tmpFolder))
		{
			$tmpFolder = JPATH_ROOT . '/tmp';
		}
		if (file_exists($tmpFolder . '/com_osmembership.zip'))
		{
			JFile::delete($tmpFolder . '/com_osmembership.zip');
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

		// Migrate currency code from plugin param to configuration
		$config = OSMembershipHelper::getConfig();
		if (empty($config->currency_code))
		{
			$query = $db->getQuery(true);
			$query->select('name, params')
				->from('#__osmembership_plugins')
				->where('published = 1');
			$db->setQuery($query);
			$plugins = $db->loadObjectList('name');

			if (isset($plugins['os_paypal']))
			{
				$params       = new JRegistry($plugins['os_paypal']->params);
				$currencyCode = $params->get('paypal_currency', 'USD');
			}
			elseif (isset($plugins['os_paypal_pro']))
			{
				$params       = new JRegistry($plugins['os_paypal_pro']->params);
				$currencyCode = $params->get('paypal_pro_currency', 'USD');
			}
			elseif ($plugins['os_payflowpro'])
			{
				$params       = new JRegistry($plugins['os_payflowpro']->params);
				$currencyCode = $params->get('payflow_currency', 'USD');
			}
			else
			{
				$currencyCode = 'USD';
			}

			$query->clear();
			$query->delete('#__osmembership_configs')
				->where('config_key = "currency_code"');
			$db->setQuery($query);
			$db->execute();

			$query->clear();
			$query->insert('#__osmembership_configs')
				->columns('config_key, config_value')
				->values('"currency_code", "' . $currencyCode . '"');
			$db->setQuery($query);
			$db->execute();
		}

		if (JLanguageMultilang::isEnabled())
		{
			try
			{
				OSMembershipHelper::setupMultilingual();
			}
			catch(Exception $e)
			{
				// Do nothing
			}
		}
		
		$installType = $this->input->getString('install_type');
		if ($installType == 'install')
		{
			$msg = JText::_('The extension was successfully installed');
		}
		else
		{
			$msg = JText::_('The extension was successfully updated');
		}						
		
		//Redirecting users to dasdboard
		JFactory::getApplication()->redirect('index.php?option=com_osmembership&view=dashboard', $msg);
	}
}