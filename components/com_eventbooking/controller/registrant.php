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

class EventbookingControllerRegistrant extends EventbookingController
{
	/**
	 * Save the registration record and back to registration record list
	 */
	public function save()
	{
		$this->csrfProtection();
		$model = $this->getModel('registrant');
		$model->store($this->input);
		$return = base64_decode($this->input->getString('return', ''));
		if ($return)
		{
			$this->setRedirect($return);
		}
		else
		{
			$this->setRedirect(JRoute::_(EventbookingHelperRoute::getViewRoute('registrants', $this->input->getInt('Itemid')), false));
		}
	}

	/**
	 * Cancel registration for the event
	 */
	public function cancel()
	{
		$app              = JFactory::getApplication();
		$db               = JFactory::getDbo();
		$query            = $db->getQuery(true);
		$user             = JFactory::getUser();
		$Itemid           = $this->input->getInt('Itemid', 0);
		$id               = $this->input->getInt('id', 0);
		$registrationCode = $this->input->getString('cancel_code', '');
		$fieldSuffix      = EventbookingHelper::getFieldSuffix();
		if ($id)
		{
			$query->select('a.id, a.title' . $fieldSuffix . ' AS title, b.user_id, cancel_before_date, DATEDIFF(cancel_before_date, NOW()) AS number_days')
				->from('#__eb_events AS a')
				->innerJoin('#__eb_registrants AS b ON a.id = b.event_id')
				->where('b.id = ' . $id);
		}
		else
		{
			$query->select('a.id, a.title' . $fieldSuffix . ' AS title, b.id AS registrant_id, b.user_id, cancel_before_date, DATEDIFF(cancel_before_date, NOW()) AS number_days')
				->from('#__eb_events AS a')
				->innerJoin('#__eb_registrants AS b ON a.id = b.event_id')
				->where('b.registration_code = ' . $db->quote($registrationCode));
		}
		$db->setQuery($query);
		$rowEvent = $db->loadObject();

		if (!$rowEvent)
		{
			$app->redirect(JRoute::_('index.php?option=com_eventbooking&Itemid=' . $Itemid), JText::_('EB_INVALID_ACTION'));
		}

		if (($user->get('id') == 0 && !$registrationCode) || ($user->get('id') != $rowEvent->user_id))
		{
			$app->redirect(JRoute::_('index.php?option=com_eventbooking&Itemid=' . $Itemid), JText::_('EB_INVALID_ACTION'));
		}

		if ($rowEvent->number_days < 0)
		{
			$msg = JText::sprintf('EB_CANCEL_DATE_PASSED', JHtml::_('date', $rowEvent->cancel_before_date, EventbookingHelper::getConfigValue('date_format'), null));
			$app->redirect(JRoute::_('index.php?option=com_eventbooking&Itemid=' . $Itemid), $msg);
		}

		if ($registrationCode)
		{
			$id = $rowEvent->registrant_id;
		}

		$model = $this->getModel('register');
		$model->cancelRegistration($id);
		$this->setRedirect(JRoute::_('index.php?option=com_eventbooking&view=registrationcancel&id=' . $id . '&Itemid=' . $Itemid, false));
	}

	/**
	 * Cancel editing a registration record
	 */
	public function cancel_edit()
	{
		$return = base64_decode($this->input->getString('return', ''));
		if ($return)
		{
			$this->setRedirect($return);
		}
		else
		{
			$this->setRedirect(JRoute::_(EventbookingHelperRoute::getViewRoute('registrants', $this->input->getInt('Itemid')), false));
		}
	}

	/**
	 * Download invoice associated to the registration record
	 *
	 * @throws Exception
	 */
	public function download_invoice()
	{
		$user = JFactory::getUser();
		if (!$user->id)
		{
			JFactory::getApplication()->redirect('index.php', JText::_('You do not have permission to download the invoice'));
		}

		$id = $this->input->getInt('id', 0);
		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_eventbooking/table');
		$row = JTable::getInstance('eventbooking', 'Registrant');
		$row->load($id);
		$canDownload = false;

		if ($row->user_id == $user->id)
		{
			$canDownload = true;
		}

		if (!$canDownload)
		{
			if ($user->authorise('eventbooking.registrants_management', 'com_eventbooking'))
			{
				$config = EventbookingHelper::getConfig();
				if ($config->only_show_registrants_of_event_owner)
				{
					$db = JFactory::getDbo();
					$query = $db->getQuery(true);
					$query->select('created_by')
						->from('#__eb_events')
						->where('id = '. $row->event_id);
					$db->setQuery($query);
					$createdBy = $db->loadResult();
					if ($createdBy == $user->id)
					{
						$canDownload = true;
					}
				}
				else
				{
					$canDownload = true;
				}
			}
		}


		if (!$canDownload)
		{
			JFactory::getApplication()->redirect('index.php', JText::_('You do not have permission to download the invoice'));
		}

		EventbookingHelper::downloadInvoice($id);
	}

	/**
	 * Export registrants data into a csv file
	 */
	public function export()
	{
		$db          = JFactory::getDbo();
		$query       = $db->getQuery(true);
		$config      = EventbookingHelper::getConfig();
		$fieldSuffix = EventbookingHelper::getFieldSuffix();
		$eventId     = $this->input->getInt('event_id', 0);

		if (!EventbookingHelper::canExportRegistrants($eventId))
		{
			JFactory::getApplication()->redirect('index.php', JText::_('EB_NOT_ALLOWED_TO_EXPORT'));
		}

		if (!$eventId)
		{
			JFactory::getApplication()->redirect('index.php', JText::_('EB_PLEASE_CHOOSE_AN_EVENT_TO_EXPORT_REGISTRANTS'));
		}

		$query->select('a.*, b.event_date')
			->select(' b.title' . $fieldSuffix . ' AS event_title')
			->from('#__eb_registrants AS a')
			->innerJoin('#__eb_events AS b ON a.event_id = b.id')
			->order('a.id');

		if ($config->show_coupon_code_in_registrant_list)
		{
			$query->select('c.code AS coupon_code')
				->leftJoin('#__eb_coupons AS c ON a.coupon_id=c.id');
		}

		$query->where('(a.published = 1 OR (a.payment_method LIKE "os_offline%" AND a.published NOT IN (2,3)))')
			->where('a.event_id = ' . $eventId);

		if (!$config->get('include_group_billing_in_csv_export', 1))
		{
			$query->where('a.is_group_billing = 0');
		}

		if (!$config->include_group_members_in_csv_export)
		{
			$query->where('a.group_id = 0');
		}


		$db->setQuery($query);
		$rows = $db->loadObjectList();

		if (count($rows) == 0)
		{
			JFactory::getApplication()->redirect('index.php', JText::_('EB_NO_REGISTRANTS_TO_EXPORT'));
		}

		if ($eventId)
		{
			if ($config->custom_field_by_category)
			{
				$query->clear();
				$query->select('category_id')
					->from('#__eb_event_categories')
					->where('event_id=' . $eventId)
					->where('main_category=1');
				$db->setQuery($query);
				$categoryId = (int) $db->loadResult();

				$query->clear();
				$query->select('id, name, title, is_core')
					->from('#__eb_fields')
					->where('published = 1')
					->where('(category_id = -1 OR id IN (SELECT field_id FROM #__eb_field_categories WHERE category_id=' . $categoryId . '))')
					->order('ordering');
			}
			else
			{
				$query->clear();
				$query->select('id, name, title, is_core')
					->from('#__eb_fields')
					->where('published = 1')
					->where('(event_id = -1 OR id IN (SELECT field_id FROM #__eb_field_events WHERE event_id=' . $eventId . '))')
					->order('ordering');
			}
		}
		else
		{
			$query->clear();
			$query->select('id, name, title, is_core')
				->from('#__eb_fields')
				->where('published = 1')
				->order('ordering');
		}
		$db->setQuery($query);
		$rowFields = $db->loadObjectList();

		$registrantIds = array(0);

		//Get name of groups
		$groupNames = array();
		if (count($rows))
		{
			foreach ($rows as $row)
			{
				$registrantIds[] = $row->id;
				if ($row->is_group_billing)
				{
					$groupNames[$row->id] = $row->first_name . ' ' . $row->last_name;
				}
			}
		}

		//Get the custom fields value and store them into an array
		$query->clear();
		$query->select('registrant_id, field_id, field_value')
			->from('#__eb_field_values')
			->where('registrant_id IN (' . implode(',', $registrantIds) . ')');
		$db->setQuery($query);
		$rowFieldValues = $db->loadObjectList();
		$fieldValues    = array();
		for ($i = 0, $n = count($rowFieldValues); $i < $n; $i++)
		{
			$rowFieldValue                                                        = $rowFieldValues[$i];
			$fieldValues[$rowFieldValue->registrant_id][$rowFieldValue->field_id] = $rowFieldValue->field_value;
		}

		EventbookingHelperData::csvExport($rows, $config, $rowFields, $fieldValues, $groupNames);
	}
}