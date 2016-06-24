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

class EventbookingModelCoupon extends RADModelAdmin
{
	/**
	 * Post - process, Store coupon code mapping with events.
	 *
	 * @param JTable   $row
	 * @param RADInput $input
	 * @param bool     $isNew
	 */
	protected function afterStore($row, $input, $isNew)
	{
		$eventIds = $input->get('event_id', array(), 'array');
		if (count($eventIds) == 0 || $eventIds[0] == -1)
		{
			$row->event_id = -1;
		}
		else
		{
			$row->event_id = 1;
		}
		$row->store();
		$couponId = $row->id;
		$db       = $this->getDbo();
		$query    = $db->getQuery(true);
		if (!$isNew)
		{
			$query->delete('#__eb_coupon_events')->where('coupon_id = ' . $couponId);
			$db->setQuery($query);
			$db->execute();
		}

		if ($row->event_id != -1)
		{
			$query->clear();
			$query->insert('#__eb_coupon_events')->columns('coupon_id, event_id');
			for ($i = 0, $n = count($eventIds); $i < $n; $i++)
			{
				$eventId = (int) $eventIds[$i];
				if ($eventId > 0)
				{
					$query->values("$couponId, $eventId");
				}
			}
			$db->setQuery($query);
			$db->execute();
		}
	}

	/**
	 * Method to remove  fields
	 *
	 * @access    public
	 * @return    boolean    True on success
	 */
	public function delete($cid = array())
	{
		if (count($cid))
		{
			$db    = $this->getDbo();
			$query = $db->getQuery(true);
			$cids  = implode(',', $cid);
			$query->delete('#__eb_coupon_events')->where('coupon_id IN (' . $cids . ')');
			$db->setQuery($query);
			$db->execute();
			//Do not allow deleting core fields
			$query->clear();
			$query->delete('#__eb_coupons')->where('id IN (' . $cids . ')');
			$db->setQuery($query);
			$db->execute();
		}

		return true;
	}
}