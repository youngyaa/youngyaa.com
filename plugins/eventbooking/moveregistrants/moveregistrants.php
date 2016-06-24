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

class plgEventBookingMoveRegistrants extends JPlugin
{
	/**
	 * Move potential users from waiting list to registrants
	 *
	 * @param $row
	 *
	 * @return bool
	 */
	public function onRegistrationCancel($row)
	{
		require_once JPATH_ROOT . '/components/com_eventbooking/helper/helper.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_eventbooking/table/eventbooking.php';
		$db               = JFactory::getDbo();
		$query            = $db->getQuery(true);
		$config           = EventBookingHelper::getConfig();
		$totalRegistrants = 0;
		while ($totalRegistrants < $row->number_registrants)
		{
			$remainingNumberRegistrants = $row->number_registrants - $totalRegistrants;
			$query->clear();
			$query->select('id')
				->from('#__eb_registrants')
				->where('event_id = ' . $row->event_id)
				->where('published = 3')
				->where('number_registrants <= ' . $remainingNumberRegistrants)
				->order('id');
			$db->setQuery($query, 0, 1);
			$id = (int) $db->loadResult();
			if ($id)
			{
				$registrant = JTable::getInstance('EventBooking', 'Registrant');
				$registrant->load($id);
				$registrant->register_date = date('Y-m-d H:i:s');
				if ($registrant->number_registrants >= 2)
				{
					$registrant->is_group_billing = 1;
				}
				$registrant->published = 1;
				$registrant->store();
				if ($registrant->number_registrants >= 2)
				{
					$numberRegistrants = $registrant->number_registrants;
					$rowMember         = JTable::getInstance('EventBooking', 'Registrant');
					for ($i = 0; $i < $numberRegistrants; $i++)
					{
						$rowMember->id                 = 0;
						$rowMember->group_id           = $registrant->id;
						$rowMember->number_registrants = 1;
						$rowMember->published          = 1;
						$rowMember->register_date      = date('Y-m-d H:i:s');
						$rowMember->store();
					}
				}
				EventBookingHelper::sendEmails($registrant, $config);
				if ($registrant->number_registrants)
				{
					$totalRegistrants += $registrant->number_registrants;
				}
				else
				{
					$totalRegistrants++;
				}
			}
			else
			{
				break;
			}
		}

		return true;
	}
}	