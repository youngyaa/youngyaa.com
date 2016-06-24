<?php
/**
 * @version        2.0.0
 * @package        Joomla
 * @subpackage     Event Booking
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2010 - 2015 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die;

error_reporting(0);
require_once JPATH_ROOT . '/components/com_eventbooking/helper/helper.php';
require_once JPATH_ROOT . '/components/com_eventbooking/helper/route.php';
require_once JPATH_ROOT . '/components/com_eventbooking/helper/jquery.php';
$user     = JFactory::getUser();
$config   = EventbookingHelper::getConfig();
$document = JFactory::getDocument();
EventbookingHelper::loadLanguage();
$db      = JFactory::getDbo();
$query   = $db->getQuery(true);
$baseUrl = JUri::base(true);
$itemId  = (int) $params->get('item_id', 0);
if (!$itemId)
{
	$itemId = EventbookingHelper::getItemid();
}
$fieldSuffix  = EventbookingHelper::getFieldSuffix();
$currentDate  = JHtml::_('date', 'Now', 'Y-m-d');
$numberEvents = $params->get('number_events', 6);
$categoryIds  = trim($params->get('category_ids', ''));
$showCategory = $params->get('show_category', 1);
$showLocation = $params->get('show_location');

$query->select('a.*, c.name AS location_name')
	->from('#__eb_events AS a')
	->leftJoin('#__eb_locations AS c ON a.location_id = c.id')
	->where('a.published = 1')
	->where('a.access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')')
	->where('DATE(a.event_date) >= ' . $db->quote($currentDate))
	->order('a.event_date');

if ($fieldSuffix)
{
	$query->select('a.title' . $fieldSuffix . ' AS title');
}

if ($categoryIds)
{
	$query->where('a.id IN (SELECT event_id FROM #__eb_event_categories WHERE category_id IN (' . $categoryIds . '))');
}

$db->setQuery($query, 0, $numberEvents);
$rows = $db->loadObjectList();

$query->clear();
$query->select('a.id, a.name' . $fieldSuffix . ' AS name')
	->from('#__eb_categories AS a')
	->innerJoin('#__eb_event_categories AS b ON a.id = b.category_id');

for ($i = 0, $n = count($rows); $i < $n; $i++)
{
	$row = $rows[$i];
	$query->where('b.event_id = ' . $row->id);
	$db->setQuery($query);
	$categories = $db->loadObjectList();
	if (count($categories))
	{
		$itemCategories = array();
		foreach ($categories as $category)
		{
			$itemCategories[] = '<a href="' . EventbookingHelperRoute::getCategoryRoute($category->id, $itemId) . '"><strong>' . $category->name .
				'</strong></a>';
		}
		$row->categories = implode('&nbsp;|&nbsp;', $itemCategories);
	}
	$query->clear('where');
}


$layout = $params->get('layout', 'default');
if ($layout == 'default')
{
	$document->addStyleSheet($baseUrl . '/modules/mod_eb_events/css/style.css');
}
else
{
	if ($config->load_bootstrap_css_in_frontend !== '0')
	{
		EventbookingHelper::loadBootstrap();
	}
	$document->addStyleSheet($baseUrl . '/modules/mod_eb_events/css/improved.css');
}
if ($config->calendar_theme)
{
	$theme = $config->calendar_theme;
}
else
{
	$theme = 'default';
}

$document->addStyleSheet($baseUrl . '/media/com_eventbooking/assets/css/themes/' . $theme . '.css');
require(JModuleHelper::getLayoutPath('mod_eb_events', $layout));