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

class EventbookingModelField extends RADModelAdmin
{
	/**
	 * Pre-process data before custom field is being saved to database
	 *
	 * @param JTable   $row
	 * @param RADInput $input
	 * @param bool     $isNew
	 */
	protected function beforeStore($row, $input, $isNew)
	{
		$input->set('depend_on_options', implode(',', $input->get('depend_on_options', array(), 'array')));
		if (in_array($row->id, $this->getRestrictedFieldIds()))
		{
			$data = $input->getData(RAD_INPUT_ALLOWRAW);
			unset($data['field_type']);
			unset($data['published']);
			unset($data['validation_rules']);
			$input->setData($data);
		}
	}

	/**
	 * Post - process, Store custom fields mapping with events.
	 *
	 * @param JTable   $row
	 * @param RADInput $input
	 * @param bool     $isNew
	 */
	protected function afterStore($row, $input, $isNew)
	{
		$config = EventbookingHelper::getConfig();
		if (!$config->custom_field_by_category)
		{
			$eventIds = $input->get('event_id', array(), 'array');
			if (count($eventIds) == 0 || $eventIds[0] == -1 || $row->name == 'first_name' || $row->name == 'email')
			{
				$row->event_id = -1;
			}
			else
			{
				$row->event_id = 1;
			}
			$row->store();

			$fieldId = $row->id;
			$db      = $this->getDbo();
			$query   = $db->getQuery(true);
			$query->clear();
			if (!$isNew)
			{
				$query->delete('#__eb_field_events')->where('field_id = ' . $fieldId);
				$db->setQuery($query);
				$db->execute();
			}

			if ($row->event_id != -1)
			{
				$query->clear();
				$query->insert('#__eb_field_events')->columns('field_id, event_id');
				for ($i = 0, $n = count($eventIds); $i < $n; $i++)
				{
					$eventId = (int) $eventIds[$i];
					if ($eventId > 0)
					{
						$query->values("$fieldId, $eventId");
					}
				}
				$db->setQuery($query);
				$db->execute();
			}
		}
		else
		{
			$categoryIds = $input->get('category_id', array(), 'array');
			if (count($categoryIds) == 0 || $categoryIds[0] == -1 || $row->name == 'first_name' || $row->name == 'email')
			{
				$row->category_id = -1;
			}
			else
			{
				$row->category_id = 1;
			}
			$row->store();

			$fieldId = $row->id;
			$db      = $this->getDbo();
			$query   = $db->getQuery(true);
			$query->clear();
			if (!$isNew)
			{
				$query->delete('#__eb_field_categories')->where('field_id = ' . $fieldId);
				$db->setQuery($query);
				$db->execute();
			}

			if ($row->category_id != -1)
			{
				$query->clear();
				$query->insert('#__eb_field_categories')->columns('field_id, category_id');
				for ($i = 0, $n = count($categoryIds); $i < $n; $i++)
				{
					$categoryId = (int) $categoryIds[$i];
					if ($categoryId > 0)
					{
						$query->values("$fieldId, $categoryId");
					}
				}
				$db->setQuery($query);
				$db->execute();
			}
		}

		// Calculate depend on options in different languages
		if (JLanguageMultilang::isEnabled())
		{
			$languages = EventbookingHelper::getLanguages();
			if (count($languages))
			{
				if ($row->depend_on_field_id > 0)
				{
					$masterField = $this->getTable();
					$masterField->load($row->depend_on_field_id);
					$masterFieldValues = explode("\r\n", $masterField->values);
					$dependOnOptions   = explode(',', $row->depend_on_options);
					$dependOnIndexes   = array();
					foreach ($dependOnOptions as $option)
					{
						$index = array_search($option, $masterFieldValues);
						if ($index !== false)
						{
							$dependOnIndexes[] = $index;
						}
					}
					foreach ($languages as $language)
					{
						$sef                             = $language->sef;
						$dependOnOptionsWithThisLanguage = array();
						$values                          = explode("\r\n", $masterField->{'values_' . $sef});
						foreach ($dependOnIndexes as $index)
						{
							if (isset($values[$index]))
							{
								$dependOnOptionsWithThisLanguage[] = $values[$index];
							}
						}
						$row->{'depend_on_options_' . $sef} = implode(',', $dependOnOptionsWithThisLanguage);
					}
					$row->store();
				}
			}
		}
	}

	/**
	 * Method to remove  fields
	 *
	 * @access    public
	 * @return    boolean    True on success
	 */
	public function delete($cid = array())
	{
		if (count($cid))
		{
			$db     = $this->getDbo();
			$query  = $db->getQuery(true);
			$config = EventbookingHelper::getConfig();
			$cids   = implode(',', $cid);
			//Delete data from field values table
			$query->delete('#__eb_field_values')->where('field_id IN (' . $cids . ')');
			$db->setQuery($query);
			$db->execute();
			$query->clear();
			if (!$config->custom_field_by_category)
			{
				$query->delete('#__eb_field_events')->where('field_id IN (' . $cids . ')');
			}
			else
			{
				$query->delete('#__eb_field_categories')->where('field_id IN (' . $cids . ')');
			}
			$db->setQuery($query);
			$db->execute();
			//Do not allow deleting core fields
			$query->clear();
			$query->delete('#__eb_fields')->where('id IN (' . $cids . ') AND is_core=0');
			$db->setQuery($query);
			$db->execute();
		}

		return true;
	}

	/**
	 * Change require status
	 *
	 * @param array $cid
	 * @param int   $state
	 *
	 * @return boolean
	 */
	public function required($cid, $state)
	{
		$cids  = implode(',', $cid);
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query->update('#__eb_fields')
			->set('required=' . $state)
			->where('id IN (' . $cids . ' )');
		$db->setQuery($query);
		$db->execute();
	}

	/**
	 * Method to change the published state of one or more records.
	 *
	 * @param array $pks   A list of the primary keys to change.
	 * @param int   $value The value of the published state.
	 *
	 * @throws Exception
	 */
	public function publish($pks, $value = 1)
	{
		$restrictedFieldIds = $this->getRestrictedFieldIds();
		$pks                = array_diff($pks, $restrictedFieldIds);
		if (count($pks))
		{
			parent::publish($pks, $value);
		}
	}

	/**
	 * Get Ids of restricted fields which cannot be changed status, ddeleted...
	 *
	 * @return array
	 */
	private function getRestrictedFieldIds()
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('id')
			->from('#__eb_fields')
			->where('name IN ("first_name", "email")');
		$db->setQuery($query);

		return $db->loadColumn();
	}
}