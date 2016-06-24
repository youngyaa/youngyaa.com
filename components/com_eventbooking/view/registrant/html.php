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

class EventbookingViewRegistrantHtml extends RADViewHtml
{

	public function display()
	{
		JFactory::getDocument()->addScript(JUri::base(true) . '/media/com_eventbooking/assets/js/paymentmethods.js');
		$this->setLayout('default');

		EventbookingHelper::addLangLinkForAjax();
		$db          = JFactory::getDbo();
		$query       = $db->getQuery(true);
		$user        = JFactory::getUser();
		$item        = $this->model->getData();
		$config      = EventbookingHelper::getConfig();
		$fieldSuffix = EventbookingHelper::getFieldSuffix();
		$userId      = $user->get('id');
		EventbookingHelper::checkEditRegistrant($item);

		if ($item->id)
		{
			$query->select('*, title' . $fieldSuffix . ' AS title')
				->from('#__eb_events')
				->where('id=' . $item->event_id);
			$db->setQuery($query);
			$event       = $db->loadObject();
			$this->event = $event;
		}

		if ($item->id && EventbookingHelper::isGroupRegistration($item->id))
		{
			$rowFields = EventbookingHelper::getFormFields($item->event_id, 1, $item->language);
			$query->clear();
			$query->select('*')
				->from('#__eb_registrants')
				->where('group_id=' . $item->id)
				->order('id');
			$db->setQuery($query);
			$rowMembers = $db->loadObjectList();
		}
		else
		{
			$rowFields  = EventbookingHelper::getFormFields($item->event_id, 0, $item->language);
			$rowMembers = array();
		}
		$form = new RADForm($rowFields);
		if ($item->id)
		{
			$data = Eventbookinghelper::getRegistrantData($item, $rowFields);
			$form->bind($data, false);
		}
		else
		{
			$data = array();
			$form->bind($data, true);
		}
		$form->bind($data);
		if ($userId && $user->authorise('eventbooking.registrants_management', 'com_eventbooking'))
		{
			$canChangeStatus    = true;
			$options            = array();
			$options[]          = JHtml::_('select.option', 0, JText::_('EB_PENDING'));
			$options[]          = JHtml::_('select.option', 1, JText::_('EB_PAID'));
			$options[]          = JHtml::_('select.option', 3, JText::_('EB_WAITING_LIST'));
			$options[]          = JHtml::_('select.option', 2, JText::_('EB_CANCELLED'));
			$lists['published'] = JHtml::_('select.genericlist', $options, 'published', ' class="inputbox" ', 'value', 'text', $item->published);
		}
		else
		{
			$canChangeStatus = false;
		}


		if (empty($item->id))
		{
			//Build list of event dropdown
			$options = array();
			$query->clear();
			$query->select('id, title' . $fieldSuffix . ' AS title, event_date')
				->from('#__eb_events')
				->where('published=1')
				->order('title');
			$db->setQuery($query);
			$options[] = JHtml::_('select.option', '', JText::_('Select Event'), 'id', 'title');
			if ($config->show_event_date)
			{
				$rows = $db->loadObjectList();
				for ($i = 0, $n = count($rows); $i < $n; $i++)
				{
					$row       = $rows[$i];
					$options[] = JHtml::_('select.option', $row->id, $row->title . ' (' . JHtml::_('date', $row->event_date, $config->date_format) . ')' .
						'', 'id', 'title');
				}
			}
			else
			{
				$options = array_merge($options, $db->loadObjectList());
			}
			$lists['event_id'] = JHtml::_('select.genericlist', $options, 'event_id', ' class="inputbox validate[required]" ', 'id', 'title', $item->event_id);
		}

		if ($config->collect_member_information && !$rowMembers && $item->number_registrants > 1)
		{
			$rowMembers = array();
			for ($i = 0; $i < $item->number_registrants; $i++)
			{
				$rowMember           = new RADTable('#__eb_registrants', 'id', $db);
				$rowMember->event_id = $item->event_id;
				$rowMember->group_id = $item->id;
				$rowMember->store();
				$rowMembers[] = $rowMember;
			}
		}

		if (count($rowMembers))
		{
			$this->memberFormFields = EventbookingHelper::getFormFields($item->event_id, 2, $item->language);
		}

		$this->item            = $item;
		$this->config          = $config;
		$this->lists           = $lists;
		$this->canChangeStatus = $canChangeStatus;
		$this->form            = $form;
		$this->rowMembers      = $rowMembers;
		$this->return          = $this->input->get('return', '', 'string');

		parent::display();
	}
}