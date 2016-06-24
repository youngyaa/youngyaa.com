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

class OSMembershipModelFields extends MPFModelList
{

	/**
	 * Constructor, Instantiate the model.
	 *
	 * @param array $config
	 */
	public function __construct($config)
	{
		parent::__construct($config);

		$this->state->insert('show_core_field', 'int', 1)
			->insert('plan_id', 'int', 0);
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

		$state = $this->getState();

		if ($state->plan_id > 0)
		{
			$query->where('(plan_id=0 OR id IN (SELECT field_id FROM #__osmembership_field_plan WHERE plan_id=' . $state->plan_id . '))');
		}

		if ($state->show_core_field == 2)
		{
			$query->where('tbl.is_core = 0');
		}

		return $this;
	}
}