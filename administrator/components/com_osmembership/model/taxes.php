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

class OSMembershipModelTaxes extends MPFModelList
{

	/**
	 * Instantiate the model.
	 *
	 * @param array $config configuration data for the model
	 *
	 */
	public function __construct($config = array())
	{
		$config['search_fields'] = array('tbl.country');

		parent::__construct($config);

		$this->state->insert('filter_country', 'string', '')
			->insert('plan_id', 'int', 0)
			->insert('vies', 'int', -1)
			->setDefault('filter_order', 'tbl.country');
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
			->select('b.title');

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
		$query->leftJoin('#__osmembership_plans AS b ON tbl.plan_id = b.id');

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

		$db    = $this->getDbo();
		$state = $this->getState();

		if ($state->filter_country)
		{
			$query->where('tbl.country = ' . $db->quote($state->filter_country));
		}

		if ($state->plan_id > 0)
		{
			$query->where('tbl.plan_id = ' . $state->plan_id);
		}

		if ($state->vies != -1)
		{
			$query->where('tbl.vies = ' . $state->vies);
		}

		return $this;
	}

}