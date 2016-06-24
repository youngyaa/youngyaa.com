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

class EventbookingViewInviteHtml extends RADViewHtml
{
	/**
	 * Display invitation form for an event
	 *
	 * @throws Exception
	 */
	public function display()
	{
		$layout = $this->getLayout();
		if ($layout == 'complete')
		{
			$this->displayInviteComplete();
		}
		else
		{
			$user        = JFactory::getUser();
			$config      = EventbookingHelper::getConfig();
			$message     = EventbookingHelper::getMessages();
			$fieldSuffix = EventbookingHelper::getFieldSuffix();
			if (strlen(trim(strip_tags($message->{'invitation_form_message' . $fieldSuffix}))))
			{
				$inviteMessage = $message->{'invitation_form_message' . $fieldSuffix};
			}
			else
			{
				$inviteMessage = $message->invitation_form_message;
			}
			$showCaptcha = 0;
			if ($config->enable_captcha && ($user->id == 0 || $config->bypass_captcha_for_registered_user !== '1'))
			{
				$captchaPlugin = JFactory::getApplication()->getParams()->get('captcha', JFactory::getConfig()->get('captcha'));
				if (!$captchaPlugin)
				{
					// Hardcode to recaptcha, reduce support request
					$captchaPlugin = 'recaptcha';
				}
				$plugin = JPluginHelper::getPlugin('captcha', $captchaPlugin);
				if ($plugin)
				{
					$showCaptcha         = 1;
					$this->captcha       = JCaptcha::getInstance($captchaPlugin)->display('dynamic_recaptcha_1', 'dynamic_recaptcha_1', 'required');
					$this->captchaPlugin = $captchaPlugin;
				}
				else
				{
					JFactory::getApplication()->enqueueMessage(JText::_('EB_CAPTCHA_NOT_ACTIVATED_IN_YOUR_SITE'), 'error');
				}
			}

			$eventId = $this->input->getInt('id');
			$name    = $this->input->getString('name');
			if (empty($name))
			{
				$name = $user->get('name');
			}
			$this->event           = EventbookingHelperDatabase::getEvent($eventId);
			$this->name            = $name;
			$this->inviteMessage   = $inviteMessage;
			$this->showCaptcha     = $showCaptcha;
			$this->friendNames     = $this->input->getString('friend_names');
			$this->friendEmails    = $this->input->getString('friend_emails');
			$this->mesage          = $this->input->getString('message');
			$this->bootstrapHelper = new EventbookingHelperBootstrap($config->twitter_bootstrap_version);

			parent::display();
		}
	}

	/**
	 * Display invitation complete message
	 */
	private function displayInviteComplete()
	{
		$message     = EventbookingHelper::getMessages();
		$fieldSuffix = EventbookingHelper::getFieldSuffix();
		if (strlen(trim(strip_tags($message->{'invitation_complete' . $fieldSuffix}))))
		{
			$this->message = $message->{'invitation_complete' . $fieldSuffix};
		}
		else
		{
			$this->message = $message->invitation_complete;
		}

		parent::display();
	}
}