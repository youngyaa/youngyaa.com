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

class EventbookingViewRegistrantHtml extends RADViewItem
{

	protected function prepareView()
	{
		parent::prepareView();

		$db        = JFactory::getDbo();
		$config    = EventbookingHelper::getConfig();
		$rows      = EventbookingHelperDatabase::getAllEvents();
		$options[] = JHtml::_('select.option', 0, JText::_('Select Event'), 'id', 'title');
		if ($config->show_event_date)
		{
			for ($i = 0, $n = count($rows); $i < $n; $i++)
			{
				$row       = $rows[$i];
				$options[] = JHtml::_('select.option', $row->id, $row->title . ' (' . JHtml::_('date', $row->event_date, $config->date_format) . ')' .
					'', 'id', 'title');
			}
		}
		else
		{
			$options = array_merge($options, $rows);
		}

		$this->lists['event_id'] = JHtml::_('select.genericlist', $options, 'event_id', ' class="inputbox" ', 'id', 'title', $this->item->event_id);
		$event                   = EventbookingHelperDatabase::getEvent((int) $this->item->event_id);
		if ($this->item->id)
		{
			if (EventbookingHelper::isGroupRegistration($this->item->id))
			{
				$rowFields = EventbookingHelper::getFormFields($this->item->event_id, 1, $this->item->language);
			}
			else
			{
				$rowFields = EventbookingHelper::getFormFields($this->item->event_id, 0, $this->item->language);
			}
		}
		else
		{
			//Default, we just display individual registration form
			$rowFields = EventbookingHelper::getFormFields($this->item->event_id, 0);
		}
		$form = new RADForm($rowFields);
		if ($this->item->id)
		{
			$data = EventBookinghelper::getRegistrantData($this->item, $rowFields);
			$form->bind($data, false);
		}
		else
		{
			$data = array();
			$form->bind($data, true);
		}

		$options                  = array();
		$options[]                = JHtml::_('select.option', 0, JText::_('Pending'));
		$options[]                = JHtml::_('select.option', 1, JText::_('Paid'));
		$options[]                = JHtml::_('select.option', 3, JText::_('EB_WAITING_LIST'));
		$options[]                = JHtml::_('select.option', 2, JText::_('Cancelled'));
		$this->lists['published'] = JHtml::_('select.genericlist', $options, 'published', ' class="inputbox" ', 'value', 'text',
			$this->item->published);
		if ($this->item->id > 0)
		{
			$query = $db->getQuery(true);
			$query->select('*')
				->from('#__eb_registrants')
				->where('group_id=' . $this->item->id)
				->order('id');
			$db->setQuery($query);
			$rowMembers = $db->loadObjectList();
		}
		else
		{
			$rowMembers = array();
		}
		if ($config->collect_member_information && !$rowMembers && $this->item->number_registrants > 1)
		{
			$rowMembers = array();
			for ($i = 0; $i < $this->item->number_registrants; $i++)
			{
				$rowMember           = new RADTable('#__eb_registrants', 'id', $db);
				$rowMember->event_id = $this->item->event_id;
				$rowMember->group_id = $this->item->id;
				$rowMember->store();
				$rowMembers[] = $rowMember;
			}
		}

		$options                       = array();
		$options[]                     = JHtml::_('select.option', -1, JText::_('EB_PAYMENT_STATUS'));
		$options[]                     = JHtml::_('select.option', 0, JText::_('EB_PARTIAL_PAYMENT'));
		$options[]                     = JHtml::_('select.option', 1, JText::_('EB_FULL_PAYMENT'));
		$this->lists['payment_status'] = JHtml::_('select.genericlist', $options, 'payment_status', ' class="inputbox" ', 'value', 'text',
			$this->item->payment_status);

		if (count($rowMembers))
		{
			$this->memberFormFields = EventbookingHelper::getFormFields($this->item->event_id, 2, $this->item->language);
		}

		$this->config     = $config;
		$this->event      = $event;
		$this->rowMembers = $rowMembers;
		$this->form       = $form;
	}
}