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

class EventbookingModelHistory extends RADModelList
{
	/**
	 * Instantiate the model.
	 *
	 * @param array $config configuration data for the model
	 *
	 */
	public function __construct($config = array())
	{
		$config['table']         = '#__eb_registrants';
		$config['search_fields'] = array('b.title');
		$config['clear_join']    = false;
		parent::__construct($config);
		$this->state->insert('filter_event_id', 'int', 0)
			->insert('filter_order', 'cmd', 'tbl.register_date')
			->insert('filter_order_Dir', 'word', 'DESC');
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
		$fieldSuffix = EventbookingHelper::getFieldSuffix();
		$query->select('tbl.*')->select('b.title' . $fieldSuffix . ' AS title, b.event_date');

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
		$query->innerJoin('#__eb_events AS b ON tbl.event_id=b.id');

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
		$user   = JFactory::getUser();
		$state  = $this->getState();
		$config = EventbookingHelper::getConfig();

		$query->where('(tbl.published=1 OR tbl.payment_method LIKE "os_offline%")')->where(
			'(tbl.user_id =' . $user->get('id') . ' OR tbl.email="' . $user->get('email') . '")');

		if ($state->filter_event_id)
		{
			$query->where('tbl.event_id=' . $state->filter_event_id);
		}

		if (isset($config->include_group_billing_in_registrants) && !$config->include_group_billing_in_registrants)
		{
			$query->where('tbl.is_group_billing = 0 ');
		}

		if (!$config->include_group_members_in_registrants)
		{
			$query->where('tbl.group_id = 0');
		}

		return parent::buildQueryWhere($query);
	}
}