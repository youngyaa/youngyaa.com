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
JLoader::registerPrefix('Eventbooking', JPATH_ROOT . '/components/com_eventbooking');
JLoader::register('RADConfig', JPATH_ADMINISTRATOR . '/components/com_eventbooking/libraries/rad/config/config.php');

function EventbookingBuildRoute(&$query)
{
	$segments = array();
	$db       = JFactory::getDbo();
	$dbQuery  = $db->getQuery(true);

	//Store the query string to use in the parseRouter method
	$queryArr = $query;

	$app  = JFactory::getApplication();
	$menu = $app->getMenu();

	//We need a menu item.  Either the one specified in the query, or the current active one if none specified
	if (empty($query['Itemid']))
	{
		$menuItem      = $menu->getActive();
		$menuItemGiven = false;
	}
	else
	{
		$menuItem      = $menu->getItem($query['Itemid']);
		$menuItemGiven = true;
	}

	// If the given menu item doesn't belong to our component, unset the Itemid from query array
	if ($menuItemGiven && isset($menuItem) && $menuItem->component != 'com_eventbooking')
	{
		$menuItemGiven = false;
		unset($query['Itemid']);
	}

	if (empty($menuItem->query['view']))
	{
		$menuItem->query['view'] = '';
	}

	//Are we dealing with the current view which is attached to a menu item?
	if (($menuItem instanceof stdClass)
		&& isset($query['view'])
		&& isset($query['id'])
		&& $menuItem->query['view'] == $query['view']
		&& isset($query['id']) && $menuItem->query['id'] == intval($query['id'])
	)
	{
		unset($query['view']);
		if (isset($query['catid']))
		{
			unset($query['catid']);
		}
		unset($query['id']);
		if (isset($query['layout']))
		{
			unset($query['layout']);
		}
	}

	if (($menuItem instanceof stdClass)
		&& isset($query['view'])
		&& $menuItem->query['view'] == $query['view']
		&& (in_array($menuItem->query['view'], array('calendar', 'events')))
	)
	{
		unset($query['view']);
	}

	//Dealing with the catid parameter in the link to event.
	if (($menuItem instanceof stdClass)
		&& ($menuItem->query['view'] == 'category')
		&& isset($query['catid'])
		&& $menuItem->query['id'] == intval($query['catid'])
	)
	{
		if (isset($query['catid']))
		{
			unset($query['catid']);
		}
	}


	$view    = isset($query['view']) ? $query['view'] : '';
	$id      = isset($query['id']) ? (int) $query['id'] : 0;
	$catId   = isset($query['catid']) ? (int) $query['catid'] : 0;
	$eventId = isset($query['event_id']) ? (int) $query['event_id'] : 0;
	$task    = isset($query['task']) ? $query['task'] : '';
	$layout  = isset($query['layout']) ? $query['layout'] : '';

	switch ($view)
	{
		case 'calendar':
			$segments[] = JText::_('EB_CALENDAR');
			break;
		case 'categories':
		case 'category':
			if ($id)
			{
				$segments = array_merge($segments, EventbookingHelperRoute::getCategoriesPath($id, 'alias'));
			}
			unset($query['id']);
			break;
		case 'event':
			if ($id)
			{
				$segments[] = EventbookingHelperRoute::getEventTitle($id);
			}
			if ($layout == 'form')
			{
				$segments[] = 'Edit';
				unset($query['layout']);
			}
			else
			{
				if ($catId)
				{
					$segments = array_merge(EventbookingHelperRoute::getCategoriesPath($catId, 'alias'), $segments);
				}
			}
			unset($query['id']);
			break;
		case 'location':
			if ($layout == 'form')
			{
				if ($id)
				{
					$dbQuery->clear();
					$dbQuery->select('name')
						->from('#__eb_locations')
						->where('id=' . (int) $id);
					$db->setQuery($dbQuery);
					$segments[] = $id . '-' . $db->loadResult();
					$segments[] = 'edit';
					unset($query['id']);
				}
				else
				{
					$segments[] = 'Add Location';
				}

				unset($query['layout']);
			}
			else
			{
				if (isset($query['location_id']))
				{
					$dbQuery->clear();
					$dbQuery->select('name')
						->from('#__eb_locations')
						->where('id=' . (int) $query['location_id']);
					$db->setQuery($dbQuery);
					$segments[] = $db->loadResult();
					unset($query['location_id']);
				}
			}
			break;
		case 'map':
			if (isset($query['location_id']))
			{
				$dbQuery->clear();
				$dbQuery->select('name')
					->from('#__eb_locations')
					->where('id=' . (int) $query['location_id']);
				$db->setQuery($dbQuery);
				$segments[] = $db->loadResult();
				unset($query['location_id']);
			}
			$segments[] = 'View Map';
			break;
		case 'cart':
			$segments[] = 'View Cart';
			break;
		case 'invite':
			if ($id)
			{
				$segments[] = EventbookingHelperRoute::getEventTitle($id);
			}
			$segments[] = 'Invite Friend';
			unset($query['id']);
			break;
		case 'password':
			if ($eventId)
			{
				$segments[] = EventbookingHelperRoute::getEventTitle($eventId);
			}
			$segments[] = 'password validation';
			unset($query['id']);
			break;
		case 'registrantlist':
			if ($id)
			{
				$segments[] = EventbookingHelperRoute::getEventTitle($id);
			}
			$segments[] = 'Registrants List';
			unset($query['id']);
			break;
		case 'waitinglist':
			$segments[] = 'Join Waitinglist successfull';
			break;
		case 'failure':
			$segments[] = 'Registration Failure';
			break;
		case 'cancel':
			$segments[] = 'Registration Cancel';
			break;
		case 'complete':
			$segments[] = 'Registration Complete';
			break;
		case 'registrationcancel':
			$segments[] = 'Registration Cancelled';
			break;
		case 'search':
			$segments[] = 'search result';
			break;
		case 'events':
			$segments[] = 'my events';
			break;
	}

	switch ($task)
	{
		case 'register.individual_registration':
			if ($eventId)
			{
				$segments[] = EventbookingHelperRoute::getEventTitle($eventId);
			}
			$segments[] = 'Individual Registration';
			unset($query['task']);
			break;
		case 'register.group_registration':
			if ($eventId)
			{
				$segments[] = EventbookingHelperRoute::getEventTitle($eventId);
			}
			$segments[] = 'Group Registration';
			unset($query['task']);
			break;
		case 'group_billing':
			$segments[] = 'Group Billing';
			unset($query['task']);
			break;
		case 'event.download_ical':
			if ($eventId)
			{
				$segments[] = EventbookingHelperRoute::getEventTitle($eventId);
			}
			$segments[] = 'download_ical';
			unset($query['task']);
			break;
		case 'edit_registrant':
			$segments[] = 'Edit Registrant';
			unset($query['task']);
			break;
		case 'event.unpublish':
			if ($id)
			{
				$segments[] = EventbookingHelperRoute::getEventTitle($id);
			}
			$segments[] = 'Unpublish';
			unset($query['task']);
			unset($query['id']);
			break;

		case 'event.publish':
			if ($id)
			{
				$segments[] = EventbookingHelperRoute::getEventTitle($id);
			}
			$segments[] = 'Publish';
			unset($query['task']);
			unset($query['id']);
			break;
		case 'registrant.export':
			if ($eventId)
			{
				$segments[] = EventbookingHelperRoute::getEventTitle($eventId);
			}
			$segments[] = 'Export Registrants';
			unset($query['task']);
			break;
		case 'checkout':
		case 'view_checkout':
			$segments[] = 'Checkout';
			unset($query['task']);
			break;
	}
	if (isset($query['view']))
	{
		unset($query['view']);
	}
	if (isset($query['event_id']))
	{
		unset($query['event_id']);
	}

	if (isset($query['catid']))
	{
		unset($query['catid']);
	}
	if (count($segments))
	{
		$unProcessedVariables = array(
			'option',
			'Itemid',
			'category_id',
			'registration_code',
			'search',
			'start',
			'limitstart',
			'limit'
		);

		if ($view != 'location' && $view != 'map')
		{
			$unProcessedVariables[] = 'location_id';
		}

		foreach ($unProcessedVariables as $variable)
		{
			if (isset($queryArr[$variable]))
			{
				unset($queryArr[$variable]);
			}
		}
		$queryString = http_build_query($queryArr);
		$segments    = array_map('JApplication::stringURLSafe', $segments);
		$key         = md5(implode('/', $segments));
		$dbQuery     = $db->getQuery(true);
		$dbQuery->select('COUNT(*)')
			->from('#__eb_urls')
			->where('md5_key="' . $key . '"');
		$db->setQuery($dbQuery);
		$total = $db->loadResult();
		if (!$total)
		{
			$dbQuery->clear();
			$dbQuery->insert('#__eb_urls')
				->columns('md5_key, `query`')
				->values("'$key', '$queryString'");
			$db->setQuery($dbQuery);
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
function EventbookingParseRoute($segments)
{
	$vars = array();
	if (count($segments))
	{
		$db    = JFactory::getDbo();
		$key   = md5(str_replace(':', '-', implode('/', $segments)));
		$query = $db->getQuery(true);
		$query->select('`query`')
			->from('#__eb_urls')
			->where('md5_key="' . $key . '"');
		$db->setQuery($query);
		$queryString = $db->loadResult();
		if ($queryString)
		{
			parse_str(html_entity_decode($queryString), $vars);
		}
	}

	$item = JFactory::getApplication()->getMenu()->getActive();
	if ($item)
	{
		if (!empty($vars['view']) && !empty($item->query['view']) && $vars['view'] == $item->query['view'])
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

	if (isset($vars['tmpl']) && !isset($_GET['tmpl']))
	{
		unset($vars['tmpl']);
	}

	return $vars;
}