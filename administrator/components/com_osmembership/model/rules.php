<?php
/**
 * @version		1.6.2
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class OSMembershipModelRules extends OSModelList
{

	function __construct($config)
	{
		$config['main_table'] = '#__osmembership_upgraderules';
		$config['state_vars'] = array(
			'filter_order' => array('b.title', 'cmd', 1), 
			'from_plan_id' => array(0, 'int', 1), 
			'to_plan_id' => array(0, 'int', 1));
		$config['search_fields'] = array('b.title', 'c.title');
		
		parent::__construct($config);
	}

	/**
	 * Build query to get list of records to display
	 * @see OSModelList::buildQuery()
	 */
	function buildQuery()
	{
		$where = $this->_buildContentWhere();
		$orderBy = $this->_buildContentOrderBy();
		$sql = 'SELECT a.*, b.title AS from_plan_title, c.title AS to_plan_title FROM #__osmembership_upgraderules AS a ' .
			 'LEFT JOIN #__osmembership_plans AS b ' . 'ON a.from_plan_id = b.id ' . 'LEFT JOIN #__osmembership_plans AS c ' .
			 'ON a.to_plan_id = c.id ' . $where . $orderBy;
		
		return $sql;
	}

	function _buildContentWhereArray()
	{
		$state = $this->getState();
		$where = parent::_buildContentWhereArray();
		if ($state->from_plan_id)
		{
			$where[] = ' a.from_plan_id = ' . $state->from_plan_id;
		}
		if ($state->to_plan_id)
		{
			$where[] = ' a.to_plan_id = ' . $state->to_plan_id;
		}
		
		return $where;
	}
}