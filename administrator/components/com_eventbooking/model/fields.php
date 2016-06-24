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

class EventbookingModelFields extends RADModelList
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
			->insert('filter_event_id', 'int', 0)
			->insert('filter_show_core_fields', 'int', 0);
	}

	/**
	 * Builds a WHERE clause for the query
	 */
	protected function buildQueryWhere(JDatabaseQuery $query)
	{
		$state = $this->state;
		if ($state->filter_category_id)
		{
			$query->where('(tbl.category_id = -1 OR tbl.id IN (SELECT field_id FROM #__eb_field_categories WHERE category_id=' . $state->filter_category_id . '))');
		}
		if ($state->filter_event_id)
		{
			$query->where('(tbl.event_id = -1 OR tbl.id IN (SELECT field_id FROM #__eb_field_events WHERE event_id=' . $state->filter_event_id . '))');
		}
		if ($state->filter_show_core_fields == 2)
		{
			$query->where('tbl.is_core = 0');
		}

		return parent::buildQueryWhere($query);
	}
}