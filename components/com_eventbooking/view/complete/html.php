<?php
/**
 * @version        	2.0.0
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2015 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die();

class EventbookingViewCompleteHtml extends RADViewHtml
{
	public $hasModel = false;
	function display()
	{
		//Hardcoded the layout, it happens with some clients. Maybe it is a bug of Joomla core code, will find out it later
		$this->setLayout('default');
		$db               = JFactory::getDbo();
		$query            = $db->getQuery(true);
		$config           = EventbookingHelper::getConfig();			
		// Try to get it from session
		$registrationCode = JFactory::getSession()->get('eb_registration_code', '');		
		if (empty($registrationCode))
		{
			$registrationCode = JRequest::getVar('registration_code');
		}
		if ($registrationCode)
		{
			$sql = 'SELECT id FROM #__eb_registrants WHERE registration_code="' . $registrationCode . '" ORDER BY id LIMIT 1 ';
			$db->setQuery($sql);
			$id = (int) $db->loadResult();
		}
		else
		{
			JFactory::getApplication()->redirect('index.php', JText::_('EB_INVALID_REGISTRATION_CODE'));
		}
		if (!$id)
		{
			JFactory::getApplication()->redirect('index.php', JText::_('EB_INVALID_REGISTRATION_CODE'));
		}
		$fieldSuffix = EventbookingHelper::getFieldSuffix();
		$query->select('a.*, a.title' . $fieldSuffix . ' AS title, b.payment_method')
			->from('#__eb_events  AS a ')
			->innerJoin('#__eb_registrants AS b ON a.id = b.event_id')
			->where('b.id=' . $id);
		$db->setQuery($query);
		$rowEvent    = $db->loadObject();
		$message     = EventbookingHelper::getMessages();
		//Override thanks message
		if (strlen(trim(strip_tags($rowEvent->thanks_message))))
		{
			$config->thanks_message = $rowEvent->thanks_message;
		}
		if (strlen(trim(strip_tags($rowEvent->thanks_message_offline))))
		{
			$config->thanks_message_offline = $rowEvent->thanks_message_offline;
		}
		if (strpos($rowEvent->payment_method, 'os_offline') !== false)
		{
			if (strlen(trim(strip_tags($rowEvent->thanks_message_offline))))
			{
				$thankMessage = $rowEvent->thanks_message_offline;
			}
			elseif (strlen(trim(strip_tags($message->{'thanks_message_offline' . $fieldSuffix}))))
			{
				$thankMessage = $message->{'thanks_message_offline' . $fieldSuffix};
			}
			else
			{
				$thankMessage = $message->thanks_message_offline;
			}
		}
		else
		{
			if (strlen(trim(strip_tags($rowEvent->thanks_message))))
			{
				$thankMessage = $rowEvent->thanks_message;
			}
			elseif (strlen(trim(strip_tags($message->{'thanks_message' . $fieldSuffix}))))
			{
				$thankMessage = $message->{'thanks_message' . $fieldSuffix};
			}
			else
			{
				$thankMessage = $message->thanks_message;
			}
		}
		$query->clear();
		$query->select('*')
			->from('#__eb_registrants')
			->where('id=' . $id);
		$db->setQuery($query);
		$rowRegistrant = $db->loadObject();
		if ($config->multiple_booking)
		{
			$rowFields = EventbookingHelper::getFormFields($rowRegistrant->id, 4);
		}
		elseif (EventbookingHelper::isGroupRegistration($rowRegistrant->id))
		{
			$rowFields = EventbookingHelper::getFormFields($rowEvent->id, 1);
		}
		else
		{
			$rowFields = EventbookingHelper::getFormFields($rowEvent->id, 0);
		}
		$form = new RADForm($rowFields);
		$data = EventbookingHelper::getRegistrantData($rowRegistrant, $rowFields);
		$form->bind($data);
		$form->buildFieldsDependency();
		$replaces = EventbookingHelper::buildTags($rowRegistrant, $form, $rowEvent, $config, false);
		foreach ($replaces as $key => $value)
		{
			$key          = strtoupper($key);
			$thankMessage = str_replace("[$key]", $value, $thankMessage);
		}
		$this->message          = $thankMessage;
		$this->registrationCode = $registrationCode;
		$this->tmpl             = JRequest::getVar('tmpl', '');
		$this->Itemid           = JRequest::getInt('Itemid', 0);
		$this->conversionTrackingCode = $config->conversion_tracking_code;

		parent::display();
	}
}