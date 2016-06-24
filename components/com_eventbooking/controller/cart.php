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

class EventbookingControllerCart extends EventbookingController
{
	/**
	 * Add the selected events to shopping cart
	 *
	 * @throws Exception
	 */
	public function add_cart()
	{
		$data  = $this->input->getData();
		if (is_numeric($data['id']))
		{
			// Check if this is event is password protected
			$event = EventbookingHelperDatabase::getEvent((int) $data['id']);
			if ($event->event_password)
			{
				$passwordPassed = JFactory::getSession()->get('eb_passowrd_' . $event->id, 0);
				if (!$passwordPassed)
				{
					$return = base64_encode(JUri::getInstance()->toString());
					$this->app->redirect(JRoute::_('index.php?option=com_eventbooking&view=password&event_id=' . $event->id . '&return=' . $return . '&Itemid=' . $this->input->getInt('Itemid', 0), false));
				}
				else
				{
					// Add event to cart, then redirect to cart page
					$model = $this->getModel('cart');
					$model->processAddToCart($data);
					$Itemid = $this->input->getInt('Itemid', 0);
					$this->app->redirect(JRoute::_(EventbookingHelperRoute::getViewRoute('cart', $Itemid), false));
				}
			}
		}
		$model = $this->getModel('cart');
		$model->processAddToCart($data);

		$this->input->set('view', 'cart');
		$this->input->set('layout', 'mini');

		$this->reloadCartModule();

		$this->display();
		$this->app->close();
	}

	/**
	 * Update the cart with new updated quantities
	 *
	 * @throws Exception
	 */
	public function update_cart()
	{
		$Itemid     = $this->input->getInt('Itemid', 0);
		$redirect   = $this->input->getInt('redirect', 1);
		$eventIds   = $this->input->get('event_id', '', 'none');
		$quantities = $this->input->get('quantity', '', 'none');
		$model      = $this->getModel('cart');
		if (!$redirect)
		{
			$eventIds   = explode(',', $eventIds);
			$quantities = explode(',', $quantities);
		}
		$model->processUpdateCart($eventIds, $quantities);
		if ($redirect)
		{
			$this->setRedirect(JRoute::_(EventbookingHelperRoute::getViewRoute('cart', $Itemid), false));
		}
		else
		{
			$this->input->set('view', 'cart');
			$this->input->set('layout', 'mini');
			$this->reloadCartModule();
			$this->display();
			$this->app->close();
		}
	}

	/**
	 * Remove the selected event from shopping cart
	 */
	public function remove_cart()
	{
		$redirect = $this->input->getInt('redirect', 1);
		$Itemid   = $this->input->getInt('Itemid', 0);
		$id       = $this->input->getInt('id', 0);
		$model    = $this->getModel('cart');
		$model->removeEvent($id);
		if ($redirect)
		{
			$this->setRedirect(JRoute::_(EventbookingHelperRoute::getViewRoute('cart', $Itemid), false));
		}
		else
		{
			$this->input->set('view', 'cart');
			$this->input->set('layout', 'mini');
			$this->reloadCartModule();
			$this->display();
			$this->app->close();
		}
	}

	/***
	 * Process checkout
	 *
	 * @throws Exception
	 */
	public function process_checkout()
	{
		$emailValid   = true;
		$captchaValid = true;

		// Check email
		$result = $this->validateRegistrantEmail(0, $this->input->get('email', '', 'none'));

		if (!$result['success'])
		{
			$emailValid = false;
		}
		else
		{
			$config = EventbookingHelper::getConfig();
			$user   = JFactory::getUser();
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
					$captchaValid = JCaptcha::getInstance($captchaPlugin)->checkAnswer($this->input->post->get('recaptcha_response_field', '', 'string'));
				}
			}
		}

		if (!$emailValid || !$captchaValid)
		{
			// Enqueue the error message
			if (!$emailValid)
			{
				$this->app->enqueueMessage($result['message'], 'warning');
			}
			else
			{
				$this->app->enqueueMessage(JText::_('EB_INVALID_CAPTCHA_ENTERED'), 'warning');
			}
			$this->input->set('captcha_invalid', 1);
			$this->input->set('view', 'register');
			$this->input->set('layout', 'cart');
			$this->display();

			return;

		}

		$cart  = new EventbookingHelperCart();
		$items = $cart->getItems();
		if (!count($items))
		{
			$this->app->redirect('index.php', JText::_('Sorry, your session was expired. Please try again!'));
		}

		$data   = $this->input->post->getData();
		$model  = $this->getModel('cart');
		$return = $model->processCheckout($data);

		if ($return == 1)
		{
			// Redirect to registration complete page
			$this->setRedirect(JRoute::_('index.php?option=com_eventbooking&view=complete&Itemid=' . $this->input->getInt('Itemid'), false, false));
		}
	}

	/**
	 * Calculate registration fee, then update information on cart registration form
	 */
	public function calculate_cart_registration_fee()
	{
		$input               = $this->input;
		$config              = EventbookingHelper::getConfig();
		$paymentMethod       = $input->getString('payment_method', '');
		$data                = $input->post->getData();
		$data['coupon_code'] = $input->getString('coupon_code', '');
		$cart                = new EventbookingHelperCart();
		$response            = array();
		$rowFields           = EventbookingHelper::getFormFields(0, 4);
		$form                = new RADForm($rowFields);
		$form->bind($data);

		$fees                               = EventbookingHelper::calculateCartRegistrationFee($cart, $form, $data, $config, $paymentMethod);
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

	/**
	 * Refresh content of cart module so that data will be keep synchronized
	 */
	private function reloadCartModule()
	{
		jimport('joomla.application.module.helper');
		$module = JModuleHelper::isEnabled('mod_eb_cart');
		if (!$module)
		{
			return;
		}
		?>
		<script type="text/javascript">
			Eb.jQuery(function ($) {
				$(document).ready(function () {
					$.ajax({
						type: 'POST',
						url: 'index.php?option=com_eventbooking&view=cart&layout=module&format=raw',
						dataType: 'html',
						success: function (html) {
							$('#cart_result').html(html);
						}
					})
				})
			})
		</script>
	<?php
	}
}