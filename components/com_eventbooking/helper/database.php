<?php

/**
 * @version            2.0.4
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2015 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */
class EventbookingHelperDatabase
{
	/**
	 * Get category data from database
	 *
	 * @param $id
	 *
	 * @return mixed
	 */
	public static function getCategory($id)
	{
		$db          = JFactory::getDbo();
		$query       = $db->getQuery(true);
		$fieldSuffix = EventbookingHelper::getFieldSuffix();
		$query->select('*')
			->from('#__eb_categories')
			->where('id=' . (int) $id);

		if ($fieldSuffix)
		{
			self::getMultilingualFields($query, array('name'), $fieldSuffix);
		}

		$db->setQuery($query);

		return $db->loadObject();
	}

	/**
	 * Get event information from database
	 *
	 * @param $id
	 *
	 * @return mixed
	 */
	public static function getEvent($id)
	{
		$db          = JFactory::getDbo();
		$query       = $db->getQuery(true);
		$fieldSuffix = EventbookingHelper::getFieldSuffix();
		$currentDate = JHtml::_('date', 'Now', 'Y-m-d H:i:s');
		$query->select('a.*, IFNULL(SUM(b.number_registrants), 0) AS total_registrants')
			->from('#__eb_events AS a')
			->select("DATEDIFF(event_date, '$currentDate') AS number_event_dates")
			->select("DATEDIFF('$currentDate', a.late_fee_date) AS late_fee_date_diff")
			->select("TIMESTAMPDIFF(MINUTE, registration_start_date, '$currentDate') AS registration_start_minutes")
			->select("TIMESTAMPDIFF(MINUTE, cut_off_date, '$currentDate') AS cut_off_minutes")
			->select("DATEDIFF(early_bird_discount_date, '$currentDate') AS date_diff")
			->leftJoin('#__eb_registrants AS b ON (a.id = b.event_id AND b.group_id=0 AND (b.published = 1 OR (b.payment_method LIKE "os_offline%" AND b.published NOT IN (2,3))))')
			->where('a.id=' . (int) $id);

		if ($fieldSuffix)
		{
			self::getMultilingualFields($query, array('a.title', 'a.short_description', 'a.description', 'a.meta_keywords', 'a.meta_description'), $fieldSuffix);
		}

		$query->group('a.id');
		$db->setQuery($query);

		return $db->loadObject();
	}

	/**
	 * Method to load location object from database
	 *
	 * @param $id
	 *
	 * @return mixed
	 */
	public static function getLocation($id)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__eb_locations')
			->where('id=' . (int) $id);
		$db->setQuery($query);

		return $db->loadObject();
	}

	/**
	 * Method to get group registration rates for an event
	 *
	 * @param $eventId
	 *
	 * @return array
	 */
	public static function getGroupRegistrationRates($eventId)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__eb_event_group_prices')
			->where('event_id=' . (int) $eventId)
			->order('id');
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Get all published categories
	 *
	 * @param string $order
	 *
	 * @return mixed
	 */
	public static function getAllCategories($order = 'title')
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id, parent, parent AS parent_id, name, name AS title')
			->from('#__eb_categories')
			->where('published=1')
			->order($order);
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Get all published events
	 *
	 * @param string $order
	 *
	 * @return mixed
	 */
	public static function getAllEvents($order = 'title')
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id, title, event_date')
			->from('#__eb_events')
			->where('published=1')
			->order($order);
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Get all published countries
	 *
	 * @param string $order
	 *
	 * @return mixed
	 */
	public static function getAllCountries($order = 'name')
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id, name')
			->from('#__eb_countries')
			->where('published')
			->order($order);
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Get all locations in the system
	 *
	 * @param string $order
	 *
	 * @return mixed
	 */
	public static function getAllLocations($order = 'name')
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id, name')
			->from('#__eb_locations')
			->where('published')
			->order($order);
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Helper method to get fields from database table in case the site is multilingual
	 *
	 * @param JDatabaseQuery $query
	 * @param array          $fields
	 * @param                $fieldSuffix
	 */
	public static function getMultilingualFields(JDatabaseQuery $query, $fields = array(), $fieldSuffix)
	{
		foreach ($fields as $field)
		{
			$alias  = $field;
			$dotPos = strpos($field, '.');
			if ($dotPos !== false)
			{
				$alias = substr($field, $dotPos + 1);
			}
			$query->select($query->quoteName($field . $fieldSuffix, $alias));
		}
	}
}