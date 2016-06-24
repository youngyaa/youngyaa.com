<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class OSMembershipViewRegisterHtml extends MPFViewHtml
{

	public function display()
	{
		$app         = JFactory::getApplication();
		$input       = $this->input;
		$db          = JFactory::getDbo();
		$query       = $db->getQuery(true);
		$user        = JFactory::getUser();
		$config      = OSMembershipHelper::getConfig();
		$messageObj  = OSMembershipHelper::getMessages();
		$fieldSuffix = OSMembershipHelper::getFieldSuffix();
		OSMembershipHelper::addLangLinkForAjax();
		JFactory::getDocument()->addScript(JUri::base(true) . '/components/com_osmembership/assets/js/paymentmethods.js');
		$userId = $user->get('id');
		$Itemid = $this->Itemid;
		$planId = $input->getInt('id', 0);

		// Check to see whether this is a valid form or not
		if (!$planId)
		{
			$app->redirect('index.php', JText::_('OSM_INVALID_MEMBERSHIP_PLAN'));
		}
		$query->select('*, title' . $fieldSuffix . ' AS title')
			->from('#__osmembership_plans')
			->where('id=' . $planId);
		$db->setQuery($query);
		$plan = $db->loadObject();
		if (!$plan || $plan->published == 0)
		{
			$app->redirect('index.php', JText::_('OSM_CANNOT_ACCESS_UNPUBLISHED_PLAN'));
		}

		if (!in_array($plan->access, $user->getAuthorisedViewLevels()))
		{
			$app->redirect('index.php', JText::_('OSM_NOT_ALLOWED_PLAN'));
		}

		// Check if user can subscribe to the plan
		if (!OSMembershipHelper::canSubscribe($plan))
		{
			$loginRedirectUrl = OSMembershipHelper::getLoginRedirectUrl();
			if ($loginRedirectUrl)
			{
				$app->redirect(JRoute::_($loginRedirectUrl));
			}
			elseif ($config->number_days_before_renewal)
			{
				// Redirect to membership profile page
				$app->enqueueMessage(JText::sprintf('OSM_COULD_NOT_RENEWAL', $config->number_days_before_renewal), 'message');
				$profileItemId = OSMembershipHelperRoute::findView('profile', $Itemid);
				$app->redirect(JRoute::_('index.php?option=com_osmembership&view=profile&Itemid=' . $profileItemId));
			}
			else
			{
				$app->enqueueMessage(JText::_('OSM_YOU_ARE_NOT_ALLOWED_TO_SIGNUP'), 'message');
				$app->redirect('index.php');
			}
		}

		$defaultPaymentMethod = os_payments::getDefautPaymentMethod($plan->payment_methods);
		$paymentMethod        = $input->post->get('payment_method', $defaultPaymentMethod, 'cmd');
		if (!$paymentMethod)
		{
			$paymentMethod = $defaultPaymentMethod;
		}

		if ($plan->currency_symbol)
		{
			$symbol = $plan->currency_symbol;
		}
		elseif ($plan->currency)
		{
			$symbol = $plan->currency;
		}
		else
		{
			$symbol = $config->currency_symbol;
		}

		$renewOptionId   = $input->getInt('renew_option_id', 0);
		$upgradeOptionId = $input->getInt('upgrade_option_id', 0);
		if ($renewOptionId)
		{
			$action = 'renew';
		}
		elseif ($upgradeOptionId)
		{
			$action = 'upgrade';
		}
		else
		{
			$action = 'subscribe';
			// Check to see whether the user signed up for this plan before or not, if he signed up before, we treat this as renewal
			if ($userId)
			{
				$query->clear();
				$query->select('COUNT(*)')
					->from('#__osmembership_subscribers')
					->where('user_id = ' . $userId)
					->where('plan_id = ' . $plan->id)
					->where('published IN (1, 2)');
				$db->setQuery($query);

				$total = (int) $db->loadResult();
				if ($total)
				{
					$renewMembershipMenuId = OSMembershipHelperRoute::findView('renewmembership', 0);
					if ($renewMembershipMenuId)
					{
						JFactory::getApplication()->redirect(JRoute::_('index.php?Itemid=' . $renewMembershipMenuId));
					}
					else
					{
						$action = 'renew';
						// If there is only one renew option, assume that users will renew of that option

						$query->clear();
						$query->select('id')
							->from('#__osmembership_renewrates')
							->where('plan_id = ' . $plan->id);
						$db->setQuery($query);
						$renewOptions = $db->loadObjectList();
						if (count($renewOptions) == 1)
						{
							$data['renew_option_id'] = $renewOptions[0]->id;
						}
						else
						{
							$data['renew_option_id'] = OSM_DEFAULT_RENEW_OPTION_ID;
						}

						$renewOptionId = $data['renew_option_id'];
					}
				}
			}
		}
		###############Payment Methods parameters###############################									
		$lists['exp_month'] = JHtml::_('select.integerlist', 1, 12, 1, 'exp_month', ' id="exp_month" class="input-small" ', $input->get('exp_month', date('m'), 'none'), '%02d');
		$currentYear        = date('Y');
		$lists['exp_year']  = JHtml::_('select.integerlist', $currentYear, $currentYear + 10, 1, 'exp_year', ' id="exp_year" class="input-small" ', $input->get('exp_year', date('Y'), 'none'));


		if ($plan->recurring_subscription)
		{
			$onlyRecurring = 1;
		}
		else
		{
			$onlyRecurring = 0;
		}

		$methods = os_payments::getPaymentMethods($onlyRecurring, $plan->payment_methods);

		if (count($methods) == 0)
		{
			$app->redirect('index.php', JText::_('OSM_NEED_TO_PUBLISH_PLUGIN'));
		}

		// Check to see if there is payment processing fee or not
		$showPaymentFee = false;
		foreach ($methods as $method)
		{
			if ($method->paymentFee)
			{
				$showPaymentFee = true;
				break;
			}
		}
		$this->showPaymentFee = $showPaymentFee;

		$rowFields = OSMembershipHelper::getProfileFields($planId, true, null, $action);
		if ($input->getInt('validation_error', 0))
		{
			$data = $input->getData();
		}
		else
		{
			$data = array();
			if ($userId)
			{
				// Check to see if this user has profile data already
				$query->clear();
				$query->select('*')
					->from('#__osmembership_subscribers')
					->where('user_id=' . $userId . ' AND is_profile=1');
				$db->setQuery($query);
				$rowProfile = $db->loadObject();
				if ($rowProfile)
				{
					$data = OSMembershipHelper::getProfileData($rowProfile, $planId, $rowFields);
				}
				else
				{
					$mappings = array();
					foreach ($rowFields as $rowField)
					{
						if ($rowField->field_mapping)
						{
							$mappings[$rowField->name] = $rowField->field_mapping;
						}
					}
					JPluginHelper::importPlugin('osmembership');
					$dispatcher = JDispatcher::getInstance();
					$results    = $dispatcher->trigger('onGetProfileData', array($userId, $mappings));
					if (count($results))
					{
						foreach ($results as $res)
						{
							if (is_array($res) && count($res))
							{
								$data = $res;
								break;
							}
						}
					}
				}
				if (!count($data) && JPluginHelper::isEnabled('user', 'profile'))
				{
					$syncronizer = new MPFSynchronizerJoomla();
					$mappings    = array();
					foreach ($rowFields as $rowField)
					{
						if ($rowField->profile_field_mapping)
						{
							$mappings[$rowField->name] = $rowField->profile_field_mapping;
						}
					}
					$data = $syncronizer->getData($userId, $mappings);
				}
			}
			else
			{
				$data = $input->getData();
			}
		}

		if ($userId && !isset($data['first_name']))
		{
			// Load the name from Joomla default name
			$name = $user->name;
			if ($name)
			{
				$pos = strpos($name, ' ');
				if ($pos !== false)
				{
					$data['first_name'] = substr($name, 0, $pos);
					$data['last_name']  = substr($name, $pos + 1);
				}
				else
				{
					$data['first_name'] = $name;
					$data['last_name']  = '';
				}
			}
		}
		if ($userId && !isset($data['email']))
		{
			$data['email'] = $user->email;
		}
		if (!isset($data['country']) || !$data['country'])
		{
			$data['country'] = $config->default_country;
		}

		$countryCode = OSMembershipHelper::getCountryCode($data['country']);
		// Get data
		$form = new MPFForm($rowFields);
		$form->setData($data)->bindData(true);
		$form->prepareFormFields('calculateSubscriptionFee();');
		$data['renew_option_id']   = $renewOptionId;
		$data['upgrade_option_id'] = $upgradeOptionId;
		$data['act']               = $action;
		$fees                      = OSMembershipHelper::calculateSubscriptionFee($plan, $form, $data, $config, $paymentMethod);
		$amount                    = $fees['amount'];
		if ($action == 'renew')
		{
			if (strlen(strip_tags($messageObj->{'subscription_renew_form_msg' . $fieldSuffix})))
			{
				$message = $messageObj->{'subscription_renew_form_msg' . $fieldSuffix};
			}
			else
			{
				$message = $messageObj->subscription_renew_form_msg;
			}
			if ($renewOptionId == OSM_DEFAULT_RENEW_OPTION_ID)
			{
				$renewOptionFrequency = $plan->subscription_length_unit;
				$renewOptionLength    = $plan->subscription_length;
			}
			else
			{
				$query->clear();
				$query->select('number_days')
					->from('#__osmembership_renewrates')
					->where('id=' . $renewOptionId);
				$db->setQuery($query);
				$numberDays = $db->loadResult();
				list($renewOptionFrequency, $renewOptionLength) = OSMembershipHelper::getRecurringSettingOfPlan($numberDays);
			}
			switch ($renewOptionFrequency)
			{
				case 'D':
					$text = $renewOptionLength > 1 ? JText::_('OSM_DAYS') : JText::_('OSM_DAY');
					break;
				case 'W' :
					$text = $renewOptionLength > 1 ? JText::_('OSM_WEEKS') : JText::_('OSM_WEEK');
					break;
				case 'M' :
					$text = $renewOptionLength > 1 ? JText::_('OSM_MONTHS') : JText::_('OSM_MONTH');
					break;
				case 'Y' :
					$text = $renewOptionLength > 1 ? JText::_('OSM_YEARS') : JText::_('OSM_YEAR');
					break;
			}
			$message = str_replace('[NUMBER_DAYS] days', $renewOptionLength . ' ' . $text, $message);
			$message = str_replace('[PLAN_TITLE]', $plan->title, $message);
			$message = str_replace('[AMOUNT]', OSMembershipHelper::formatCurrency($amount, $config, $symbol), $message);
		}
		elseif ($action == 'upgrade')
		{
			if (strlen(strip_tags($messageObj->{'subscription_upgrade_form_msg' . $fieldSuffix})))
			{
				$message = $messageObj->{'subscription_upgrade_form_msg' . $fieldSuffix};
			}
			else
			{
				$message = $messageObj->subscription_upgrade_form_msg;
			}
			$query->clear();
			$query->select('b.title')
				->from('#__osmembership_upgraderules AS a')
				->innerJoin('#__osmembership_plans AS b ON a.from_plan_id=b.id')
				->where('a.id=' . $upgradeOptionId);
			$db->setQuery($query);
			$fromPlan = $db->loadResult();
			$message  = str_replace('[PLAN_TITLE]', $plan->title, $message);
			$message  = str_replace('[AMOUNT]', OSMembershipHelper::formatCurrency($amount, $config, $symbol), $message);
			$message  = str_replace('[FROM_PLAN_TITLE]', $fromPlan, $message);
		}
		else
		{
			if (strlen(strip_tags($plan->{'subscription_form_message' . $fieldSuffix})) || strlen(strip_tags($plan->subscription_form_message)))
			{
				if (strlen(strip_tags($plan->{'subscription_form_message' . $fieldSuffix})))
				{
					$message = $plan->{'subscription_form_message' . $fieldSuffix};
				}
				else
				{
					$message = $plan->subscription_form_message;
				}

			}
			else
			{
				if (strlen(strip_tags($messageObj->{'subscription_form_msg' . $fieldSuffix})))
				{
					$message = $messageObj->{'subscription_form_msg' . $fieldSuffix};
				}
				else
				{
					$message = $messageObj->subscription_form_msg;
				}
			}
			if ($plan->recurring_subscription)
			{
				//We will first need to detect regular duration								
				if ($plan->trial_duration)
				{
					$trialPeriorText = JText::_('OSM_TRIAL_RECURRING_SUBSCRIPTION_PERIOR');
					$trialPeriorText = str_replace('[TRIAL_DURATION]', $plan->trial_duration, $trialPeriorText);
					switch ($plan->trial_duration_unit)
					{
						case 'D':
							$trialPeriorText = str_replace('[TRIAL_DURATION_UNIT]', $plan->trial_duration > 1 ? JText::_('OSM_DAYS') : JText::_('OSM_DAY'), $trialPeriorText);
							break;
						case 'W':
							$trialPeriorText = str_replace('[TRIAL_DURATION_UNIT]', $plan->trial_duration > 1 ? JText::_('OSM_WEEKS') : JText::_('OSM_WEEK'), $trialPeriorText);
							break;
						case 'M':
							$trialPeriorText = str_replace('[TRIAL_DURATION_UNIT]', $plan->trial_duration > 1 ? JText::_('OSM_MONTHS') : JText::_('OSM_MONTH'), $trialPeriorText);
							break;
						case 'Y':
							$trialPeriorText = str_replace('[TRIAL_DURATION_UNIT]', $plan->trial_duration > 1 ? JText::_('OSM_YEARS') : JText::_('OSM_YEAR'), $trialPeriorText);
							break;
						default:
							$trialPeriorText = str_replace('[TRIAL_DURATION_UNIT]', $plan->trial_duration > 1 ? JText::_('OSM_DAYS') : JText::_('OSM_DAY'), $trialPeriorText);
							break;
					}
					$this->trialPeriorText = $trialPeriorText;
				}
				$length            = $plan->subscription_length;
				$regularPeriorText = JText::_('OSM_REGULAR_SUBSCRIPTION_PERIOR');
				$regularPeriorText = str_replace('[REGULAR_DURATION]', $length, $regularPeriorText);
				switch ($plan->subscription_length_unit)
				{
					case 'D':
						$regularPeriorText = str_replace('[REGULAR_DURATION_UNIT]', $length > 1 ? JText::_('OSM_DAYS') : JText::_('OSM_DAY'), $regularPeriorText);
						break;
					case 'W':
						$regularPeriorText = str_replace('[REGULAR_DURATION_UNIT]', $length > 1 ? JText::_('OSM_WEEKS') : JText::_('OSM_WEEK'), $regularPeriorText);
						break;
					case 'M':
						$regularPeriorText = str_replace('[REGULAR_DURATION_UNIT]', $length > 1 ? JText::_('OSM_MONTHS') : JText::_('OSM_MONTH'), $regularPeriorText);
						break;
					case 'Y':
						$regularPeriorText = str_replace('[REGULAR_DURATION_UNIT]', $length > 1 ? JText::_('OSM_YEARS') : JText::_('OSM_YEAR'), $regularPeriorText);
						break;
					default:
						$regularPeriorText = str_replace('[REGULAR_DURATION_UNIT]', $length > 1 ? JText::_('OSM_DAYS') : JText::_('OSM_DAY'), $regularPeriorText);
						break;
				}
				$this->regularPeriorText = $regularPeriorText;
				$message                 = str_replace('[PLAN_TITLE]', $plan->title, $message);
				$message                 = str_replace('[AMOUNT]', OSMembershipHelper::formatCurrency($amount, $config, $symbol), $message);
			}
			else
			{
				$message = str_replace('[PLAN_TITLE]', $plan->title, $message);
				$message = str_replace('[AMOUNT]', OSMembershipHelper::formatCurrency($amount, $config, $symbol), $message);
			}
		}

		// Implement Joomla core recpatcha
		$showCaptcha = 0;
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
				$showCaptcha   = 1;
				$this->captcha = JCaptcha::getInstance($captchaPlugin)->display('dynamic_recaptcha_1', 'dynamic_recaptcha_1', 'required');
			}
			else
			{
				JFactory::getApplication()->enqueueMessage(JText::_('OSM_CAPTCHA_NOT_ACTIVATED_IN_YOUR_SITE'), 'error');
			}
		}

		if ($config->enable_coupon)
		{
			$nullDate = $db->getNullDate();
			// Only show coupon if there are coupons code created for this plan
			$query->clear();
			$query->select('COUNT(*)')
				->from('#__osmembership_coupons')
				->where('published=1')
				->where('(valid_from="' . $nullDate . '" OR DATE(valid_from) <= CURDATE())')
				->where('(valid_to="' . $nullDate . '" OR DATE(valid_to) >= CURDATE())')
				->where('(times = 0 OR times > used)')
				->where('(plan_id=0 OR plan_id=' . $plan->id . ')');
			$db->setQuery($query);
			$total = (int) $db->loadResult();

			if (!$total)
			{
				// No coupon for this plan, so we just disable coupon
				$config->enable_coupon = 0;
			}
		}

		// Assign variables to template
		$this->userId            = $userId;
		$this->paymentMethod     = $paymentMethod;
		$this->lists             = $lists;
		$this->Itemid            = $Itemid;
		$this->config            = $config;
		$this->plan              = $plan;
		$this->methods           = $methods;
		$this->action            = $action;
		$this->renewOptionId     = $renewOptionId;
		$this->upgradeOptionId   = $upgradeOptionId;
		$this->message           = $message;
		$this->form              = $form;
		$this->fees              = $fees;
		$this->showCaptcha       = $showCaptcha;
		$this->countryBaseTax    = (int) OSMembershipHelper::isCountryBaseTax();
		$this->taxRate           = OSMembershipHelper::calculateMaxTaxRate($planId);
		$this->taxStateCountries = OSMembershipHelper::getTaxStateCountries();
		$this->countryCode       = $countryCode;
		$this->bootstrapHelper   = new OSMembershipHelperBootstrap($config->twitter_bootstrap_version);
		$this->currencySymbol    = $symbol;

		$this->setLayout('default');

		parent::display();
	}
}