<?php
/**
 * @version        	2.0.0
 * @package        	Joomla
 * @subpackage		Event Booking
 * @author  		Tuan Pham Ngoc
 * @copyright    	Copyright (C) 2010 - 2015 Ossolution Team
 * @license        	GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die();

class EventbookingControllerMessage extends EventbookingController
{

	public function save()
	{
		$data = $this->input->getData();
		$model = $this->getModel();
		$model->store($data);

		$task = $this->getTask();
		if ($task == 'save')
		{
			$this->setRedirect('index.php?option=com_eventbooking&view=dashboard', JText::_('EB_MESSAGES_SAVED'));
		}
		else
		{
			$this->setRedirect('index.php?option=com_eventbooking&view=message', JText::_('EB_MESSAGES_SAVED'));
		}
	}
	
	public function cancel()
	{
		$this->setRedirect('index.php?option=com_eventbooking&view=dashboard');
	}
}