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

class EventbookingControllerEvent extends EventbookingController
{
	public function __construct(RADInput $input = null, array $config = array())
	{
		parent::__construct($input, $config);

		$this->registerTask('unpublish', 'publish');
	}

	/**
	 * Save an event
	 */
	public function save()
	{
		$this->csrfProtection();
		$model = $this->getModel('event');
		try
		{
			$model->store($this->input);
			$msg = JText::_('Successfully saving event');
		}
		catch (Exception $e)
		{
			$msg = JText::_('Error while saving event:' . $e->getMessage());
		}

		$return = base64_decode($this->input->getString('return'));
		if ($return)
		{
			$this->setRedirect($return);
		}
		else
		{
			$this->setRedirect(JRoute::_(EventbookingHelperRoute::getViewRoute('events', $this->input->getInt('Itemid')), false), $msg);
		}
	}

	/**
	 * Publish the selected events
	 *
	 */
	public function publish()
	{
		$id = $this->input->getInt('id', 0);
		if (!EventbookingHelper::canChangeEventStatus($id))
		{
			$msg = JText::_('EB_NO_PUBLISH_PERMISSION');
			$this->setRedirect(JRoute::_(EventbookingHelperRoute::getViewRoute('events', $this->input->getInt('Itemid', 0)), false), $msg);

			return;
		}

		//OK, enough permission checked. Change status of the event
		$task = $this->getTask();
		if ($task == 'publish')
		{
			$msg   = JText::_('EB_PUBLISH_SUCCESS');
			$state = 1;
		}
		else
		{
			$msg   = JText::_('EB_UNPUBLISH_SUCCESS');
			$state = 0;
		}
		$model = $this->getModel('event');
		$model->publish($id, $state);

		$return = base64_decode($this->input->getString('return'));
		if ($return)
		{
			$this->setRedirect($return);
		}
		else
		{
			$this->setRedirect(JRoute::_(EventbookingHelperRoute::getViewRoute('events', $this->input->getInt('Itemid', 0)), false), $msg);
		}
	}

	/**
	 * Send invitation to friends
	 * @return void|boolean
	 */
	public function send_invite()
	{
		$this->csrfProtection();
		$config = EventbookingHelper::getConfig();
		if ($config->show_invite_friend)
		{
			$config = EventbookingHelper::getConfig();
			$user   = JFactory::getUser();
			if ($config->enable_captcha && ($user->id == 0 || $config->bypass_captcha_for_registered_user !== '1'))
			{
				$captchaValid  = true;
				$input         = $this->input;
				$captchaPlugin = $this->app->getParams()->get('captcha', JFactory::getConfig()->get('captcha'));
				if (!$captchaPlugin)
				{
					// Hardcode to recaptcha, reduce support request
					$captchaPlugin = 'recaptcha';
				}
				$plugin = JPluginHelper::getPlugin('captcha', $captchaPlugin);
				if ($plugin)
				{
					$captchaValid = JCaptcha::getInstance($captchaPlugin)->checkAnswer($input->post->get('recaptcha_response_field', '', 'string'));
				}
				if (!$captchaValid)
				{
					$this->app->enqueueMessage(JText::_('EB_INVALID_CAPTCHA_ENTERED'), 'warning');
					$this->input->set('view', 'invite');
					$this->input->set('layout', 'default');
					$this->display();

					return;
				}
			}
			$model = $this->getModel('invite');
			$post  = $this->input->post->getData();
			$model->sendInvite($post);
			$this->setRedirect(
				JRoute::_('index.php?option=com_eventbooking&view=invite&layout=complete&tmpl=component&Itemid=' . $this->input->getInt('Itemid', 0),
					false));
		}
		else
		{
			JError::raiseError(403, JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'));
		}
	}

	/**
	 * Download Ical
	 */
	public function download_ical()
	{
		$eventId = $this->input->getInt('event_id');
		if ($eventId)
		{
			$config      = EventbookingHelper::getConfig();
			$event       = EventbookingHelperDatabase::getEvent($eventId);
			$rowLocation = EventbookingHelperDatabase::getLocation($event->location_id);
			if ($config->from_name)
			{
				$fromName = $config->from_name;
			}
			else
			{
				$fromName = JFactory::getConfig()->get('from_name');
			}
			if ($config->from_email)
			{
				$fromEmail = $config->from_email;
			}
			else
			{
				$fromEmail = JFactory::getConfig()->get('mailfrom');
			}

			$ics = new EventbookingHelperIcs();
			$ics->setName($event->title)
				->setDescription($event->short_description)
				->setOrganizer($fromEmail, $fromName)
				->setStart($event->event_date)
				->setEnd($event->event_end_date);

			if ($rowLocation)
			{
				$ics->setLocation($rowLocation->name);
			}

			$ics->download();
		}
	}

	/**
	 * Redirect user to events mangement page
	 *
	 */
	public function cancel()
	{
		$return = base64_decode($this->input->getString('return'));
		if ($return)
		{
			$this->setRedirect($return);
		}
		else
		{
			$this->setRedirect(JRoute::_('index.php?option=com_eventbooking&view=events&Itemid=' . $this->input->getInt('Itemid', 0), false));
		}
	}
}