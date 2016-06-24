<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     OSMembership
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * OS Membership Component Field Model
 *
 * @package        Joomla
 * @subpackage     Membership Pro
 */
class OSMembershipModelField extends MPFModelAdmin
{
	/**
	 * Method to store a custom field
	 *
	 * @param MPFInput $input
	 * @param array    $ignore
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function store($input, $ignore = array())
	{
		$id = $input->getInt('id');
		if ($id)
		{
			$row = $this->getTable();
			$row->load($id);
			if ($row->is_core)
			{
				$ignore = array('name', 'fee_field');
			}
		}

		$dependOnOptions = '';
		if ($input->has('depend_on_options'))
		{
			$dependOnOptions = implode(',', $input->get('depend_on_options', array(), 'array'));
		}
		$input->set('depend_on_options', $dependOnOptions);

		parent::store($input, $ignore);
	}


	/**
	 * Store custom fields mapping with plans.
	 *
	 * @param JTable   $row
	 * @param MPFInput $input
	 * @param bool     $isNew
	 */
	protected function afterStore($row, $input, $isNew)
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		$planIds = $input->get('plan_id', array(), 'array');
		if (empty($planIds) || $planIds[0] == 0 || $row->name == 'first_name' || $row->name == 'email')
		{
			$row->plan_id = 0;
		}
		else
		{
			$row->plan_id = 1;
		}

		$row->store(); // Store the plan_id field

		if (!$isNew)
		{
			$query->delete('#__osmembership_field_plan')
				->where('field_id = ' . $row->id);
			$db->setQuery($query);
			$db->execute();
		}

		if ($row->plan_id != 0)
		{
			$query->clear();			
			for ($i = 0, $n = count($planIds); $i < $n; $i++)
			{
				$planId = $planIds[$i];
				if ($planId > 0)
				{
					$query->values("$row->id, $planId");
				}
			}

			$query->insert('#__osmembership_field_plan')
				->columns('field_id, plan_id');
			$db->setQuery($query);

			$db->execute();
		}

		// Calculate depend on options in different languages
		if (JLanguageMultilang::isEnabled())
		{
			$languages = OSMembershipHelper::getLanguages();
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
	 * Method to delete one or more records.
	 *
	 * @param array $cid
	 *
	 * @throws Exception
	 */
	public function delete($cid = array())
	{
		if (count($cid))
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$cids  = implode(',', $cid);
			$query->delete('#__osmembership_fields')
				->where('is_core = 0')
				->where('id IN (' . $cids . ')');
			$db->setQuery($query);
			$db->execute();

			$query->clear();
			$query->delete('#__osmembership_field_value')
				->where('field_id IN (' . $cids . ')');
			$db->setQuery($query);
			$db->execute();
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
			->from('#__osmembership_fields')
			->where('name IN ("first_name", "email")');
		$db->setQuery($query);

		return $db->loadColumn();
	}
}