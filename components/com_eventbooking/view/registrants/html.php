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

class EventbookingViewRegistrantsHtml extends RADViewHtml
{

	public function display()
	{
		$user = JFactory::getUser();
		if (!$user->authorise('eventbooking.registrants_management', 'com_eventbooking'))
		{
			if ($user->get('guest'))
			{
				JFactory::getApplication()->redirect('index.php?option=com_users&view=login&return=' . base64_encode(JUri::getInstance()->toString()));
			}
			else
			{
				JFactory::getApplication()->redirect('index.php', JText::_('NOT_AUTHORIZED'));
			}
		}

		$fieldSuffix = EventbookingHelper::getFieldSuffix();
		$db          = JFactory::getDbo();
		$query       = $db->getQuery(true);
		$config      = EventbookingHelper::getConfig();
		$model       = $this->getModel();
		$state       = $model->getState();
		$query->select('id, title' . $fieldSuffix . ' AS title, event_date')
			->from('#__eb_events')
			->where('published = 1')
			->order('title');
		//Get list of events				
		if ($config->only_show_registrants_of_event_owner)
		{
			$query->where('created_by = ' . (int) $user->id);
		}
		$db->setQuery($query);
		$options   = array();
		$options[] = JHtml::_('select.option', 0, JText::_('EB_SELECT_EVENT'), 'id', 'title');
		if ($config->show_event_date)
		{
			$rows = $db->loadObjectList();
			for ($i = 0, $n = count($rows); $i < $n; $i++)
			{
				$row       = $rows[$i];
				$options[] = JHtml::_('select.option', $row->id,
					$row->title . ' (' . JHtml::_('date', $row->event_date, $config->date_format, null) . ')' . '', 'id', 'title');
			}
		}
		else
		{
			$options = array_merge($options, $db->loadObjectList());
		}
		$lists['filter_event_id']  = JHtml::_('select.genericlist', $options, 'filter_event_id', ' class="inputbox" onchange="submit();"', 'id', 'title',
			$state->filter_event_id);
		$options                   = array();
		$options[]                 = JHtml::_('select.option', -1, JText::_('EB_REGISTRATION_STATUS'));
		$options[]                 = JHtml::_('select.option', 0, JText::_('EB_PENDING'));
		$options[]                 = JHtml::_('select.option', 1, JText::_('EB_PAID'));
		$options[]                 = JHtml::_('select.option', 2, JText::_('EB_CANCELLED'));
		$lists['filter_published'] = JHtml::_('select.genericlist', $options, 'filter_published', ' class="input-medium" onchange="submit()" ', 'value', 'text',
			$state->filter_published);
		$lists['search']           = $state->filter_search;
		$lists['order_Dir']        = $state->filter_order_Dir;
		$lists['order']            = $state->filter_order;
		$this->lists               = $lists;
		$this->items               = $model->getData();
		$this->pagination          = $model->getPagination();
		$this->config              = $config;

		parent::display();
	}
}