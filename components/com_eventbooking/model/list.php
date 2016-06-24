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

class EventbookingModelList extends RADModelList
{

	/**
	 * Instantiate the model.
	 *
	 * @param array $config configuration data for the model
	 *
	 */
	public function __construct($config = array())
	{
		$config['table'] = '#__eb_events';

		parent::__construct($config);

		$this->state->insert('id', 'int', 0);

		$ebConfig   = EventbookingHelper::getConfig();
		$listLength = (int) $ebConfig->number_events;

		if ($listLength)
		{
			$this->state->setDefault('limit', $listLength);
		}

		if ($ebConfig->order_events == 2)
		{
			$this->state->set('filter_order', 'tbl.event_date');
		}
		else
		{
			$this->state->set('filter_order', 'tbl.ordering');
		}

		if ($ebConfig->order_direction == 'desc')
		{
			$this->state->set('filter_order_Dir', 'DESC');
		}
		else
		{
			$this->state->set('filter_order_Dir', 'ASC');
		}
	}

	/**
	 * Method to get Events data
	 *
	 * @access public
	 * @return array
	 */
	public function getData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->data))
		{
			$rows = parent::getData();
			EventbookingHelperData::calculateDiscount($rows);
			$config = EventbookingHelper::getConfig();
			if ($config->show_price_including_tax)
			{
				for ($i = 0, $n = count($rows); $i < $n; $i++)
				{
					$row                    = $rows[$i];
					$taxRate                = $row->tax_rate;
					$row->individual_price  = round($row->individual_price * (1 + $taxRate / 100), 2);
					$row->fixed_group_price = round($row->fixed_group_price * (1 + $taxRate / 100), 2);
					if ($config->show_discounted_price)
					{
						$row->discounted_price = round($row->discounted_price * (1 + $taxRate / 100), 2);
					}
				}
			}
			$this->data = $rows;
		}

		return $this->data;
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
		$currentDate = JHtml::_('date', 'Now', 'Y-m-d H:i:s');
		$fieldSuffix = EventbookingHelper::getFieldSuffix();
		$query->select('tbl.*')
			->select("DATEDIFF(tbl.early_bird_discount_date, '$currentDate') AS date_diff")
			->select("DATEDIFF('$currentDate', tbl.late_fee_date) AS late_fee_date_diff")
			->select("DATEDIFF(tbl.event_date, '$currentDate') AS number_event_dates")
			->select("TIMESTAMPDIFF(MINUTE, tbl.registration_start_date, '$currentDate') AS registration_start_minutes")
			->select("TIMESTAMPDIFF(MINUTE, tbl.cut_off_date, '$currentDate') AS cut_off_minutes")
			->select('c.name AS location_name, c.address AS location_address')
			->select('IFNULL(SUM(b.number_registrants), 0) AS total_registrants');

		if ($fieldSuffix)
		{
			EventbookingHelperDatabase::getMultilingualFields($query, array('tbl.title', 'tbl.short_description', 'tbl.description'), $fieldSuffix);
		}

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
		$query->leftJoin(
			'#__eb_registrants AS b ON (tbl.id = b.event_id AND b.group_id=0 AND (b.published = 1 OR (b.payment_method LIKE "os_offline%" AND b.published NOT IN (2,3))))')->leftJoin(
			'#__eb_locations AS c ON tbl.location_id = c.id ');

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
		$db     = $this->getDbo();
		$user   = JFactory::getUser();
		$state  = $this->getState();
		$config = EventbookingHelper::getConfig();

		if (!$user->authorise('core.admin', 'com_eventbooking'))
		{
			$query->where('tbl.published=1')->where('tbl.access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')');
		}

		$categoryId = $this->state->id ? $this->state->id : $this->state->category_id;

		if ($categoryId)
		{
			$query->where(' tbl.id IN (SELECT event_id FROM #__eb_event_categories WHERE category_id=' . $categoryId . ')');
		}

		if ($state->location_id)
		{
			$query->where('tbl.location_id=' . $state->location_id);
		}

		if ($state->search)
		{
			$search = $db->Quote('%' . $db->escape($state->search, true) . '%', false);
			$query->where("(LOWER(tbl.title) LIKE $search OR LOWER(tbl.short_description) LIKE $search OR LOWER(tbl.description) LIKE $search)");
		}
		$name = strtolower($this->getName());
		if ($name == 'archive')
		{
			$query->where('DATE(tbl.event_date) < CURDATE()');
		}
		elseif ($config->hide_past_events || ($name == 'upcomingevents'))
		{
			$currentDate = JHtml::_('date', 'Now', 'Y-m-d');
			$query->where('DATE(tbl.event_date) >= "' . $currentDate . '"');
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
		$query->group('tbl.id');

		return $this;
	}
} 