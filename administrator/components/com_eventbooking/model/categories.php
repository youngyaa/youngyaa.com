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
defined('_JEXEC') or die();

class EventbookingModelCategories extends RADModelList
{
	/**
	 * Instantiate the model.
	 *
	 * @param array $config configuration data for the model
	 *
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->state->insert('filter_parent', 'int', 0);
	}

	/**
	 * Method to get categories data
	 *
	 * @access public
	 * @return array
	 */
	public function getData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->data))
		{
			$parent = $this->state->filter_parent;
			$db     = $this->getDbo();
			$query  = $db->getQuery(true);
			$this->buildQueryColumns($query)
				->buildQueryFrom($query)
				->buildQueryJoins($query)
				->buildQueryWhere($query)
				->buildQueryGroup($query)
				->buildQueryOrder($query);
			$db->setQuery($query);
			$rows     = $db->loadObjectList();
			$children = array();
			// first pass - collect children
			if (count($rows))
			{
				foreach ($rows as $v)
				{
					$pt   = $v->parent;
					$list = @$children[$pt] ? $children[$pt] : array();
					array_push($list, $v);
					$children[$pt] = $list;
				}
			}
			$list  = JHtml::_('menu.treerecurse', $parent, '', array(), $children, 9999);
			$total = count($list);
			jimport('joomla.html.pagination');
			$this->pagination = new JPagination($total, $this->state->limitstart, $this->state->limit);
			// slice out elements based on limits
			$list       = array_slice($list, $this->pagination->limitstart, $this->pagination->limit);
			$this->data = $list;
		}

		return $this->data;
	}

	/**
	 * Builds SELECT columns list for the query
	 */
	protected function buildQueryColumns(JDatabaseQuery $query)
	{
		$query->select('tbl.*, tbl.parent AS parent_id, tbl.name AS title, COUNT(ec.id) AS total_events');

		return $this;
	}

	/**
	 * Builds LEFT JOINS clauses for the query
	 */
	protected function buildQueryJoins(JDatabaseQuery $query)
	{
		$query->leftJoin('#__eb_event_categories AS ec ON tbl.id = ec.category_id');

		return $this;
	}

	/**
	 * Builds a GROUP BY clause for the query
	 */
	protected function buildQueryGroup(JDatabaseQuery $query)
	{
		$query->group('tbl.id');

		return $this;
	}
}