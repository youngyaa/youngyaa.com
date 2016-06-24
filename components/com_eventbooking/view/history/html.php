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

class EventbookingViewHistoryHtml extends RADViewHtml
{

	public function display()
	{
		if (!JFactory::getUser()->id)
		{
			JFactory::getApplication()->redirect('index.php?option=com_users&view=login&return=' . base64_encode(JUri::getInstance()->toString()));
		}

		$model              = $this->getModel();
		$state              = $model->getState();
		$config             = EventbookingHelper::getConfig();
		$lists['search']    = JString::strtolower($state->filter_search);
		$lists['order_Dir'] = $state->filter_order_Dir;
		$lists['order']     = $state->filter_order;

		//Get list of events
		$rows      = EventbookingHelperDatabase::getAllEvents();
		$options   = array();
		$options[] = JHtml::_('select.option', 0, JText::_('EB_SELECT_EVENT'), 'id', 'title');
		if ($config->show_event_date)
		{
			for ($i = 0, $n = count($rows); $i < $n; $i++)
			{
				$row       = $rows[$i];
				$options[] = JHtml::_('select.option', $row->id,
					$row->title . ' (' . JHtml::_('date', $row->event_date, $config->date_format, null) . ')' . '', 'id', 'title');
			}
		}
		else
		{
			$options = array_merge($options, $rows);
		}

		$lists['filter_event_id'] = JHtml::_('select.genericlist', $options, 'filter_event_id', 'class="input-xlarge" onchange="submit();"', 'id', 'title',
			$state->filter_event_id);
		$this->lists              = $lists;
		$this->items              = $model->getData();
		$this->pagination         = $model->getPagination();
		$this->config             = $config;

		parent::display();
	}
}