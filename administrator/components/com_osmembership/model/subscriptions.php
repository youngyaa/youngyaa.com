<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class OSMembershipModelSubscriptions extends MPFModelList
{
	/**
	 * Instantiate the model.
	 *
	 * @param array $config configuration data for the model
	 *
	 */
	public function __construct($config = array())
	{
		$config['table']         = '#__osmembership_subscribers';
		$config['search_fields'] = array('tbl.first_name', 'tbl.last_name', 'tbl.email', 'tbl.subscription_id', 'tbl.membership_id', 'c.username');
		$config['clear_join']    = false;
		parent::__construct($config);

		$this->state->insert('plan_id', 'int', 0)
			->insert('subscription_type', 'int', 0)
			->insert('published', 'int', -1)
			->setDefault('filter_order', 'tbl.created_date')
			->setDefault('filter_order_Dir', 'DESC');
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
		$query->select(array('tbl.*'))
			->select('b.title AS plan_title, b.lifetime_membership')
			->select('b.currency, b.currency_symbol')
			->select('c.username AS username');

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
		$query->leftJoin('#__osmembership_plans AS b ON tbl.plan_id = b.id')
			->leftJoin('#__users AS c ON tbl.user_id = c.id');

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
		parent::buildQueryWhere($query);

		$state = $this->getState();

		// Don't show group members as it is managed in a separate function
		$query->where('group_admin_id = 0');

		if ($state->plan_id)
		{
			$query->where('tbl.plan_id = ' . $state->plan_id);
		}

		if ($state->published != -1)
		{
			$query->where('tbl.published = ' . $state->published);
		}

		switch ($state->subscription_type)
		{
			case 1:
				$query->where('tbl.act = "subscribe"');
				break;
			case 2:
				$query->where('tbl.act = "renew"');
				break;
			case 3:
				$query->where('tbl.act = "upgrade"');
				break;
		}

		return $this;
	}

	/**
	 * Get statistic data
	 *
	 * @return array
	 */
	public static function getStatisticsData()
	{
		$data   = array();
		$config = JFactory::getConfig();
		$db     = JFactory::getDbo();
		$query  = $db->getQuery(true);

		$query->select('COUNT(id) AS number_subscriptions, SUM(gross_amount) AS total_amount')
			->from('#__osmembership_subscribers');

		// Today
		$date = JFactory::getDate('now', $config->get('offset'));
		$date->setTime(0, 0, 0);
		$date->setTimezone(new DateTimeZone('UCT'));
		$fromDate = $date->toSql(true);
		$date     = JFactory::getDate('now', $config->get('offset'));
		$date->setTime(23, 59, 59);
		$date->setTimezone(new DateTimeZone('UCT'));
		$toDate = $date->toSql(true);

		$query->where('published IN (1,2)')
			->where('created_date >= ' . $db->quote($fromDate))
			->where('created_date <=' . $db->quote($toDate));
		$db->setQuery($query);
		$row = $db->loadObject();

		$data['today'] = array(
			'number_subscriptions' => (int) $row->number_subscriptions,
			'total_amount'         => floatval($row->total_amount)
		);

		// Yesterday
		$date = JFactory::getDate('now', $config->get('offset'));
		$date->modify('-1 day');
		$date->setTime(0, 0, 0);
		$date->setTimezone(new DateTimeZone('UCT'));
		$fromDate = $date->toSql(true);
		$date     = JFactory::getDate('now', $config->get('offset'));
		$date->modify('-1 day');
		$date->setTime(23, 59, 59);
		$date->setTimezone(new DateTimeZone('UCT'));
		$toDate = $date->toSql(true);

		$query->clear('where');
		$query->where('published IN (1,2)')
			->where('created_date >= ' . $db->quote($fromDate))
			->where('created_date <=' . $db->quote($toDate));
		$db->setQuery($query);
		$row = $db->loadObject();

		$data['yesterday'] = array(
			'number_subscriptions' => (int) $row->number_subscriptions,
			'total_amount'         => floatval($row->total_amount)
		);

		// This week
		$date   = JFactory::getDate('now', $config->get('offset'));
		$monday = clone $date->modify(('Sunday' == $date->format('l')) ? 'Monday last week' : 'Monday this week');
		$monday->setTime(0, 0, 0);
		$monday->setTimezone(new DateTimeZone('UCT'));
		$fromDate = $monday->toSql(true);
		$sunday   = clone $date->modify('Sunday this week');
		$sunday->setTime(23, 59, 59);
		$sunday->setTimezone(new DateTimeZone('UCT'));
		$toDate = $sunday->toSql(true);

		$query->clear('where');
		$query->where('published IN (1,2)')
			->where('created_date >= ' . $db->quote($fromDate))
			->where('created_date <=' . $db->quote($toDate));
		$db->setQuery($query);
		$row = $db->loadObject();

		$data['this_week'] = array(
			'number_subscriptions' => (int) $row->number_subscriptions,
			'total_amount'         => floatval($row->total_amount)
		);

		// Last week, re-use data from this week
		$monday->modify('-7 day');
		$sunday->modify('-7 day');
		$fromDate = $monday->toSql(true);
		$toDate   = $sunday->toSql(true);

		$query->clear('where');
		$query->where('published IN (1,2)')
			->where('created_date >= ' . $db->quote($fromDate))
			->where('created_date <=' . $db->quote($toDate));
		$db->setQuery($query);
		$row = $db->loadObject();

		$data['last_week'] = array(
			'number_subscriptions' => (int) $row->number_subscriptions,
			'total_amount'         => floatval($row->total_amount)
		);

		// This month
		$date = JFactory::getDate('now', $config->get('offset'));
		$date->setDate($date->year, $date->month, 1);
		$date->setTime(0, 0, 0);
		$date->setTimezone(new DateTimeZone('UCT'));
		$fromDate = $date->toSql(true);
		$date     = JFactory::getDate('now', $config->get('offset'));
		$date->setDate($date->year, $date->month, $date->daysinmonth);
		$date->setTime(23, 59, 59);
		$date->setTimezone(new DateTimeZone('UCT'));
		$toDate = $date->toSql(true);

		$query->clear('where');
		$query->where('published IN (1,2)')
			->where('created_date >= ' . $db->quote($fromDate))
			->where('created_date <=' . $db->quote($toDate));
		$db->setQuery($query);
		$row = $db->loadObject();

		$data['this_month'] = array(
			'number_subscriptions' => (int) $row->number_subscriptions,
			'total_amount'         => floatval($row->total_amount)
		);

		// Last month
		$date = JFactory::getDate('first day of last month', $config->get('offset'));
		$date->setTime(0, 0, 0);
		$date->setTimezone(new DateTimeZone('UCT'));
		$fromDate = $date->toSql(true);
		$date     = JFactory::getDate('last day of last month', $config->get('offset'));
		$date->setTime(23, 59, 59);
		$date->setTimezone(new DateTimeZone('UCT'));
		$toDate = $date->toSql(true);

		$query->clear('where');
		$query->where('published IN (1,2)')
			->where('created_date >= ' . $db->quote($fromDate))
			->where('created_date <=' . $db->quote($toDate));
		$db->setQuery($query);
		$row = $db->loadObject();

		$data['last_month'] = array(
			'number_subscriptions' => (int) $row->number_subscriptions,
			'total_amount'         => floatval($row->total_amount)
		);

		// This year
		$date = JFactory::getDate('now', $config->get('offset'));
		$date->setDate($date->year, 1, 1);
		$date->setTime(0, 0, 0);
		$date->setTimezone(new DateTimeZone('UCT'));
		$fromDate = $date->toSql(true);
		$date     = JFactory::getDate('now', $config->get('offset'));
		$date->setDate($date->year, 12, 31);
		$date->setTime(23, 59, 59);
		$date->setTimezone(new DateTimeZone('UCT'));
		$toDate = $date->toSql(true);

		$query->clear('where');
		$query->where('published IN (1,2)')
			->where('created_date >= ' . $db->quote($fromDate))
			->where('created_date <=' . $db->quote($toDate));
		$db->setQuery($query);
		$row = $db->loadObject();

		$data['this_year'] = array(
			'number_subscriptions' => (int) $row->number_subscriptions,
			'total_amount'         => floatval($row->total_amount)
		);

		// Last year
		$date = JFactory::getDate('now', $config->get('offset'));
		$date->setDate($date->year - 1, 1, 1);
		$date->setTime(0, 0, 0);
		$date->setTimezone(new DateTimeZone('UCT'));
		$fromDate = $date->toSql(true);
		$date     = JFactory::getDate('now', $config->get('offset'));
		$date->setDate($date->year - 1, 12, 31);
		$date->setTime(23, 59, 59);
		$date->setTimezone(new DateTimeZone('UCT'));
		$toDate = $date->toSql(true);

		$query->clear('where');
		$query->where('published IN (1,2)')
			->where('created_date >= ' . $db->quote($fromDate))
			->where('created_date <=' . $db->quote($toDate));
		$db->setQuery($query);
		$row = $db->loadObject();

		$data['last_year'] = array(
			'number_subscriptions' => (int) $row->number_subscriptions,
			'total_amount'         => floatval($row->total_amount)
		);

		// Total subscription
		$query->clear();
		$query->select('COUNT(*) AS number_subscriptions, SUM(gross_amount) AS total_amount')
			->from('#__osmembership_subscribers')
			->where('(published IN (1,2) OR (published = 0 AND payment_method LIKE "os_offline%"))');
		$db->setQuery($query);
		$row = $db->loadObject();

		$data['total_subscriptions'] = array(
			'number_subscriptions' => (int) $row->number_subscriptions,
			'total_amount'         => floatval($row->total_amount)
		);

		// Active subscriptions
		$query->clear();
		$query->select('COUNT(*) AS number_subscriptions, SUM(gross_amount) AS total_amount')
			->from('#__osmembership_subscribers')
			->where('published = 1');
		$db->setQuery($query);
		$row = $db->loadObject();

		$data['active_subscriptions'] = array(
			'number_subscriptions' => (int) $row->number_subscriptions,
			'total_amount'         => floatval($row->total_amount)
		);

		// Active subscribers
		$query->clear();
		$query->select('DISTINCT profile_id')
			->from('#__osmembership_subscribers')
			->where('published = 1');
		$db->setQuery($query);
		$data['active_subscribers'] = array(
			'number_subscriptions' => count($db->loadColumn()),
			'total_amount'         => 0
		);

		return $data;
	}
}