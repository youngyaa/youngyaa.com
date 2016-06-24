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
defined('_JEXEC') or die();

class EventbookingViewFailureHtml extends RADViewHtml
{

	public function display()
	{
		$this->setLayout('default');
		$reason = isset($_SESSION['reason']) ? $_SESSION['reason'] : '';
		if (!$reason)
		{
			$reason = $this->input->getString('failReason', '');
		}
		$this->reason = $reason;

		parent::display();
	}
}