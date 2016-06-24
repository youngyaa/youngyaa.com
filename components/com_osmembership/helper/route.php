<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die();
require_once JPATH_ROOT . '/components/com_osmembership/helper/helper.php';

class OSMembershipHelperRoute
{

	protected static $config;

	protected static $lookup;

	protected static $plans;

	protected static $categories;

	public static function getPlanMenuId($id, &$catId = 0, $itemId = 0)
	{
		$needles = array('plan' => array((int) $id));
		if (!$catId)
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('category_id')
				->from('#__osmembership_plans')
				->where('id=' . (int) $id);
			$db->setQuery($query);
			$catId = (int) $db->loadResult();
		}

		if ($catId)
		{
			$needles['plans']      = self::getCategoriesPath($catId, 'id', false);
			$needles['categories'] = $needles['plans'];
		}

		return self::findItem($needles, $itemId);
	}

	/**
	 * Function to get Category Route
	 *
	 * @param  int $id
	 * @param int  $itemId
	 *
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
			$link                  = 'index.php?option=com_osmembership&view=plans&id=' . $id;
			$needles['plans']      = self::getCategoriesPath($id, 'id', false);
			$needles['categories'] = $needles['plans'];
			if ($item = self::findItem($needles, $itemId))
			{
				$link .= '&Itemid=' . $item;
			}
		}

		return $link;
	}

	/**
	 * Function to get sign up router
	 *
	 * @param int $id
	 * @param int $itemId
	 *
	 * @return string
	 */
	public static function getSignupRoute($id, $itemId = 0)
	{
		static $plans = null;

		if ($plans === null)
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id, category_id')
				->from('#__osmembership_plans')
				->where('published = 1');
			$db->setQuery($query);
			$plans = $db->loadObjectList('id');
		}

		if (!$id)
		{
			$link = '';
		}
		else
		{
			//Create the link
			$link    = 'index.php?option=com_osmembership&view=register&id=' . $id;
			$needles = array('register' => array($id),  'plan' => array($id));
			$catId   = (int) $plans[$id]->category_id;
			if ($catId)
			{
				$link .= '&catid=' . $catId;
				$needles['plans'] = self::getCategoriesPath($catId, 'id', false);
				$needles['categories'] = $needles['plans'];

			}

			if ($item = self::findItem($needles, $itemId))
			{
				$link .= '&Itemid=' . $item;
			}
		}

		return $link;
	}

	/**
	 * Get event title, used for building the router
	 *
	 * @param $id
	 *
	 * @return mixed
	 */
	public static function getPlanTitle($id)
	{
		if (!isset(self::$plans[$id]))
		{
			$fieldSuffix = OSMembershipHelper::getFieldSuffix();
			$db          = JFactory::getDbo();
			$query       = $db->getQuery(true);
			$query->select('alias' . $fieldSuffix)
				->from('#__osmembership_plans')
				->where('id=' . (int) $id);
			$db->setQuery($query);

			self::$plans[$id] = $db->loadResult();
		}

		return self::$plans[$id];
	}

	public static function getCategoryTitle($id)
	{
		if (!isset(self::$categories[$id]))
		{
			$fieldSuffix = OSMembershipHelper::getFieldSuffix();
			$db          = JFactory::getDbo();
			$query       = $db->getQuery(true);
			$query->select('alias' . $fieldSuffix)
				->from('#__osmembership_categories')
				->where('id=' . (int) $id);
			$db->setQuery($query);
			self::$categories[$id] = $db->loadResult();
		}

		return self::$categories[$id];
	}

	public static function getCategoriesPath($id, $type = 'id', $reverse = true, $parentId = 0)
	{
		static $categories = null;
		if ($categories === null)
		{
			$db          = JFactory::getDbo();
			$fieldSuffix = OSMembershipHelper::getFieldSuffix();
			$query       = $db->getQuery(true);
			$query->select('id, alias' . $fieldSuffix . ' AS alias, parent_id')
				->from('#__osmembership_categories')
				->where('published = 1');
			$db->setQuery($query);
			$categories = $db->loadObjectList('id');
		}
		$paths = array();
		$count = 0;
		do
		{
			if (isset($categories[$id]))
			{
				$paths[] = $categories[$id]->{$type};
				$id      = $categories[$id]->parent_id;
			}
			else
			{
				break;
			}
			$count++;
		} while ($id != $parentId && $count < 10);


		if ($reverse)
		{
			$paths = array_reverse($paths);
		}

		return $paths;
	}

	/**
	 * Find item id variable corresponding to the view
	 *
	 * @param $view
	 *
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
	 *
	 * @param string $needles
	 *
	 * @return int
	 */
	public static function findItem($needles = null, $itemId = 0)
	{
		$app   = JFactory::getApplication();
		$menus = $app->getMenu('site');

		// Prepare the reverse lookup array.
		if (self::$lookup === null)
		{
			self::$lookup = array();
			$component    = JComponentHelper::getComponent('com_osmembership');
			$items        = $menus->getItems('component_id', $component->id);
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