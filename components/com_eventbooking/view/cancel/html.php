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

class EventbookingViewCancelHtml extends RADViewHtml
{
	public $hasModel = false;

	public function display()
	{
		$this->setLayout('default');
		$db          = JFactory::getDbo();
		$query       = $db->getQuery(true);
		$id          = $this->input->getInt('id');
		$message     = EventbookingHelper::getMessages();
		$fieldSuffix = EventbookingHelper::getFieldSuffix();

		$query->select('b.title' . $fieldSuffix . ' AS event_title')
			->from('#__eb_registrants AS a')
			->innerJoin('#__eb_events AS b ON a.event_id = b.id')
			->where('a.id=' . $id);
		$db->setQuery($query);
		$eventTitle = $db->loadResult();


		if (strlen(trim(strip_tags($message->{'cancel_message' . $fieldSuffix}))))
		{
			$cancelMessage = $message->{'cancel_message' . $fieldSuffix};
		}
		else
		{
			$cancelMessage = $message->cancel_message;
		}

		$cancelMessage = str_replace('[EVENT_TITLE]', $eventTitle, $cancelMessage);
		$this->message = $cancelMessage;

		parent::display();
	}
}