<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     OSMembership
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die();

class OSMembershipControllerRegister extends OSMembershipController
{
	/**
	 * Initialize data for renewing membership
	 */
	public function process_renew_membership()
	{
		//$this->csrfProtection();

		$renewOptionId = $this->input->getString('renew_option_id', 0);
		if (!$renewOptionId)
		{
			$this->app->redirect('index.php', JText::_('OSM_INVALID_RENEW_MEMBERSHIP_OPTION'));
		}
		if (strpos($renewOptionId, '|') !== false)
		{
			$renewOptionArray = explode('|', $renewOptionId);
			$this->input->set('id', (int) $renewOptionArray[0]);
			$this->input->set('renew_option_id', (int) $renewOptionArray[1]);
		}
		else
		{
			$this->input->set('id', (int) $renewOptionId);
			$this->input->set('renew_option_id', OSM_DEFAULT_RENEW_OPTION_ID);
		}
		$this->input->set('view', 'register');
		$this->input->set('layout', 'default');
		$this->display();
	}

	/**
	 * Initialize data for upgrading membership
	 *
	 * @throws Exception
	 */
	public function process_upgrade_membership()
	{
		//$this->csrfProtection();

		$upgradeOptionId = $this->input->getInt('upgrade_option_id', 0);
		$db              = JFactory::getDbo();
		$query           = $db->getQuery(true);
		$query->select('to_plan_id')
			->from('#__osmembership_upgraderules')
			->where('id=' . $upgradeOptionId);
		$db->setQuery($query);
		$upgradeRule = $db->loadObject();
		if ($upgradeRule)
		{
			//Set Plan ID
			$this->input->set('id', $upgradeRule->to_plan_id);
			$this->input->set('view', 'register');
			$this->input->set('layout', 'default');
			$this->display();
		}
		else
		{
			$this->app->redirect('index.php', JText::_('OSM_INVALID_UPGRADE_MEMBERSHIP_OPTION'));
		}
	}

	/**
	 * Process subscription
	 *
	 * @throws Exception
	 */
	public function process_subscription()
	{
		$this->csrfProtection();
		$config = OSMembershipHelper::getConfig();
		$input  = $this->input;

		if (!empty($config->use_email_as_username) && !JFactory::getUser()->get('id'))
		{
			$input->post->set('username', $input->post->getString('email'));
		}

		if ($config->enable_captcha)
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
				$res = JCaptcha::getInstance($captchaPlugin)->checkAnswer($input->post->get('recaptcha_response_field', '', 'string'));
			}
			else
			{
				$res = true;
			}

			if (!$res)
			{
				$this->app->enqueueMessage(JText::_('OSM_INVALID_CAPTCHA_ENTERED'), 'warning');
				$input->set('view', 'register');
				$input->set('layout', 'default');
				$input->set('id', $input->getInt('plan_id', 0));
				$input->set('validation_error', 1);
				$this->display();

				return;
			}
		}
		// Server side validation
		$model  = $this->getModel();
		$errors = $model->validate($input);

		if (count($errors))
		{
			// Enqueue the error messages
			foreach ($errors as $error)
			{
				$this->app->enqueueMessage($error, 'error');
			}

			$input->set('view', 'register');
			$input->set('layout', 'default');
			$input->set('id', $input->getInt('plan_id', 0));
			$input->set('validation_error', 1);
			$this->display();

			return;
		}

		// OK, data validation success, process the subscription
		try
		{
			$data = $input->post->getData();
			$model->processSubscription($data);
		}
		catch (Exception $e)
		{
			$this->app->enqueueMessage($e->getMessage(), 'error');
			$input->set('view', 'register');
			$input->set('layout', 'default');
			$input->set('id', $input->getInt('plan_id', 0));
			$input->set('validation_error', 1);
			$this->display();

			return;
		}
	}

	/**
	 * Verify the payment and further process. Called by payment gateway when a payment completed
	 *
	 */
	public function payment_confirm()
	{
		$model         = $this->getModel();
		$paymentMethod = $this->input->getString('payment_method');
		$model->paymentConfirm($paymentMethod);
	}

	/**
	 * Verify the payment and further process. Called by payment gateway when a recurring payment happened
	 *
	 */
	public function recurring_payment_confirm()
	{
		$model         = $this->getModel();
		$paymentMethod = $this->input->getString('payment_method');
		$model->recurringPaymentConfirm($paymentMethod);
	}

	/**
	 * Cancel recurring subscription
	 *
	 * @throws Exception
	 */
	public function process_cancel_subscription()
	{
		$this->csrfProtection();
		$subscriptionId = $this->input->post->get('subscription_id', '', 'none');
		$Itemid         = $this->input->getInt('Itemid', 0);

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__osmembership_subscribers')
			->where('subscription_id = ' . $db->quote($subscriptionId));
		$db->setQuery($query);
		$rowSubscription = $db->loadObject();
		if ($rowSubscription && OSMembershipHelper::canCancelSubscription($rowSubscription))
		{
			$model = $this->getModel('Register');
			$ret   = $model->cancelSubscription($rowSubscription);
			if ($ret)
			{
				$this->app->redirect('index.php?option=com_osmembership&view=subscriptioncancel&subscription_code=' . $rowSubscription->subscription_code . '&Itemid=' . $Itemid);
			}
			else
			{
				// Redirect back to profile page, the payment plugin should enque the reason of failed cancellation so that it could be displayed to end user
				$this->app->redirect('index.php?option=com_osmembership&view=profile&Itemid=' . $Itemid);
			}
		}
		else
		{
			// Redirect back to user profile page
			$this->app->redirect('index.php?option=com_osmembership&view=profile&Itemid=' . $Itemid,
				JText::_('OSM_INVALID_SUBSCRIPTION'));
		}
	}

	/**
	 * Re-calculate subscription fee when subscribers choose a fee option on subscription form
	 *
	 * Called by ajax request. After calculation, the system will update the fee displayed on end users on subscription sign up form
	 */
	public function calculate_subscription_fee()
	{
		$db     = JFactory::getDbo();
		$query  = $db->getQuery(true);
		$config = OSMembershipHelper::getConfig();
		$planId = $this->input->getInt('plan_id', 0);
		$query->select('*')
			->from('#__osmembership_plans')
			->where('id=' . $planId);
		$db->setQuery($query);
		$rowPlan   = $db->loadObject();
		$rowFields = OSMembershipHelper::getProfileFields($planId);
		$data      = $this->input->getData();
		$form      = new MPFForm($rowFields);
		$form->setData($data)->bindData(true);
		$fees = OSMembershipHelper::calculateSubscriptionFee($rowPlan, $form, $data, $config, $this->input->get('payment_method', '', 'none'));

		$amountFields = array(
			'trial_amount',
			'trial_discount_amount',
			'trial_tax_amount',
			'trial_payment_processing_fee',
			'trial_gross_amount',
			'regular_amount',
			'regular_discount_amount',
			'regular_tax_amount',
			'regular_payment_processing_fee',
			'regular_gross_amount',
			'amount',
			'discount_amount',
			'tax_amount',
			'payment_processing_fee',
			'gross_amount'
		);
		foreach ($amountFields as $field)
		{
			$fees[$field] = OSMembershipHelper::formatAmount($fees[$field], $config);
		}
		echo json_encode($fees);
		$this->app->close();
	}

	/**
	 * Get list of states for the selected country, using in AJAX request
	 */
	public function get_states()
	{
		$countryName = $this->input->get('country_name', '', 'string');
		$fieldName   = $this->input->get('field_name', 'state', 'string');
		$stateName   = $this->input->get('state_name', '', 'string');
		if (!$countryName)
		{
			$countryName = OSMembershipHelper::getConfigValue('default_country');
		}
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->clear();
		$query->select('required')
			->from('#__osmembership_fields')
			->where('name=' . $db->quote('state'));
		$db->setQuery($query);
		$required = $db->loadResult();
		($required) ? $class = 'validate[required]' : $class = '';

		$query->clear();
		$query->select('country_id')
			->from('#__osmembership_countries')
			->where('name=' . $db->quote($countryName));
		$db->setQuery($query);
		$countryId = $db->loadResult();
		//get state
		$query->clear();
		$query->select('state_2_code AS value, state_name AS text')
			->from('#__osmembership_states')
			->where('country_id=' . (int) $countryId)
			->where('published=1')
			->order('state_name');
		$db->setQuery($query);
		$states  = $db->loadObjectList();
		$options = array();
		if (count($states))
		{
			$options[] = JHtml::_('select.option', '', JText::_('OSM_SELECT_STATE'));
			$options   = array_merge($options, $states);
		}
		else
		{
			$options[] = JHtml::_('select.option', 'N/A', JText::_('OSM_NA'));
		}
		echo JHtml::_('select.genericlist', $options, $fieldName, ' class="input-large ' . $class . '" id="' . $fieldName . '"', 'value', 'text', $stateName);
		$this->app->close();
	}

	/**
	 * Get depend fields status to show/hide custom fields based on selected options
	 */
	public function get_depend_fields_status()
	{
		$db          = JFactory::getDbo();
		$fieldId     = $this->input->get('field_id', 'int');
		$fieldValues = $this->input->get('field_values', '', 'none');
		$fieldValues = explode(',', $fieldValues);
		//Get list of depend fields
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__osmembership_fields')
			->where('depend_on_field_id=' . $fieldId);
		$db->setQuery($query);
		$rows       = $db->loadObjectList();
		$showFields = array();
		$hideFields = array();
		foreach ($rows as $row)
		{
			$dependOnOptions = explode(",", $row->depend_on_options);
			if (count(array_intersect($fieldValues, $dependOnOptions)))
			{
				$showFields[] = 'field_' . $row->name;
			}
			else
			{
				$hideFields[] = 'field_' . $row->name;
			}
		}
		echo json_encode(array('show_fields' => implode(',', $showFields), 'hide_fields' => implode(',', $hideFields)));
		JFactory::getApplication()->close();
	}
}