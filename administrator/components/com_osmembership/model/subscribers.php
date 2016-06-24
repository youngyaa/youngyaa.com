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
defined('_JEXEC') or die();

class OSMembershipModelSubscribers extends MPFModelList
{
	/**
	 * Instantiate the model.
	 *
	 * @param array $config configuration data for the model
	 *
	 */
	public function __construct($config)
	{
		$config['clear_join']    = false;
		$config['search_fields'] = array('tbl.first_name', 'tbl.last_name', 'tbl.email', 'tbl.membership_id', 'b.username');

		parent::__construct($config);

		$this->state->insert('plan_id', 'int', 0)
			->insert('published', 'int', -1)
			->setDefault('filter_order', 'tbl.created_date')
			->setDefault('filter_order_Dir', 'DESC');
	}

	/**
	 * Get list of profile records
	 *
	 * @return array
	 */
	public function getData()
	{
		$rows = parent::getData();
		for ($i = 0, $n = count($rows); $i < $n; $i++)
		{
			$row        = $rows[$i];
			$row->plans = OSMembershipHelper::getSubscriptions($row->id);
		}

		return $rows;
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
		$query->select(array('tbl.*'))
			->select('b.username');

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
		$query->leftJoin('#__users AS b ON tbl.user_id = b.id');

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

		$query->where('tbl.is_profile = 1')
			->where('group_admin_id <= 0');

		return $this;
	}
}