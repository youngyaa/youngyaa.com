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

class EventbookingViewCalendarRaw extends RADViewHtml
{

	public function display()
	{
		$currentDateData = EventbookingModelCalendar::getCurrentDateData();

		//Initialize default month and year
		$month = $this->input->getInt('month', 0);
		$year  = $this->input->getInt('year', 0);
		if (!$month)
		{
			$month = $currentDateData['month'];
		}

		if (!$year)
		{
			$year = $currentDateData['year'];
		}

		$model    = new EventbookingModelCalendar(array('remember_states' => false, 'ignore_request' => true));
		$model->setState('month', $month)
			->setState('year', $year);

		$rows        = $model->getData();
		$this->data  = EventbookingHelperData::getCalendarData($rows, $year, $month, true);
		$this->month = $month;
		$this->year  = $year;

		$days     = array();
		$startDay = EventBookingHelper::getConfigValue('calendar_start_date');
		for ($i = 0; $i < 7; $i++)
		{
			$days[$i] = EventbookingHelperData::getDayNameHtmlMini(($i + $startDay) % 7, true);
		}

		$listMonth = array(
			JText::_('EB_JAN'),
			JText::_('EB_FEB'),
			JText::_('EB_MARCH'),
			JText::_('EB_APR'),
			JText::_('EB_MAY'),
			JText::_('EB_JUNE'),
			JText::_('EB_JUL'),
			JText::_('EB_AUG'),
			JText::_('EB_SEP'),
			JText::_('EB_OCT'),
			JText::_('EB_NOV'),
			JText::_('EB_DEC'));

		$this->days      = $days;
		$this->listMonth = $listMonth;

		parent::display();
	}
}