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

class EventbookingControllerRegister extends EventbookingController
{

	/**
	 * Check the entered event password and make sure the entered password is valid
	 */
	public function check_event_password()
	{
		$password = $this->input->get('password', '', 'none');
		$eventId  = $this->input->getInt('event_id', 0);
		$return   = $this->input->get('return', '', 'none');
		$model    = $this->getModel('Register');
		$success  = $model->checkPassword($eventId, $password);
		if ($success)
		{
			JFactory::getSession()->set('eb_passowrd_' . $eventId, 1);
			$this->setRedirect(base64_decode($return));
		}
		else
		{
			// Redirect back to password view
			$Itemid = $this->input->getInt('Itemid');
			$url    = JRoute::_('index.php?option=com_eventbooking&view=password&event_id=' . $eventId . '&return=' . $return . '&Itemid=' . $Itemid, false);
			$this->setMessage(JText::_('EB_INVALID_EVENT_PASSWORD'), 'error');
			$this->setRedirect($url);
		}
	}

	/**
	 * Display individual registration form
	 *
	 * @throws Exception
	 */
	public function individual_registration()
	{
		$user    = JFactory::getUser();
		$config  = EventbookingHelper::getConfig();
		$db      = JFactory::getDbo();
		$query   = $db->getQuery(true);
		$eventId = $this->input->getInt('event_id');
		if (!$eventId)
		{
			return;
		}
		$event = EventbookingHelperDatabase::getEvent($eventId);
		if (!$event)
		{
			return;
		}
		if ($event->event_password)
		{
			$passwordPassed = JFactory::getSession()->get('eb_passowrd_' . $event->id, 0);
			if (!$passwordPassed)
			{
				$return = base64_encode(JUri::getInstance()->toString());
				JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_eventbooking&view=password&event_id=' . $event->id . '&return=' . $return . '&Itemid=' . $this->input->getInt('Itemid', 0), false));
			}
		}

		// Check to see if the event is a paid event
		if ($config->custom_field_by_category)
		{
			$query->select('category_id')
				->from('#__eb_event_categories')
				->where('event_id=' . $event->id)
				->where('main_category=1');
			$db->setQuery($query);
			$categoryId = (int) $db->loadResult();
			$query->clear();
			$query->select('COUNT(id)')
				->from('#__eb_fields')
				->where('published=1 AND fee_field=1 AND (category_id = -1 OR id IN (SELECT field_id FROM #__eb_field_categories WHERE category_id=' . $categoryId . '))');
			$db->setQuery($query);
			$total = (int) $db->loadResult();
		}
		else
		{
			$query->select('COUNT(id)')
				->from('#__eb_fields')
				->where('published=1 AND fee_field=1 AND (event_id = -1 OR id IN (SELECT field_id FROM #__eb_field_events WHERE event_id=' . $eventId . '))');
			$db->setQuery($query);
			$total = (int) $db->loadResult();
		}

		if ($config->simply_registration_process && $event->individual_price == 0 && $total == 0 && $user->id)
		{
			$rowFields = EventbookingHelper::getFormFields($eventId, 0);
			$data      = EventbookingHelper::getFormData($rowFields, $eventId, $user->id, $config);
			$name      = $user->name;
			$pos       = strpos($name, ' ');
			if ($pos !== false)
			{
				$data['first_name'] = substr($name, 0, $pos);
				$data['last_name']  = substr($name, $pos + 1);
			}
			else
			{
				$data['first_name'] = $name;
			}
			$data['email']    = $user->email;
			$data['event_id'] = $eventId;
			$model            = $this->getModel('Register');
			$return = $model->processIndividualRegistration($data);
			if ($return === 1)
			{
				// Redirect registrants to registration complete page
				$this->setRedirect(JRoute::_('index.php?option=com_eventbooking&view=complete&Itemid=' . $this->input->getInt('Itemid'), false, false));
			}
			elseif ($return === 2)
			{
				// Redirect to waiting list complete page
				$this->setRedirect(JRoute::_('index.php?option=com_eventbooking&view=waitinglist&Itemid=' . $this->input->getInt('Itemid'), false, false));
			}
		}
		else
		{
			$this->input->set('view', 'register');
			$this->input->set('layout', 'default');
			$this->display();
		}
	}

	/**
	 * Process individual registration
	 */
	public function process_individual_registration()
	{
		$app     = JFactory::getApplication();
		$session = JFactory::getSession();
		$input   = $this->input;
		$eventId = $input->getInt('event_id', 0);
		if (!$eventId)
		{
			return;
		}
		$event = EventbookingHelperDatabase::getEvent($eventId);
		if (!$event)
		{
			return;
		}

		$user         = JFactory::getUser();
		$config       = EventbookingHelper::getConfig();
		$emailValid   = true;
		$captchaValid = true;

		// Check email
		$result = $this->validateRegistrantEmail($eventId, $input->get('email', '', 'none'));

		if (!$result['success'])
		{
			$emailValid = false;
		}
		else
		{
			if ($config->enable_captcha && ($user->id == 0 || $config->bypass_captcha_for_registered_user !== '1'))
			{
				$captchaPlugin = $app->getParams()->get('captcha', JFactory::getConfig()->get('captcha'));
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
			}
		}

		if (!$emailValid || !$captchaValid)
		{
			// Enqueue the error message
			if (!$emailValid)
			{
				$app->enqueueMessage($result['message'], 'warning');
			}
			else
			{
				$app->enqueueMessage(JText::_('EB_INVALID_CAPTCHA_ENTERED'), 'warning');
			}

			$fromArticle = $input->post->getInt('from_article', 0);
			if ($fromArticle)
			{
				$formData = $input->post->getData();
				$session->set('eb_form_data', serialize($formData));
				$session->set('eb_catpcha_invalid', 1);
				$app->redirect($session->get('eb_artcile_url'));

				return;
			}
			else
			{
				$input->set('captcha_invalid', 1);
				$this->execute('individual_registration');

				return;
			}
		}
		$session->clear('eb_catpcha_invalid');
		$data   = $input->post->getData();
		$model  = $this->getModel('Register');
		$return = $model->processIndividualRegistration($data);

		if ($return === 1)
		{
			// Redirect registrants to registration complete page
			$this->setRedirect(JRoute::_('index.php?option=com_eventbooking&view=complete&Itemid=' . $this->input->getInt('Itemid'), false, false));
		}
		elseif ($return === 2)
		{
			// Redirect to waiting list complete page
			$this->setRedirect(JRoute::_('index.php?option=com_eventbooking&view=waitinglist&Itemid=' . $this->input->getInt('Itemid'), false, false));
		}
	}

	/**
	 * Store number of registrants and return form allow entering group members information
	 */
	public function store_number_registrants()
	{
		$config  = EventbookingHelper::getConfig();
		$session = JFactory::getSession();
		$session->set('eb_number_registrants', $this->input->getInt('number_registrants'));
		if ($config->collect_member_information)
		{
			$this->input->set('view', 'register');
			$this->input->set('layout', 'group_members');
		}
		else
		{
			$this->input->set('view', 'register');
			$this->input->set('layout', 'group_billing');
		}
		$this->display();
	}

	/**
	 * Store group members data and display group billing form
	 */
	public function store_group_members_data()
	{
		$membersData = $this->input->post->getData();
		$session     = JFactory::getSession();
		$session->set('eb_group_members_data', serialize($membersData));
		$eventId         = $this->input->getInt('event_id', 0);
		$showBillingStep = EventbookingHelper::showBillingStep($eventId);
		if (!$showBillingStep)
		{
			$this->process_group_registration();
		}
		else
		{
			$this->input->set('view', 'register');
			$this->input->set('layout', 'group_billing');
			$this->display();
		}
	}

	/**
	 * Process group registration
	 */
	public function process_group_registration()
	{
		$app     = JFactory::getApplication();
		$session = JFactory::getSession();
		$input   = $this->input;
		$eventId = $input->getInt('event_id');
		if (!$eventId)
		{
			return;
		}
		$event = EventbookingHelperDatabase::getEvent($eventId);

		if (!$event)
		{
			return;
		}
		$config = EventbookingHelper::getConfig();
		$user   = JFactory::getUser();

		$emailValid   = true;
		$captchaValid = true;

		// Check email
		$result = $this->validateRegistrantEmail($eventId, $input->get('email', '', 'none'));

		if (!$result['success'])
		{
			$emailValid = false;
		}
		else
		{
			if ($config->enable_captcha && ($user->id == 0 || $config->bypass_captcha_for_registered_user !== '1'))
			{
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
			}
		}

		if (!$emailValid || !$captchaValid)
		{
			// Enqueue the error message
			if (!$emailValid)
			{
				$app->enqueueMessage($result['message'], 'warning');
			}
			else
			{
				$app->enqueueMessage(JText::_('EB_INVALID_CAPTCHA_ENTERED'), 'warning');
			}

			$data = $input->post->getData();
			$session->set('eb_group_billing_data', serialize($data));
			$input->set('captcha_invalid', 1);
			$input->set('view', 'register');
			$input->set('layout', 'group');
			$this->display();

			return;
		}

		// Check to see if there is a valid number registrants
		$numberRegistrants = (int) $session->get('eb_number_registrants', '');
		if (!$numberRegistrants)
		{
			// Session was lost for some reasons, users will have to start over again
			if ($config->use_https)
			{
				$ssl = 1;
			}
			else
			{
				$ssl = 0;
			}
			$signupUrl = JRoute::_('index.php?option=com_eventbooking&task=register.group_registration&event_id=' . $eventId . '&Itemid=' . $input->getInt('Itemid', 0), false, $ssl);
			$app->redirect($signupUrl, JText::_('Sorry, your session was expired. Please try again!'));
		}

		$data   = $input->post->getData();
		$model  = $this->getModel('Register');
		$return = $model->processGroupRegistration($data);
		if ($return === 1)
		{
			// Redirect registrants to registration complete page
			$this->setRedirect(JRoute::_('index.php?option=com_eventbooking&view=complete&Itemid=' . $this->input->getInt('Itemid'), false, false));
		}
		elseif ($return === 2)
		{
			// Redirect to waiting list complete page
			$this->setRedirect(JRoute::_('index.php?option=com_eventbooking&view=waitinglist&Itemid=' . $this->input->getInt('Itemid'), false, false));
		}
	}

	/**
	 * Calculate registration fee, then update the information on registration form
	 */
	function calculate_individual_registration_fee()
	{
		$config        = EventbookingHelper::getConfig();
		$eventId       = $this->input->getInt('event_id', 0);
		$data          = $this->input->post->getData();
		$paymentMethod = $this->input->getString('payment_method', '');
		$event         = EventbookingHelperDatabase::getEvent($eventId);
		$rowFields     = EventbookingHelper::getFormFields($eventId, 0);
		$form          = new RADForm($rowFields);
		$form->bind($data);
		$fees = EventbookingHelper::calculateIndividualRegistrationFees($event, $form, $data, $config, $paymentMethod);

		$response                           = array();
		$response['total_amount']           = EventbookingHelper::formatAmount($fees['total_amount'], $config);
		$response['discount_amount']        = EventbookingHelper::formatAmount($fees['discount_amount'], $config);
		$response['tax_amount']             = EventbookingHelper::formatAmount($fees['tax_amount'], $config);
		$response['payment_processing_fee'] = EventbookingHelper::formatAmount($fees['payment_processing_fee'], $config);
		$response['amount']                 = EventbookingHelper::formatAmount($fees['amount'], $config);
		$response['deposit_amount']         = EventbookingHelper::formatAmount($fees['deposit_amount'], $config);
		$response['coupon_valid']           = $fees['coupon_valid'];

		echo json_encode($response);
		JFactory::getApplication()->close();
	}

	/**
	 * Calculate registration fee, then update information on group registration form
	 */
	function calculate_group_registration_fee()
	{
		$config        = EventbookingHelper::getConfig();
		$eventId       = $this->input->getInt('event_id');
		$data          = $this->input->post->getData();
		$paymentMethod = $this->input->getString('payment_method', '');

		$event = EventbookingHelperDatabase::getEvent($eventId);

		$rowFields = EventbookingHelper::getFormFields($eventId, 1);
		$form      = new RADForm($rowFields);
		$form->bind($data);

		$fees = EventbookingHelper::calculateGroupRegistrationFees($event, $form, $data, $config, $paymentMethod);

		$response                           = array();
		$response['total_amount']           = EventbookingHelper::formatAmount($fees['total_amount'], $config);
		$response['discount_amount']        = EventbookingHelper::formatAmount($fees['discount_amount'], $config);
		$response['tax_amount']             = EventbookingHelper::formatAmount($fees['tax_amount'], $config);
		$response['payment_processing_fee'] = EventbookingHelper::formatAmount($fees['payment_processing_fee'], $config);
		$response['amount']                 = EventbookingHelper::formatAmount($fees['amount'], $config);
		$response['deposit_amount']         = EventbookingHelper::formatAmount($fees['deposit_amount'], $config);
		$response['coupon_valid']           = $fees['coupon_valid'];
		echo json_encode($response);
		$this->app->close();
	}

	/**
	 * Validate to see whether this email can be used to register for this event or not
	 *
	 * @param $eventId
	 * @param $email
	 *
	 * @return array
	 */
	protected function validateRegistrantEmail($eventId, $email)
	{
		$user   = JFactory::getUser();
		$db     = JFactory::getDbo();
		$query  = $db->getQuery(true);
		$config = EventbookingHelper::getConfig();
		$result = array(
			'success' => true,
			'message' => ''
		);

		if ($config->prevent_duplicate_registration && !$config->multiple_booking)
		{
			$query->clear();
			$query->select('COUNT(id)')
				->from('#__eb_registrants')
				->where('event_id=' . $eventId)
				->where('email="' . $email . '"')
				->where('(published=1 OR (payment_method LIKE "os_offline%" AND published NOT IN (2,3)))');
			$db->setQuery($query);
			$total = $db->loadResult();
			if ($total)
			{
				$result['success'] = false;
				$result['message'] = JText::_('EB_EMAIL_REGISTER_FOR_EVENT_ALREADY');
			}
		}

		if ($result['success'] && $config->user_registration && !$user->id)
		{
			$query->clear();
			$query->select('COUNT(*)')
				->from('#__users')
				->where('email="' . $email . '"');
			$db->setQuery($query);
			$total = $db->loadResult();
			if ($total)
			{
				$result['success'] = false;
				$result['message'] = JText::_('EB_EMAIL_REGISTER_FOR_EVENT_ALREADY');
			}
		}

		return $result;
	}
}