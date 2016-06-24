<?php
/**
 * @package     MPF
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2015 Ossolution Team, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die();

/**
 * Model class for handling lists of items.
 *
 * @package     MPF
 * @subpackage  Model
 * @since       2.0
 */
class MPFModelList extends MPFModel
{
	/**
	 * The query object of the model
	 *
	 * @var JDatabaseQuery
	 */
	protected $query;
	/**
	 * List total
	 *
	 * @var integer
	 */
	protected $total;

	/**
	 * Model list data
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * Pagination object
	 *
	 * @var JPagination
	 */
	protected $pagination;

	/**
	 * Name of state field name, usually be tbl.state or tbl.published
	 *
	 * @var string
	 */
	protected $stateField;

	/**
	 * List of fields which will be used for searching data from database table
	 *
	 * @var array
	 */
	protected $searchFields = array();

	/**
	 * Remember model states, always set to true for model list
	 * @var boolean
	 */
	public $rememberStates = true;

	/**
	 * Clear join clause for getTotal method
	 *
	 * @var bool
	 */
	protected $clearJoin = true;

	/**
	 * Instantiate the model.
	 *
	 * @param array $config configuration data for the model
	 *
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->query = $this->db->getQuery(true);

		$fields = array_keys($this->db->getTableColumns($this->table));

		if (in_array('ordering', $fields))
		{
			$defaultOrdering = 'tbl.ordering';
		}
		else
		{
			$defaultOrdering = 'tbl.id';
		}
		if (in_array('published', $fields))
		{
			$this->stateField = 'tbl.published';
		}
		else
		{
			$this->stateField = 'tbl.state';
		}
		$this->state->insert('limit', 'int', JFactory::getConfig()->get('list_limit'))
			->insert('limitstart', 'int', 0)
			->insert('filter_order', 'cmd', $defaultOrdering)
			->insert('filter_order_Dir', 'word', 'asc')
			->insert('filter_search', 'string')
			->insert('filter_state', 'string')
			->insert('filter_access', 'int', 0)
			->insert('filter_language', 'string');

		if (isset($config['search_fields']))
		{
			$this->searchFields = (array) $config['search_fields'];
		}
		else
		{
			// Build the search field array automatically, basically, we should search based on name, title, description if these fields are available			
			if (in_array('name', $fields))
			{
				$this->searchFields[] = 'tbl.name';
			}
			if (in_array('title', $fields))
			{
				$this->searchFields[] = 'tbl.title';
			}
			if (in_array('alias', $fields))
			{
				$this->searchFields[] = 'tbl.alias';
			}
		}

		if (isset($config['clear_join']))
		{
			$this->clearJoin = $config['clear_join'];
		}
	}

	/**
	 * Get a list of items
	 *
	 * @return array
	 */
	public function getData()
	{
		if (empty($this->data))
		{
			$db    = $this->getDbo();
			$query = $this->query;

			$this->buildQueryColumns($query)
				->buildQueryFrom($query)
				->buildQueryJoins($query)
				->buildQueryWhere($query)
				->buildQueryGroup($query)
				->buildQueryHaving($query)
				->buildQueryOrder($query);

			// Adjust the limitStart state property
			$limit = $this->state->limit;
			if ($limit)
			{
				$offset = $this->state->limitstart;
				$total  = $this->getTotal();

				//If the offset is higher than the total recalculate the offset
				if ($offset !== 0 && $total !== 0)
				{
					if ($offset >= $total)
					{
						$offset                  = floor(($total - 1) / $limit) * $limit;
						$this->state->limitstart = $offset;
					}
				}
			}

			$db->setQuery($query, $this->state->limitstart, $this->state->limit);
			$this->data = $db->loadObjectList();

			// Store the query so that it can be used in getTotal method if needed
			$this->query = $query;
		}

		return $this->data;
	}

	/**
	 * Get total record. Child class should override this method if needed
	 *
	 * @return integer Number of records
	 *
	 */
	public function getTotal()
	{
		if (empty($this->total))
		{
			$db    = $this->getDbo();
			$query = clone $this->query;
			$query->clear('select')
				->clear('group')
				->clear('having')
				->clear('order')
				->select('COUNT(*)');

			// Limit, offset is only available in Joomla 3.x
			if (version_compare(JVERSION, '3.0', 'ge'))
			{
				$query->clear('limit');
			}

			// Clear join clause if needed
			if ($this->clearJoin)
			{
				$query->clear('join');
			}

			$db->setQuery($query);
			$this->total = (int) $db->loadResult();
		}

		return $this->total;
	}

	/**
	 * Get pagination object
	 *
	 * @return JPagination
	 */
	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->pagination))
		{
			jimport('joomla.html.pagination');
			$this->pagination = new JPagination($this->getTotal(), $this->state->limitstart, $this->state->limit);
		}

		return $this->pagination;
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
		$query->select(array('tbl.*'));

		return $this;
	}

	/**
	 * Builds FROM tables list for the query
	 *
	 * @param JDatabaseQuery $query
	 *
	 * @return $this
	 */
	protected function buildQueryFrom(JDatabaseQuery $query)
	{
		$query->from($this->table . ' AS tbl');

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
		$user  = JFactory::getUser();
		$db    = $this->getDbo();
		$state = $this->state;
		if ($state->filter_state == 'P')
		{
			$query->where($this->stateField . ' = 1');
		}
		elseif ($state->filter_state == 'U')
		{
			$query->where($this->stateField . ' = 0');
		}
		if ($state->filter_access)
		{
			$query->where('tbl.access = ' . (int) $state->filter_access);
			if (!$user->authorise('core.admin'))
			{
				$query->where('tbl.access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')');
			}
		}
		if ($state->filter_search)
		{
			//Remove blank space from searching
			$state->filter_search = trim($state->filter_search);
			if (stripos($state->search, 'id:') === 0)
			{
				$query->where('tbl.id = ' . (int) substr($state->filter_search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($state->filter_search, true) . '%', false);
				if (is_array($this->searchFields))
				{
					$whereOr = array();
					foreach ($this->searchFields as $searchField)
					{
						$whereOr[] = " LOWER($searchField) LIKE " . $search;
					}
					$query->where('(' . implode(' OR ', $whereOr) . ') ');
				}
			}
		}

		if ($state->filter_language && $state->filter_language != '*')
		{
			$query->where('tbl.language IN (' . $db->Quote($state->filter_language) . ',' . $db->Quote('*') . ', "")');
		}

		return $this;
	}

	/**
	 * Builds a GROUP BY clause for the query
	 *
	 * @param JDatabaseQuery $query
	 *
	 * @return $this
	 */
	protected function buildQueryGroup(JDatabaseQuery $query)
	{
		return $this;
	}

	/**
	 * Builds a HAVING clause for the query
	 *
	 * @param JDatabaseQuery $query
	 *
	 * @return $this
	 */
	protected function buildQueryHaving(JDatabaseQuery $query)
	{
		return $this;
	}

	/**
	 * Builds a generic ORDER BY clasue based on the model's state
	 *
	 * @param JDatabaseQuery $query
	 *
	 * @return $this
	 */
	protected function buildQueryOrder(JDatabaseQuery $query)
	{
		$sort      = $this->state->filter_order;
		$direction = strtoupper($this->state->filter_order_Dir);
		if ($sort)
		{
			$query->order($sort . ' ' . $direction);
		}

		return $this;
	}
}
