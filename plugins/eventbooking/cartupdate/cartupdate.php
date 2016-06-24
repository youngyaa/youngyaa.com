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

class plgEventBookingCartUpdate extends JPlugin
{

	/**
	 * Mark all registration records in cart paid when the payment completed
	 *
	 * @param $row
	 */
	public function onAfterPaymentSuccess($row)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->update('#__eb_registrants')
			->set('published = 1')
			->set('payment_date = NOW()')
			->where('cart_id = ' . (int) $row->id);
		$db->setQuery($query);
		$db->execute();
	}
}