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

class plgEventbookingInvoice extends JPlugin
{

	/**
	 * Generate invoice number after registrant complete payment for registration
	 *
	 * @param $row
	 *
	 * @return bool
	 */
	public function onAfterPaymentSuccess($row)
	{
		if (!$row->invoice_number)
		{
			$this->processInvoiceNumber($row);
		}

		return true;
	}

	/**
	 * Generate invoice number after registrant complete registration in case he uses offline payment
	 *
	 * @param $row
	 */
	public function onAfterStoreRegistrant($row)
	{
		if ((strpos($row->payment_method, 'os_offline') !== false) && !$row->invoice_number)
		{
			$this->processInvoiceNumber($row);
		}
	}


	private function processInvoiceNumber($row)
	{
		if (EventbookingHelper::needInvoice($row))
		{
			$invoiceNumber       = EventbookingHelper::getInvoiceNumber();
			$row->invoice_number = $invoiceNumber;
			$row->store();
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->update('#__eb_registrants')
				->set('invoice_number=' . $db->quote($invoiceNumber))
				->where('id=' . $row->id . ' OR cart_id=' . $row->id . ' OR group_id=' . $row->id);
			$db->setQuery($query);
			$db->execute();
		}
	}
}
