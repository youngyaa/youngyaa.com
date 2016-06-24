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

class EventbookingViewMassmailHtml extends RADViewHtml
{

	public function display()
	{
		$config    = EventbookingHelper::getConfig();
		$rows      = EventbookingHelperDatabase::getAllEvents();
		$options   = array();
		$options[] = JHtml::_('select.option', 0, JText::_('Select Event'), 'id', 'title');
		if ($config->show_event_date)
		{
			for ($i = 0, $n = count($rows); $i < $n; $i++)
			{
				$row       = $rows[$i];
				$options[] = JHtml::_('select.option', $row->id, $row->title . ' (' . JHtml::_('date', $row->event_date, $config->date_format, null) . ')' .
					'', 'id', 'title');
			}
		}
		else
		{
			$options = array_merge($options, $rows);
		}
		$lists             = array();
		$lists['event_id'] = JHtml::_('select.genericlist', $options, 'event_id', '', 'id', 'title');
		$this->lists       = $lists;

		parent::display();
	}
}