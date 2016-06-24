<?php
/**
 * @version            2.3.0
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2016 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die();

class OSMembershipViewFailureHtml extends MPFViewHtml
{
	public $hasModel = false;

	public function display()
	{
		$this->setLayout('default');
		$reason = JFactory::getSession()->get('omnipay_payment_error_reason');
		if (!$reason)
		{
			$reason = $this->input->getString('failReason', '');
		}
		$this->reason = $reason;

		parent::display();
	}
}