<?php
/**
 * @version        2.0.0
 * @package        Joomla
 * @subpackage     Event Booking
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2010 - 2015 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die();

/**
 * HTML View class for the Event Booking component
 *
 * @static
 * @package        Joomla
 * @subpackage     Event Booking
 */
class EventbookingViewEventsHtml extends RADViewHtml
{

	public function display()
	{
		if (JFactory::getUser()->get('guest'))
		{
			JFactory::getApplication()->redirect('index.php?option=com_users&view=login&return=' . base64_encode(JUri::getInstance()->toString()));

			return;
		}
		$model            = $this->getModel();
		$this->items      = $model->getData();
		$this->pagination = $model->getPagination();
		$this->config     = EventbookingHelper::getConfig();
		$this->nullDate   = JFactory::getDbo()->getNullDate();

		//Add categories filter
		$state                             = $model->getState();
		$this->lists['filter_category_id'] = EventbookingHelperHtml::buildCategoryDropdown($state->filter_category_id, 'filter_category_id',
			'onchange="submit();"');
		$this->lists['filter_search']      = $state->filter_search;

		parent::display();
	}
}