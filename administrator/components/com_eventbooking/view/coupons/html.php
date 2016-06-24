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

class EventbookingViewCouponsHtml extends RADViewList
{

	protected function prepareView()
	{
		parent::prepareView();

		$config              = EventbookingHelper::getConfig();
		$rows      = EventbookingHelperDatabase::getAllEvents();
		$options   = array();
		$options[] = JHtml::_('select.option', 0, JText::_('EB_ALL_EVENTS'), 'id', 'title');
		if ($config->show_event_date)
		{
			for ($i = 0, $n = count($rows); $i < $n; $i++)
			{
				$row       = $rows[$i];
				$options[] = JHtml::_('select.option', $row->id,
					$row->title . ' (' . JHtml::_('date', $row->event_date, $config->date_format) . ')' . '', 'id', 'title');
			}
		}
		else
		{
			$options = array_merge($options, $rows);
		}
		$this->lists['filter_event_id'] = JHtml::_('select.genericlist', $options, 'filter_event_id', 'class="inputbox" onchange="submit();" ',
			'id', 'title', $this->state->filter_event_id);

		$discountTypes       = array(0 => '%', 1 => $config->get('currency_symbol', '$'));
		$this->discountTypes = $discountTypes;
		$this->nullDate      = JFactory::getDbo()->getNullDate();
		$this->dateFormat    = $config->get('date_format', 'Y-m-d');
	}
}