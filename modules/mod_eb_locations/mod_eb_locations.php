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

$document = JFactory::getDocument();
$document->addStylesheet(JUri::base(true) . '/media/com_eventbooking/assets/css/style.css', 'text/css', null, null);
$user = JFactory::getUser();
EventbookingHelper::loadLanguage();
$app              = JFactory::getApplication();
$db               = JFactory::getDBO();
$query            = $db->getQuery(true);
$numberLocations  = (int) $params->get('number_locations', 0);
$showNumberEvents = (int) $params->get('show_number_events', 1);

$query->select('a.id, a.name, COUNT(b.id) AS total_events')
	->from('#__eb_locations AS a')
	->leftJoin('#__eb_events AS b ON (a.id = b.location_id AND b.access IN (' . implode(',', $user->getAuthorisedViewLevels()) . '))')
	->where('a.published = 1')
	->group('a.id')
	->having('total_events > 0')
	->order('a.name');

if ($app->getLanguageFilter())
{
	$query->where('a.language IN (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
}

if ($numberLocations)
{
	$db->setQuery($query, 0, $numberLocations);
}
else
{
	$db->setQuery($query);
}
$rows   = $db->loadObjectList();
$itemId = (int) $params->get('item_id');
if (!$itemId)
{
	$itemId = EventBookingHelper::getItemid();
}

require(JModuleHelper::getLayoutPath('mod_eb_locations', 'default'));