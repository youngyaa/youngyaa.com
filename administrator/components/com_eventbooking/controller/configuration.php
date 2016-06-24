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

class EventbookingControllerConfiguration extends EventbookingController
{
	/**
	 * Save configuration data
	 */
	public function save()
	{
		$data  = $this->input->getData(RAD_INPUT_ALLOWRAW);
		$model = $this->getModel();
		$model->store($data);

		$task = $this->getTask();
		if ($task == 'save')
		{
			$this->setRedirect('index.php?option=com_eventbooking&view=dashboard', JText::_('EB_CONFIGURATION_DATA_SAVED'));
		}
		else
		{
			$this->setRedirect('index.php?option=com_eventbooking&view=configuration', JText::_('EB_CONFIGURATION_DATA_SAVED'));
		}
	}

	/**
	 * Cancel configuration action, redirect back to dashboard
	 */
	public function cancel()
	{
		$this->setRedirect('index.php?option=com_eventbooking&view=dashboard');
	}
}