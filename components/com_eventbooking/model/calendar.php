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

class EventbookingModelCalendar extends RADModel
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

		$this->state->insert('year', 'int', 0)
			->insert('month', 'int', 0)
			->insert('default_month', 'int', 0)
			->insert('default_year', 'int', 0)
			->insert('date', 'string', '')
			->insert('day', 'string', '')
			->insert('id', 'int', 0)
			->insert('mini_calendar', 'int', 0);
	}

	/**
	 * Get monthly events
	 *
	 * @return array|mixed
	 */
	public function getData()
	{
		$db              = $this->getDbo();
		$query           = $db->getQuery(true);
		$config          = EventbookingHelper::getConfig();
		$fieldSuffix     = EventbookingHelper::getFieldSuffix();
		$date            = JFactory::getDate('now', JFactory::getConfig()->get('offset'));
		$year            = $this->state->year ? $this->state->year : $this->state->default_year;
		$month           = $this->state->month ? $this->state->month : $this->state->default_month;
		$currentDateData = self::getCurrentDateData();

		if (!$year)
		{
			$year = $currentDateData['year'];
		}

		if (!$month)
		{
			$month = $currentDateData['month'];
		}

		$this->state->set('month', $month)
			->set('year', $year);

		// Calculate start date and end date of the given month
		$date->setDate($year, $month, 1);
		$date->setTime(0, 0, 0);
		$startDate = $date->toSql(true);

		$date->setDate($year, $month, $date->daysinmonth);
		$date->setTime(23, 59, 59);
		$endDate = $date->toSql(true);


		$query->select('a.*,title' . $fieldSuffix . ' AS title, SUM(b.number_registrants) AS total_registrants')
			->from('#__eb_events AS a')
			->leftJoin('#__eb_registrants AS b ON (a.id = b.event_id ) AND b.group_id = 0 AND (b.published=1 OR (b.payment_method LIKE "os_offline%" AND b.published NOT IN (2,3)))')
			->where('a.published = 1')
			->where('a.access in (' . implode(',', JFactory::getUser()->getAuthorisedViewLevels()) . ')')
			->group('a.id')
			->order('a.event_date ASC, a.ordering ASC');

		if ($this->state->id)
		{
			$catId = $this->state->id;
			$query->where("a.id IN (SELECT event_id FROM #__eb_event_categories WHERE category_id=$catId)");
		}
		if ($config->show_multiple_days_event_in_calendar && !$this->state->mini_calendar)
		{
			$query->where("((`event_date` BETWEEN '$startDate' AND '$endDate') OR (MONTH(event_end_date) = $month AND YEAR(event_end_date) = $year ))");
		}
		else
		{
			$query->where("`event_date` BETWEEN '$startDate' AND '$endDate'");
		}

		if ($config->hide_past_events)
		{
			$currentDate = JHtml::_('date', 'Now', 'Y-m-d H:i:s');
			$query->where('DATE(event_date) >= ' . $db->quote($currentDate));
		}
		$db->setQuery($query);
		if ($config->show_multiple_days_event_in_calendar && !$this->state->mini_calendar)
		{
			$rows      = $db->loadObjectList();
			$rowEvents = array();
			for ($i = 0, $n = count($rows); $i < $n; $i++)
			{
				$row      = $rows[$i];
				$arrDates = explode('-', $row->event_date);
				if ($arrDates[0] == $year && $arrDates[1] == $month)
				{
					$rowEvents[] = $row;
				}
				$startDateParts = explode(' ', $row->event_date);
				$startTime      = strtotime($startDateParts[0]);
				$startDateTime  = strtotime($row->event_date);
				$endDateParts   = explode(' ', $row->event_end_date);
				$endTime        = strtotime($endDateParts[0]);
				$count          = 0;
				while ($startTime < $endTime)
				{
					$count++;
					$rowNew             = clone ($row);
					$rowNew->event_date = date('Y-m-d H:i:s', $startDateTime + $count * 24 * 3600);
					$arrDates           = explode('-', $rowNew->event_date);
					if ($arrDates[0] == $year && $arrDates[1] == $month)
					{
						$rowEvents[] = $rowNew;
					}
					$startTime += 24 * 3600;
				}
			}

			return $rowEvents;
		}
		else
		{
			return $db->loadObjectList();
		}

	}

	/**
	 * Get events of the given week
	 *
	 * @return array
	 */
	public function getEventsByWeek()
	{
		$config      = EventbookingHelper::getConfig();
		$fieldSuffix = EventbookingHelper::getFieldSuffix();
		$db          = $this->getDbo();
		$query       = $db->getQuery(true);

		// get first day of week of today
		$currentDateData = self::getCurrentDateData();
		$startWeekDate   = $this->state->date;
		if ($startWeekDate)
		{
			$date = JFactory::getDate($startWeekDate, JFactory::getConfig()->get('offset'));
		}
		else
		{
			$date = JFactory::getDate($currentDateData['start_week_date'], JFactory::getConfig()->get('offset'));
			$this->state->set('date', $date->format('Y-m-d', true));
		}
		$date->setTime(0, 0, 0);
		$startDate = $date->toSql(true);
		$date->modify('+6 day');
		$endDate = $date->toSql(true);
		$query->select('a.*')
			->select('b.name AS location_name')
			->from('#__eb_events AS a')
			->leftJoin('#__eb_locations AS b ON b.id = a.location_id')
			->where('a.published = 1')
			->where("(a.event_date BETWEEN '$startDate' AND '$endDate')")
			->where('a.access IN (' . implode(',', JFactory::getUser()->getAuthorisedViewLevels()) . ')');

		if ($fieldSuffix)
		{
			EventbookingHelperDatabase::getMultilingualFields($query, array('a.title', 'a.short_description'), $fieldSuffix);
		}
		if ($config->hide_past_events)
		{
			$query->where('DATE(a.event_date) >=' . $db->quote($currentDateData['current_date']));
		}
		$query->order('a.event_date ASC, a.ordering ASC');

		$db->setQuery($query);
		$events   = $db->loadObjectList();
		$eventArr = array();
		foreach ($events as $event)
		{
			$eventArr[date('w', strtotime($event->event_date))][] = $event;
		}

		return $eventArr;
	}

	/**
	 * Get events of the given date
	 *
	 * @return mixed
	 */
	public function getEventsByDaily()
	{
		$config      = EventbookingHelper::getConfig();
		$fieldSuffix = EventbookingHelper::getFieldSuffix();
		$db          = $this->getDbo();
		$query       = $db->getQuery(true);
		$day         = $this->state->day;
		if (!$day)
		{
			$currentDateData = self::getCurrentDateData();
			$day             = $currentDateData['current_date'];
			$this->state->set('day', $day);
		}
		$startDate = $day . " 00:00:00";
		$endDate   = $day . " 23:59:59";
		$query->select('a.*')
			->select('a.title' . $fieldSuffix . ' AS title')
			->select('short_description' . $fieldSuffix . ' AS short_description')
			->select('b.name AS location_name')
			->from('#__eb_events AS a')
			->leftJoin('#__eb_locations AS b ON b.id = a.location_id')
			->where('a.published = 1')
			->where("(a.event_date BETWEEN '$startDate' AND '$endDate')")
			->where('a.access IN (' . implode(',', JFactory::getUser()->getAuthorisedViewLevels()) . ')');
		if ($config->hide_past_events)
		{
			$query->where('DATE(event_date) >=' . JHtml::_('date', 'Now', 'Y-m-d'));
		}
		$query->order('a.event_date ASC, a.ordering ASC');

		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Get data of current date
	 *
	 * @return array
	 */
	public static function getCurrentDateData($currentDate = 'now')
	{
		static $data;
		if (empty($data))
		{
			$config               = EventbookingHelper::getConfig();
			$startDay             = (int) $config->calendar_start_date;
			$data                 = array();
			$date                 = new DateTime($currentDate, new DateTimeZone(JFactory::getConfig()->get('offset')));
			$data['year']         = $date->format('Y');
			$data['month']        = $date->format('m');
			$data['current_date'] = $date->format('Y-m-d');
			if ($startDay == 0)
			{
				$date->modify('Sunday last week');
			}
			else
			{
				$date->modify(('Sunday' == $date->format('l')) ? 'Monday last week' : 'Monday this week');
			}
			$data['start_week_date'] = $date->format('Y-m-d');
			$data['end_week_date']   = $date->modify('+6 day')->format('Y-m-d');
		}

		return $data;
	}
}
