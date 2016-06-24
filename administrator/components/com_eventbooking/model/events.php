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

class EventbookingModelEvents extends RADModelList
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

		$this->state->insert('filter_category_id', 'int', 0)
			->insert('filter_location_id', 'int', 0)
			->insert('filter_past_events', 'int', 0)
			->insert('filter_order_Dir', 'word', 'DESC');
	}

	/**
	 * Method to get events data
	 *
	 * @access public
	 * @return array
	 */
	function getData()
	{
		$rows  = parent::getData();
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('a.name FROM #__eb_categories AS a')
			->innerJoin('#__eb_event_categories AS b ON a.id = b.category_id')
			->order('a.ordering');
		for ($i = 0, $n = count($rows); $i < $n; $i++)
		{
			$row = $rows[$i];
			$query->where('event_id=' . $row->id);
			$db->setQuery($query);
			$row->category_name = implode(' | ', $db->loadColumn());
			$query->clear('where');
		}

		return $rows;
	}

	/**
	 * Builds SELECT columns list for the query
	 */
	protected function buildQueryColumns(JDatabaseQuery $query)
	{
		$query->select('tbl.*,  SUM(rgt.number_registrants) AS total_registrants');

		return $this;
	}

	/**
	 * Builds LEFT JOINS clauses for the query
	 */
	protected function buildQueryJoins(JDatabaseQuery $query)
	{
		$query->leftJoin('#__eb_registrants AS rgt ON (tbl.id = rgt.event_id AND rgt.group_id = 0 AND (rgt.published=1 OR (rgt.payment_method LIKE "os_offline%" AND rgt.published NOT IN (2,3))))');

		return $this;
	}

	/**
	 * Build where clase of the query
	 *
	 * @see RADModelList::buildQueryWhere()
	 */
	protected function buildQueryWhere(JDatabaseQuery $query)
	{
		$app = JFactory::getApplication();
		if ($this->state->filter_category_id)
		{
			$query->where('tbl.id IN (SELECT event_id FROM #__eb_event_categories WHERE category_id=' . $this->state->filter_category_id . ')');
		}
		if ($this->state->filter_location_id)
		{
			$query->where('tbl.location_id=' . $this->state->filter_location_id);
		}
		if ($this->state->filter_past_events == 0 && $app->isAdmin())
		{
			$query->where('DATE(tbl.event_date) >= CURDATE()');
		}

		if ($app->isSite())
		{
			$query->where('tbl.created_by=' . (int) JFactory::getUser()->id);
		}

		return parent::buildQueryWhere($query);
	}

	protected function buildQueryGroup(JDatabaseQuery $query)
	{
		$query->group('tbl.id');

		return $this;
	}
}