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

class EventbookingViewRegistrationcancelHtml extends RADViewHtml
{
	public $hasModel = false;

	public function display()
	{
		$this->setLayout('default');

		$db          = JFactory::getDbo();
		$query       = $db->getQuery(true);
		$message     = EventbookingHelper::getMessages();
		$fieldSuffix = EventbookingHelper::getFieldSuffix();
		$id          = $this->input->getInt('id', 0);
		$query->select('a.*, b.title' . $fieldSuffix . ' AS event_title')
			->from('#__eb_registrants AS a')
			->innerJoin('#__eb_events AS b ON a.event_id = b.id')
			->where('a.id=' . $id);
		$db->setQuery($query);
		$rowRegistrant = $db->loadObject();

		if (!$rowRegistrant)
		{
			JFactory::getApplication()->redirect('index.php', JText::_('EB_INVALID_REGISTRATION_CODE'));
		}

		if ($rowRegistrant->amount > 0)
		{
			if (strlen(trim(strip_tags($message->{'registration_cancel_message_paid' . $fieldSuffix}))))
			{
				$cancelMessage = $message->{'registration_cancel_message_paid' . $fieldSuffix};
			}
			else
			{
				$cancelMessage = $message->registration_cancel_message_paid;
			}
		}
		else
		{
			if (strlen(trim(strip_tags($message->{'registration_cancel_message_free' . $fieldSuffix}))))
			{
				$cancelMessage = $message->{'registration_cancel_message_free' . $fieldSuffix};
			}
			else
			{
				$cancelMessage = $message->registration_cancel_message_free;
			}
		}

		$cancelMessage = str_replace('[EVENT_TITLE]', $rowRegistrant->event_title, $cancelMessage);
		$this->message = $cancelMessage;

		parent::display();
	}
}