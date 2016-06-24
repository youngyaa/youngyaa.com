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

class OSMembershipModelGroupmembers extends MPFModelList
{
	/**
	 * Instantiate the model.
	 *
	 * @param array $config configuration data for the model
	 *
	 */
	public function __construct($config = array())
	{
		$config['table']         = '#__osmembership_subscribers';
		$config['search_fields'] = array('tbl.first_name', 'tbl.last_name', 'tbl.email');

		parent::__construct($config);

		$this->state->insert('filter_plan_id', 'int', 0)
			->insert('filter_group_admin_id', 0)
			->insert('filter_published', 0)
			->setDefault('filter_order', 'tbl.created_date')
			->setDefault('filter_order_Dir', 'DESC');

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
			->select('b.title' . $fieldSuffix . ' AS plan_title, b.lifetime_membership');

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

		$query->where('tbl.group_admin_id > 0');

		if ($this->state->filter_plan_id)
		{
			$query->where('tbl.plan_id = ' . $this->state->filter_plan_id);
		}

		if ($this->state->filter_group_admin_id)
		{
			$query->where('tbl.group_admin_id = ' . $this->state->filter_group_admin_id);
		}

		if ($this->state->filter_published)
		{
			$query->where('tbl.published = ' . $this->state->filter_published);
		}

		return $this;
	}
}