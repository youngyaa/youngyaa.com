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

class plgEventBookingJSactivities extends JPlugin
{
	public function onAfterPaymentSuccess($row)
	{
		if (!file_exists(JPATH_ADMINISTRATOR . '/components/com_community/community.php'))
		{
			return;
		}
		jimport('joomla.utilities.date');
		require_once JPATH_ROOT . '/components/com_eventbooking/helper/helper.php';
		$itemId = EventbookingHelper::getItemid();
		EventbookingHelper::loadLanguage();
		$user  = JFactory::getUser();
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('title')
			->from('#__eb_events')
			->where('id = ' . (int) $row->event_id);
		$db->setQuery($query);
		$eventTitle  = $db->loadResult();
		$url         = JRoute::_('index.php?option=com_eventbooking&view=event&id=' . $row->event_id . '&Itemid=' . $itemId);
		$eventTitle  = '<a href="' . $url . '"><strong>' . $eventTitle . '<strong></a>';
		$obj         = new StdClass();
		$obj->actor  = $user->id;
		$obj->target = $user->id;
		if ($user->id)
		{
			$obj->title = JText::sprintf('EB_ACTOR_REGISTER_FOR_EVENT', $eventTitle);
		}
		else
		{
			$obj->title = JText::sprintf('EB_USER_REGISTER_FOR_EVENT', $row->first_name . ' ' . $row->last_name, $eventTitle);
		}
		$obj->content = '';
		$obj->app     = '';
		$obj->cid     = $user->id;
		$obj->params  = null;
		$obj->created = JFactory::getDate()->toSql();
		$obj->points  = 0;
		$obj->access  = 0;
		$db->insertObject('#__community_activities', $obj);
	}
}
