<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

class plgOSMembershipInvoice extends JPlugin
{
	/**
	 * Run when a membership activated
	 *
	 * @param PlanOsMembership $row
	 */
	function onMembershipActive($row)
	{
		if (!$row->invoice_number && OSMembershipHelper::needToCreateInvoice($row))
		{
			$row->invoice_number = OSMembershipHelper::getInvoiceNumber($row);
			$row->store();
		}

		return true;
	}

	function onAfterStoreSubscription($row)
	{
		if ($row->payment_method == 'os_offline' && !$row->invoice_number && OSMembershipHelper::needToCreateInvoice($row))
		{
			$row->invoice_number = OSMembershipHelper::getInvoiceNumber($row);
			$row->store();
		}
	}
}
