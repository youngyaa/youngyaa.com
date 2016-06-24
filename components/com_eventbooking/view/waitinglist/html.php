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

class EventbookingViewWaitinglistHtml extends RADViewHtml
{

	/**
	 * Display interface to user
	 *
	 * @throws Exception
	 */
	public function display()
	{
		$db     = JFactory::getDbo();
		$query  = $db->getQuery(true);
		$config = EventbookingHelper::getConfig();

		$registrationCode = JFactory::getSession()->get('eb_registration_code', '');
		$query->select('*')
			->from('#__eb_registrants')
			->where('registration_code = ' . $db->quote($registrationCode));
		$db->setQuery($query);
		$rowRegistrant = $db->loadObject();
		if (!$rowRegistrant)
		{
			JFactory::getApplication()->redirect('index.php', JText::_('EB_INVALID_REGISTRATION_CODE'));
		}

		$rowEvent    = EventbookingHelperDatabase::getEvent($rowRegistrant->event_id);
		$message     = EventbookingHelper::getMessages();
		$fieldSuffix = EventbookingHelper::getFieldSuffix();

		if (strlen(strip_tags($message->{'waitinglist_complete_message' . $fieldSuffix})))
		{
			$msg = $message->{'waitinglist_complete_message' . $fieldSuffix};
		}
		else
		{
			$msg = $message->waitinglist_complete_message;
		}

		if (EventbookingHelper::isGroupRegistration($rowRegistrant->id))
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
		$replaces = EventbookingHelper::buildTags($rowRegistrant, $form, $rowEvent, $config);
		foreach ($replaces as $key => $value)
		{
			$key = strtoupper($key);
			$msg = str_replace("[$key]", $value, $msg);
		}
		$this->message = $msg;

		parent::display();
	}
}