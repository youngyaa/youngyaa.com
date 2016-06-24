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
require_once JPATH_ROOT . '/components/com_osmembership/helper/route.php';
function OSMembershipBuildRoute(&$query)
{
	$segments = array();

	//We need a menu item.  Either the one specified in the query, or the current active one if none specified
	$menu        = JFactory::getApplication()->getMenu();
	if (empty($query['Itemid']))
	{
		$menuItem = $menu->getActive();
	}
	else
	{
		$menuItem = $menu->getItem($query['Itemid']);
	}

	$mView = empty($menuItem->query['view']) ? null : $menuItem->query['view'];
	$mId = empty($menuItem->query['id']) ? null : (int)$menuItem->query['id'];

	$view  = isset($query['view']) ? $query['view'] : '';
	$id    = isset($query['id']) ? (int) $query['id'] : 0;

	//Are we dealing with plan or register view which is attached to a menu item?
	if ($menuItem && $mView && $mId && $mView == $view && $mId == $id)
	{
		unset($query['view']);
		if (isset($query['catid']))
		{
			unset($query['catid']);
		}
		unset($query['id']);

		$view = '';
		$id = '';
	}

	// Link to register view and we have a menu item to link to plan view of the plan
	if ($menuItem && $view == 'register' && $mView == 'plan' && $mId == $id)
	{
		if (isset($query['catid']))
		{
			unset($query['catid']);
		}
		unset($query['id']);
		$id = '';
	}

	// Special case for groupmembers router
	if ($menuItem && $mView == 'groupmembers' && $mView == $view)
	{
		unset($query['view']);
		$view = '';
	}

	if ($mView == 'plans' && $mId)
	{
		$parentId = (int) $menuItem->query['id'];
		if (isset($query['catid']) && $mId == intval($query['catid']))
		{
			unset($query['catid']);
		}
	}
	else
	{
		$parentId = 0;
	}

	$queryArr = $query;

	$task  = isset($query['task']) ? $query['task'] : '';
	$catId = isset($query['catid']) ? (int) $query['catid'] : 0;


	switch ($view)
	{
		case 'plans':
			if ($id)
			{
				$segments = array_merge($segments, OSMembershipHelperRoute::getCategoriesPath($id, 'alias', true, $parentId));
			}
			unset($query['view']);
			unset($query['id']);
			break;
		case 'plan':

			if ($catId)
			{
				$segments = array_merge($segments, OSMembershipHelperRoute::getCategoriesPath($catId, 'alias', true, $parentId));
			}

			if ($id)
			{
				$segments[] = OSMembershipHelperRoute::getPlanTitle($id);
			}

			unset($query['view']);
			unset($query['id']);
			if (isset($query['catid']))
			{
				unset($query['catid']);
			}
			break;
		case 'register':
			if ($catId)
			{
				$segments = array_merge($segments, OSMembershipHelperRoute::getCategoriesPath($catId, 'alias', true, $parentId));
			}

			if ($id)
			{
				$segments[] = OSMembershipHelperRoute::getPlanTitle($id);
			}

			$segments[] = 'Sign up';
			unset($query['view']);
			unset($query['id']);
			if (isset($query['catid']))
			{
				unset($query['catid']);
			}
			break;
		case 'failure':
			$segments[] = 'Subscription Failure';
			unset($query['view']);
			break;
		case 'cancel':
			$segments[] = 'Subscription cancel';
			unset($query['view']);
			break;
		case 'complete':
			$segments[] = 'Subscription Complete';
			unset($query['view']);
			break;
		case 'subscription':
			$segments[] = 'Subscription Detail';
			unset($query['view']);
			break;
	}

	if ($task == 'renew_membership')
	{
		$segments[] = 'Renew Membership';
		unset($query['task']);
	}

	if (count($segments))
	{
		$unProcessedVariables = array(
			'option',
			'Itemid',
			'search',
			'start',
			'limitstart',
			'limit'
		);

		foreach ($unProcessedVariables as $variable)
		{
			if (isset($queryArr[$variable]))
			{
				unset($queryArr[$variable]);
			}
		}

		$queryString = http_build_query($queryArr);
		$db       = JFactory::getDbo();
		$segments = array_map('JApplication::stringURLSafe', $segments);
		$key      = md5(implode('/', $segments));
		$q        = $db->getQuery(true);
		$q->select('COUNT(*)')
			->from('#__osmembership_sefurls')
			->where('md5_key="' . $key . '"');
		$db->setQuery($q);
		$total = $db->loadResult();

		if (!$total)
		{
			$q->clear();
			$q->insert('#__osmembership_sefurls')
				->columns('md5_key, `query`')
				->values("'$key', '$queryString'");
			$db->setQuery($q);
			$db->execute();
		}
	}

	return $segments;
}

/**
 *
 * Parse the segments of a URL.
 *
 * @param    array    The segments of the URL to parse.
 *
 * @return    array    The URL attributes to be used by the application.
 */
function OSMembershipParseRoute($segments)
{
	$vars = array();
	if (count($segments))
	{
		$db    = JFactory::getDbo();
		$key   = md5(str_replace(':', '-', implode('/', $segments)));
		$query = $db->getQuery(true);
		$query->select('`query`')
			->from('#__osmembership_sefurls')
			->where('md5_key="' . $key . '"');
		$db->setQuery($query);
		$queryString = $db->loadResult();
		if ($queryString)
		{
			parse_str(html_entity_decode($queryString), $vars);
		}
	}

	$item = JFactory::getApplication()->getMenu()->getActive();
	if ($item && $item->component == 'com_osmembership')
	{
		$mView = empty($item->query['view']) ? null : $item->query['view'];
		if (!empty($vars['view']) && ($vars['view'] == $mView || ($vars['view'] == 'register' && $mView == 'plan')))
		{
			foreach ($item->query as $key => $value)
			{
				if ($key != 'option' && $key != 'Itemid' && !isset($vars[$key]))
				{
					$vars[$key] = $value;
				}
			}
		}
	}

	return $vars;
}