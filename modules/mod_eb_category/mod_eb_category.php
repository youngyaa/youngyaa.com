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
require_once JPATH_ROOT . '/components/com_eventbooking/helper/helper.php';
require_once JPATH_ROOT . '/components/com_eventbooking/helper/route.php';
EventbookingHelper::loadLanguage();
$fieldSuffix      = EventbookingHelper::getFieldSuffix();
$db               = JFactory::getDbo();
$query            = $db->getQuery(true);
$numberCategories = (int) $params->get('number_categories', 0);

$query->select('a.id, a.name' . $fieldSuffix . ' AS name')
	->from('#__eb_categories AS a')
	->where('a.parent = 0')
	->where('a.published = 1')
	->where('a.access IN (' . implode(',', JFactory::getUser()->getAuthorisedViewLevels()) . ')')
	->order('a.ordering');

if ($numberCategories)
{
	$db->setQuery($query, 0, $numberCategories);
}
else
{
	$db->setQuery($query);
}
$rows = $db->loadObjectList();

$itemId = (int) $params->get('item_id');
if (!$itemId)
{
	$itemId = EventbookingHelper::getItemid();
}
require(JModuleHelper::getLayoutPath('mod_eb_category', 'default'));