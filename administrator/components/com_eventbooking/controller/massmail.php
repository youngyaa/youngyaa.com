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

class EventbookingControllerMassmail extends EventbookingController
{
	/**
	 * Send Massmail to registrants of an event
	 */
	public function send()
	{
		$data  = $this->input->getData();
		$model = $this->getModel();
		$model->send($data);
		$this->setRedirect('index.php?option=com_eventbooking&view=massmail', JText::_('EB_EMAIL_SENT'));
	}

	/**
	 * Cancel sending massmail, redirect back to dashboard
	 */
	public function cancel()
	{
		$this->setRedirect('index.php?option=com_eventbooking&view=dashboard');
	}
}