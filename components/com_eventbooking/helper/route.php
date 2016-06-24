<?php
/**
 * @version        	2.0.0
 * @package        	Joomla
 * @subpackage		Event Booking
 * @author  		Tuan Pham Ngoc
 * @copyright    	Copyright (C) 2010 - 2015 Ossolution Team
 * @license        	GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die();

class EventbookingHelperRoute
{

	protected static $config;

	protected static $lookup;

	protected static $events;

	/**
	 * 
	 * Function to get Event Route
	 * @param int $id
	 * @param int $catId
	 * @return string
	 */
	public static function getEventRoute($id, $catId = 0, $itemId = 0)
	{
		$needles = array('event' => array((int) $id));
		$link = 'index.php?option=com_eventbooking&view=event&id=' . $id;
		if (!$catId)
		{
			//Find the main category of this event
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('category_id')
				->from('#__eb_event_categories')
				->where('event_id=' . (int) $id)
				->where('main_category=1');
			$db->setQuery($query);
			$catId = (int) $db->loadResult();
		}
		if ($catId)
		{
			$needles['category'] = self::getCategoriesPath($catId, 'id', false);
			$needles['categories'] = $needles['category'];
			$link .= '&catid=' . $catId;
		}
		
		if ($item = self::findItem($needles, $itemId))
		{
			$link .= '&Itemid=' . $item;
		}
		return $link;
	}

	/**
	 * 
	 * Function to get Category Route
	 * @param int $id
	 * @return string
	 */
	public static function getCategoryRoute($id, $itemId = 0)
	{
		if (!$id)
		{
			$link = '';
		}
		else
		{
			//Create the link
			$link = 'index.php?option=com_eventbooking&view=category&id=' . $id;
			$catIds = self::getCategoriesPath($id, 'id', false);
			$needles = array('category' => $catIds, 'categories' => $catIds);
			if ($item = self::findItem($needles, $itemId))
				$link .= '&Itemid=' . $item;
		}
		
		return $link;
	}

	/**
	 * 
	 * Function to get View Route
	 * @param string $view (cart, checkout)
	 * @return string
	 */
	public static function getViewRoute($view, $itemId)
	{
		//Create the link
		$link = 'index.php?option=com_eventbooking&view=' . $view;
		if ($item = self::findView($view, $itemId))
		{
			$link .= '&Itemid=' . $item;
		}

		return $link;
	}

	/**
     * Get event title, used for building the router
     *
     * @param $id
     * @return mixed
     */
	public static function getEventTitle($id)
	{
		if (self::$config == null)
		{
			self::$config = EventbookingHelper::getConfig();
		}
		if (!isset(self::$events[$id]))
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$fieldSuffix = EventbookingHelper::getFieldSuffix();
			$query->select('id, event_type, parent_id,  alias'.$fieldSuffix.' AS alias')
				->from('#__eb_events')
				->where('id=' . $id);
			$db->setQuery($query);
			$event = $db->loadObject();
			if (self::$config->insert_event_id)
			{
				self::$events[$id] = $id . '-' . $event->alias;
			}
			else
			{
				self::$events[$id] = $event->alias;
			}
		}
		
		return self::$events[$id];
	}

	public static function getCategoriesPath($id, $type = 'id', $reverse = true)
	{
		static $categories;
		if (self::$config == null)
		{
			self::$config = EventbookingHelper::getConfig();
		}
		$db = JFactory::getDbo();
		if (empty($categories))
		{
			$fieldSuffix = EventbookingHelper::getFieldSuffix();
			$query = $db->getQuery(true);
			$query->select('id, alias'.$fieldSuffix.' AS alias, parent')->from('#__eb_categories');
			$db->setQuery($query);
			$categories = $db->loadObjectList('id');
		}
		$paths = array();
		if ($type == 'id' || self::$config->insert_category == 0)
		{
			do
			{
				$paths[] = $categories[$id]->{$type};
				$id = $categories[$id]->parent;
			}
			while ($id != 0);
			if ($reverse)
			{
				$paths = array_reverse($paths);
			}
		}
		else
		{
			$paths[] = $categories[$id]->{$type};
		}
		
		return $paths;
	}

	/**
     * Find item id variable corresponding to the view
     *
     * @param $view
     * @return int
     */
	public static function findView($view, $itemId)
	{
		$needles = array($view => array(0));
		if ($item = self::findItem($needles, $itemId))
		{
			return $item;
		}
		else
		{
			return 0;
		}
	}

	/**
	 * 
	 * Function to find Itemid
	 * @param string $needles
	 * @return int
	 */
	public static function findItem($needles = null, $itemId = 0)
	{
		$app = JFactory::getApplication();
		$menus = $app->getMenu('site');
		
		// Prepare the reverse lookup array.
		if (self::$lookup === null)
		{
			self::$lookup = array();
			$component = JComponentHelper::getComponent('com_eventbooking');
			$items = $menus->getItems('component_id', $component->id);
			foreach ($items as $item)
			{
				if (isset($item->query) && isset($item->query['view']))
				{
					$view = $item->query['view'];
					if (!isset(self::$lookup[$view]))
					{
						self::$lookup[$view] = array();
					}
					if (isset($item->query['id']))
					{
						self::$lookup[$view][$item->query['id']] = $item->id;
					}
					else
					{
						self::$lookup[$view][0] = $item->id;
					}
				}
			}
		}
		
		if ($needles)
		{
			foreach ($needles as $view => $ids)
			{
				if (isset(self::$lookup[$view]))
				{
					foreach ($ids as $id)
					{
						if (isset(self::$lookup[$view][(int) $id]))
						{
							return self::$lookup[$view][(int) $id];
						}
					}
				}
			}
		}
		
		//Return default item id
		return $itemId;
	}
}