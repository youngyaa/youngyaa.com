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

class EventbookingViewLocationsHtml extends RADViewHtml
{

	public function display()
	{
		if (!JFactory::getUser()->authorise('eventbooking.addlocation', 'com_eventbooking'))
		{
			JFactory::getApplication()->redirect('index.php', JText::_("EB_NO_PERMISSION"));

			return;
		}
		$model            = $this->getModel();
		$this->items      = $model->getData();
		$this->pagination = $model->getPagination();

		parent::display();
	}
}