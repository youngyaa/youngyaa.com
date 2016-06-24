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

class EventbookingViewRegistrantlistHtml extends RADViewHtml
{

	public function display()
	{
		if (!EventbookingHelper::canViewRegistrantList())
		{
			return;
		}
		$config  = EventbookingHelper::getConfig();
		$db      = JFactory::getDbo();
		$query   = $db->getQuery(true);
		$eventId = $this->input->getInt('id', 0);
		if ($eventId)
		{
			// Get list of registration records
			$query->select('*')
				->from('#__eb_registrants AS tbl')
				->where('tbl.event_id=' . $eventId)
				->where('(tbl.published =1 OR tbl.payment_method LIKE "os_offline%")')
				->where('tbl.published NOT IN (2,3)');
			if (isset($config->include_group_billing_in_registrants) && !$config->include_group_billing_in_registrants)
			{
				$query->where('tbl.is_group_billing = 0 ');
			}
			if (!$config->include_group_members_in_registrants)
			{
				$query->where('tbl.group_id = 0');
			}
			$query->order('register_date DESC');
			$db->setQuery($query);
			$rows = $db->loadObjectList();

			// Check to see whether we need to display custom fields data for this event
			$query->clear();
			$query->select('custom_field_ids')
				->from('#__eb_events')
				->where('id = ' . $eventId);
			$db->setQuery($query);
			$customFieldIds = $db->loadResult();
			$customFieldIds = trim($customFieldIds);
			if (!$customFieldIds)
			{
				$customFieldIds = trim($config->registrant_list_custom_field_ids);
			}
			if ($customFieldIds)
			{
				$fields      = explode(',', $customFieldIds);
				$fieldTitles = array();
				$fieldValues = array();
				$fieldSuffix = EventbookingHelper::getFieldSuffix();
				$query->clear();
				$query->select('id, name,title' . $fieldSuffix . ' AS title, is_core')
					->from('#__eb_fields')
					->where('id IN (' . $customFieldIds . ')');
				$rowFields = $db->loadObjectList();
				foreach ($rowFields as $rowField)
				{
					$fieldTitles[$rowField->id] = $rowField->title;
				}

				// Getting values for custom fields
				$registrantIds = array();
				foreach ($rows as $row)
				{
					$registrantIds[] = $row->id;
					foreach ($rowFields as $rowField)
					{
						if ($rowField->is_core)
						{
							$fieldValues[$row->id][$rowField->id] = $row->{$rowField->name};
						}
					}

				}

				$query->clear();
				$query->select('registrant_id, field_id, field_value')
					->from('#__eb_field_values')
					->where('registrant_id IN (' . implode(',', $registrantIds) . ')');
				$db->setQuery($query);
				$rowFieldValues = $db->loadObjectList();
				foreach ($rowFieldValues as $rowFieldValue)
				{
					$fieldValue = $rowFieldValue->field_value;
					if (is_string($fieldValue) && is_array(json_decode($fieldValue)))
					{
						$fieldValue = implode(', ', json_decode($fieldValue));
					}
					$fieldValues[$rowFieldValue->registrant_id][$rowFieldValue->field_id] = $fieldValue;
				}

				$this->fieldTitles  = $fieldTitles;
				$this->fieldValues  = $fieldValues;
				$this->fields       = $fields;
				$displayCustomField = true;
			}
			else
			{
				$displayCustomField = false;
			}
			$this->items              = $rows;
			$this->config             = $config;
			$this->displayCustomField = $displayCustomField;
			$this->bootstrapHelper    = new EventbookingHelperBootstrap($config->twitter_bootstrap_version);

			parent::display();
		}
		else
		{
			return;
		}
	}
}