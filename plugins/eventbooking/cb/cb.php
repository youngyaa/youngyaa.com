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

class plgEventBookingCB extends JPlugin
{

	public function __construct(& $subject, $config = array())
	{
		parent::__construct($subject, $config);
		$this->canRun = file_exists(JPATH_ROOT . '/components/com_comprofiler/comprofiler.php');
	}

	/**
	 * Update CB profile data with information which registrant entered on registration form
	 *
	 * @param $row
	 *
	 * @return bool|void
	 */
	public function onAfterStoreRegistrant($row)
	{
		if (!$this->canRun)
		{
			return;
		}
		require_once JPATH_ROOT . '/components/com_eventbooking/helper/helper.php';
		$config = EventbookingHelper::getConfig();
		if ($row->user_id && $config->cb_integration == 1)
		{
			$db = JFactory::getDBO();
			$sql = 'SELECT count(*) FROM `#__comprofiler` WHERE `user_id` = ' . $db->Quote($row->user_id);
			$db->setQuery($sql);
			$count = $db->loadResult();
			if ($count)
			{
				return;
			}	

			$sql = ' SHOW FIELDS FROM #__comprofiler ';
			$db->setQuery($sql);
			$fields = $db->loadObjectList();
			$fieldList = array();
			for ($i = 0, $n = count($fields); $i < $n; $i++)
			{
				$field = $fields[$i];
				$fieldList[] = $field->Field;
			}
			
			//Get list of fields belong to table		
			$sql = 'SELECT name, field_mapping FROM #__eb_fields WHERE field_mapping != "" AND field_mapping IS NOT NULL AND is_core = 1';
			$db->setQuery($sql);
			$fields = $db->loadObjectList();
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
			
			$sql = 'SELECT a.field_mapping, b.field_value FROM #__eb_fields AS a ' . ' INNER JOIN #__eb_field_values AS b ' .
				 ' ON a.id = b.field_id ' . ' WHERE b.registrant_id=' . $row->id;
			
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
			$profile = new stdClass();
			$profile->id = $row->user_id;
			$profile->user_id = $row->user_id;
			$profile->firstname = $row->first_name;
			$profile->lastname = $row->last_name;
			$profile->avatarapproved = 1;
			$profile->confirmed = 1;
			$profile->registeripaddr = htmlspecialchars($_SERVER['REMOTE_ADDR']);
			$profile->banned = 0;
			$profile->acceptedterms = 1;
			foreach ($fieldValues as $fieldName => $value)
			{
				$profile->{$fieldName} = $value;
			}
			$db->insertObject('#__comprofiler', $profile);			

			//Update the block field in users table
			$sql = 'UPDATE  #__users SET `block` = 0 WHERE id=' . $row->user_id;
			$db->setQuery($sql);
			$db->execute();
			return true;
		}
	}
}	