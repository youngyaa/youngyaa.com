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

class EventbookingModelRegistrants extends RADModelList
{

	/**
	 * Instantiate the model.
	 *
	 * @param array $config configuration data for the model
	 *
	 */
	public function __construct($config = array())
	{
		$config['search_fields'] = array('tbl.first_name', 'tbl.last_name', 'tbl.email', 'tbl.transaction_id');

		parent::__construct($config);

		$this->state->insert('filter_event_id', 'int', 0)
			->insert('filter_published', 'int', -1)
			->insert('filter_order_Dir', 'word', 'DESC');
	}

	/**
	 * Method to get registrants data
	 *
	 * @access public
	 * @return array
	 */
	public function getData()
	{
		$rows = parent::getData();
		if (count($rows))
		{
			$db    = $this->getDbo();
			$query = $db->getQuery(true);

			// Get group billing records
			$billingIds = array();
			foreach ($rows as $row)
			{
				if ($row->group_id)
				{
					$billingIds[] = $row->group_id;
				}
			}

			if (count($billingIds))
			{
				$query->select('id, first_name, last_name')
					->from('#__eb_registrants')
					->where('id IN (' . implode(',', $billingIds) . ')');
				$db->setQuery($query);
				$billingRecords = $db->loadObjectList('id');
				foreach ($rows as $row)
				{
					if ($row->group_id > 0)
					{
						$billingRecord   = $billingRecords[$row->group_id];
						$row->group_name = $billingRecord->first_name . ' ' . $billingRecord->last_name;
					}
				}
			}
		}

		return $rows;
	}

	/**
	 * Builds SELECT columns list for the query
	 */
	protected function buildQueryColumns(JDatabaseQuery $query)
	{
		$query->select('tbl.*, ev.title, ev.event_date, cp.code AS coupon_code');

		return $this;
	}

	/**
	 * Builds LEFT JOINS clauses for the query
	 */
	protected function buildQueryJoins(JDatabaseQuery $query)
	{
		$query->leftJoin('#__eb_events AS ev ON tbl.event_id = ev.id')->leftJoin('#__eb_coupons AS cp ON tbl.coupon_id = cp.id');

		return $this;
	}

	/**
	 * Build where clase of the query
	 *
	 * @see RADModelList::buildQueryWhere()
	 */
	protected function buildQueryWhere(JDatabaseQuery $query)
	{
		$config = EventbookingHelper::getConfig();

		if ($this->state->filter_published != -1)
		{
			$query->where(' tbl.published = ' . $this->state->filter_published);
		}
		if ($this->state->filter_event_id)
		{
			$query->where(' tbl.event_id = ' . $this->state->filter_event_id);
		}

		if (!$config->show_pending_registrants)
		{
			$query->where('(tbl.published >= 1 OR tbl.payment_method LIKE "os_offline%")');
		}

		if (isset($config->include_group_billing_in_registrants) && !$config->include_group_billing_in_registrants)
		{
			$query->where(' tbl.is_group_billing = 0 ');
		}

		if (!$config->include_group_members_in_registrants)
		{
			$query->where(' tbl.group_id = 0 ');
		}

		if (JFactory::getApplication()->isSite() && $config->only_show_registrants_of_event_owner)
		{
			$query->where('tbl.event_id IN (SELECT id FROM #__eb_events WHERE created_by =' . JFactory::getUser()->id . ')');
		}

		return parent::buildQueryWhere($query);
	}
}