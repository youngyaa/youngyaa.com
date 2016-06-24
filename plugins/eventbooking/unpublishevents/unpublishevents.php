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

class plgEventBookingUnpublishEvents extends JPlugin
{
	public function onAfterStoreRegistrant($row)
	{
		if (strpos($row->payment_method, 'os_offline') !== false)
		{
			$this->processUnpublishEvent($row->event_id);
		}
	}

	public function onAfterPaymentSuccess($row)
	{
		$this->processUnpublishEvent($row->event_id);
	}

	private function processUnpublishEvent($eventId)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('event_capacity')
			->from('#__eb_events')
			->where('id = ' . $eventId);
		$db->setQuery($query);
		$capacity = (int) $db->loadResult();
		if ($capacity > 0)
		{
			$query->clear();
			$query->select('COUNT(*)')
				->from('#__eb_registrants AS b')
				->where('event_id = ' . (int) $eventId)
				->where('b.group_id = 0')
				->where('(b.published = 1 OR (b.payment_method LIKE "os_offline%" AND b.published NOT IN (2,3)))');
			$db->setQuery($query);
			$totalRegistrants = (int) $db->loadResult();
			if ($totalRegistrants >= $capacity)
			{
				// Un-publish the event
				$query->clear();
				$query->update('#__eb_events')
					->set('published = 0')
					->where('id = ' . (int) $eventId);
				$db->setQuery($query);
				$db->execute();
			}
		}
	}
}