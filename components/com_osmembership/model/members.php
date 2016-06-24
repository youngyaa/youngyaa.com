<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

class OSMembershipModelMembers extends MPFModelList
{
	/**
	 * Instantiate the model.
	 *
	 * @param array $config configuration data for the model
	 *
	 */
	public function __construct($config = array())
	{
		$config['search_fields'] = array('tbl.first_name', 'tbl.last_name', 'tbl.email', 'b.title');
		$config['table']         = '#__osmembership_subscribers';
		$config['clear_join']    = false;
		parent::__construct($config);

		$this->state->insert('id', 'int', 0);

		$params = JFactory::getApplication()->getParams();

		$this->state->setDefault('filter_order', $params->get('sort_by', 'tbl.created_date'))
			->setDefault('filter_order_Dir', $params->get('sort_direction', 'DESC'));
	}


	/**
	 * Builds SELECT columns list for the query
	 *
	 * @param JDatabaseQuery $query
	 *
	 * @return $this
	 */
	protected function buildQueryColumns(JDatabaseQuery $query)
	{
		$fieldSuffix = OSMembershipHelper::getFieldSuffix();

		$query->select('tbl.*')
			->select('b.title' . $fieldSuffix . ' AS plan_title');

		return $this;
	}

	/**
	 * Builds JOINS clauses for the query
	 *
	 * @param JDatabaseQuery $query
	 *
	 * @return $this
	 */
	protected function buildQueryJoins(JDatabaseQuery $query)
	{
		$query->leftJoin('#__osmembership_plans AS b  ON tbl.plan_id = b.id');

		return $this;
	}

	/**
	 * Builds a WHERE clause for the query
	 *
	 * @param JDatabaseQuery $query
	 *
	 * @return $this
	 */
	protected function buildQueryWhere(JDatabaseQuery $query)
	{
		parent::buildQueryWhere($query);

		$query->where('tbl.is_profile = 1');

		if ($this->state->id)
		{
			$query->where('tbl.plan_id = ' . (int) $this->state->id);
		}

		$query->where('tbl.id IN (SELECT DISTINCT profile_id FROM #__osmembership_subscribers WHERE published = 1)');

		return $this;
	}

	/**
	 * Get profile custom fields data
	 *
	 * @return array
	 */
	public function getFieldsData()
	{
		$fieldsData = array();
		$rows       = $this->data;
		$fields     = OSMembershipHelper::getProfileFields($this->state->id, false);
		if (count($rows) && count($fields))
		{
			$db    = $this->getDbo();
			$query = $db->getQuery(true);
			$ids   = array();
			foreach ($rows as $row)
			{
				$ids[] = $row->id;
			}
			$query->select('*')
				->from('#__osmembership_field_value')
				->where('subscriber_id IN (' . implode(',', $ids) . ')');
			$db->setQuery($query);
			$fieldValues = $db->loadObjectList();
			if (count($fieldValues))
			{
				foreach ($fieldValues as $fieldValue)
				{
					$fieldsData[$fieldValue->subscriber_id][$fieldValue->field_id] = $fieldValue->field_value;
				}
			}
		}

		return $fieldsData;
	}
}