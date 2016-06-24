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

class EventbookingModelCoupons extends RADModelList
{

	/**
	 * Instantiate the model.
	 *
	 * @param array $config configuration data for the model
	 *
	 */
	public function __construct($config = array())
	{
		$config['search_fields'] = array('tbl.code');

		parent::__construct($config);

		$this->state->insert('filter_event_id', 'int', 0);
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
		if ($this->state->filter_event_id)
		{
			$query->where('(tbl.event_id = -1 OR tbl.id IN (SELECT coupon_id FROM #__eb_coupon_events WHERE event_id=' . $this->state->filter_event_id . '))');
		}

		return parent::buildQueryWhere($query);
	}
}