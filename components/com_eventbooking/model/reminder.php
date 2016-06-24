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

class EventBookingModelReminder extends JModelLegacy
{

	/**
	 * Constructor function
	 */
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Send reminder
	 */
	public static function sendReminder($numberEmailSendEachTime = 0)
	{
		$db     = JFactory::getDbo();
		$config = EventbookingHelper::getConfig();
		if (!$numberEmailSendEachTime)
		{
			$numberEmailSendEachTime = 15;
		}
		if ($config->from_name)
		{
			$fromName = $config->from_name;
		}
		else
		{
			$fromName = JFactory::getConfig()->get('fromname');
		}
		if ($config->from_email)
		{
			$fromEmail = $config->from_email;
		}
		else
		{
			$fromEmail = JFactory::getConfig()->get('mailfrom');
		}
		$eventFields = array('b.id as event_id', 'b.event_date');
		if (JLanguageMultilang::isEnabled())
		{
			$languages = EventbookingHelper::getLanguages();
			if (count($languages))
			{
				foreach ($languages as $language)
				{
					$eventFields[] = 'b.title_' . $language->sef;
				}
			}
			else
			{
				$eventFields[] = 'b.title';
			}
		}
		else
		{
			$eventFields[] = 'b.title';
		}
		$sql = 'SELECT a.id, a.first_name, a.last_name, a.email, a.register_date, a.transaction_id, a.language, ' . implode(',', $eventFields) .
			' FROM #__eb_registrants AS a INNER JOIN #__eb_events AS b ' . ' ON a.event_id = b.id ' .
			' WHERE a.published=1 AND a.is_reminder_sent = 0 AND b.enable_auto_reminder=1 AND (DATEDIFF(b.event_date, NOW()) <= b.remind_before_x_days) AND (DATEDIFF(b.event_date, NOW()) >=0) ORDER BY b.event_date, a.register_date ' .
			' LIMIT ' . $numberEmailSendEachTime;
		$db->setQuery($sql);
		$rows    = $db->loadObjectList();
		$message = EventbookingHelper::getMessages();
		$mailer  = JFactory::getMailer();
		for ($i = 0, $n = count($rows); $i < $n; $i++)
		{
			$row         = $rows[$i];
			$fieldSuffix = EventbookingHelper::getFieldSuffix($row->language);
			if (strlen($message->{'reminder_email_subject' . $fieldSuffix}))
			{
				$emailSubject = $message->{'reminder_email_subject' . $fieldSuffix};
			}
			else
			{
				$emailSubject = $message->reminder_email_subject;
			}
			$emailSubject = str_replace('[EVENT_TITLE]', $row->title . $fieldSuffix, $emailSubject);
			if (strlen($message->{'reminder_email_body' . $fieldSuffix}))
			{
				$emailBody = $message->{'reminder_email_body' . $fieldSuffix};
			}
			else
			{
				$emailBody = $message->reminder_email_body;
			}
			$replaces                = array();
			$replaces['event_date']  = JHtml::_('date', $row->event_date, $config->event_date_format, null);
			$replaces['first_name']  = $row->first_name;
			$replaces['last_name']   = $row->last_name;
			$replaces['event_title'] = $row->event_title;
			foreach ($replaces as $key => $value)
			{
				$emailBody = str_replace('[' . strtoupper($key) . ']', $value, $emailBody);
			}
			$emailBody = EventbookingHelper::convertImgTags($emailBody);
			$mailer->sendMail($fromEmail, $fromName, $row->email, $emailSubject, $emailBody, 1);
			$mailer->ClearAllRecipients();

			//Mark this registrant as received reminder
			$sql = 'UPDATE #__eb_registrants SET is_reminder_sent = 1 WHERE id = ' . (int) $row->id;
			$db->setQuery($sql);
			$db->execute();
		}
	}
} 