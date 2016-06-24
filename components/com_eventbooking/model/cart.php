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

class EventbookingModelCart extends RADModel
{
	/**
	 * Add one or multiple events to cart
	 *
	 * @param $data
	 *
	 * @return bool
	 */
	public function processAddToCart($data)
	{
		if (is_array($data['id']))
		{
			$eventIds = $data['id'];
		}
		else
		{
			$eventIds = array($data['id']);
		}
		$cart = new EventbookingHelperCart();
		$cart->addEvents($eventIds);

		return true;
	}

	/**
	 * Update cart with new quantities
	 *
	 * @param array $eventIds
	 * @param array $quantities
	 *
	 * @return bool
	 */
	public function processUpdateCart($eventIds, $quantities)
	{
		$cart = new EventbookingHelperCart();
		$cart->updateCart($eventIds, $quantities);

		return true;
	}

	/**
	 * Remove an event from cart
	 *
	 * @param $id
	 *
	 * @return bool
	 */
	public function removeEvent($id)
	{
		$cart = new EventbookingHelperCart();
		$cart->remove($id);

		return true;
	}

	/**
	 * Process checkout in case customer using shopping cart feature
	 *
	 * @param $data
	 *
	 * @return int
	 * @throws Exception
	 */
	public function processCheckout(&$data)
	{
		jimport('joomla.user.helper');
		$db                     = JFactory::getDbo();
		$query                  = $db->getQuery(true);
		$user                   = JFactory::getUser();
		$config                 = EventbookingHelper::getConfig();
		$row                    = JTable::getInstance('EventBooking', 'Registrant');
		$data['transaction_id'] = strtoupper(JUserHelper::genRandomPassword());
		$cart                   = new EventbookingHelperCart();
		$items                  = $cart->getItems();
		$quantities             = $cart->getQuantities();
		$paymentMethod          = isset($data['payment_method']) ? $data['payment_method'] : '';
		$fieldSuffix            = EventbookingHelper::getFieldSuffix();
		if (!$user->id && $config->user_registration)
		{
			$userId          = EventbookingHelper::saveRegistration($data);
			$data['user_id'] = $userId;
		}
		$rowFields = EventbookingHelper::getFormFields(0, 4);
		$form      = new RADForm($rowFields);
		$form->bind($data);
		$data['collect_records_data'] = true;
		$fees                         = EventbookingHelper::calculateCartRegistrationFee($cart, $form, $data, $config, $paymentMethod);
		// Save the active language
		if (JFactory::getApplication()->getLanguageFilter())
		{
			$language = JFactory::getLanguage()->getTag();
		}
		else
		{
			$language = '*';
		}
		$recordsData = $fees['records_data'];
		$cartId      = 0;
		$couponId    = 0;
		// Store list of registrants
		for ($i = 0, $n = count($items); $i < $n; $i++)
		{
			$eventId    = $items[$i];
			$recordData = $recordsData[$eventId];
			$row->bind($data);
			$row->event_id               = $eventId;
			$row->coupon_id              = isset($recordData['coupon_id']) ? $recordData['coupon_id'] : 0;
			$row->total_amount           = $recordData['total_amount'];
			$row->discount_amount        = $recordData['discount_amount'];
			$row->late_fee               = $recordData['late_fee'];
			$row->tax_amount             = $recordData['tax_amount'];
			$row->payment_processing_fee = $recordData['payment_processing_fee'];
			$row->amount                 = $recordData['amount'];
			$row->deposit_amount         = $recordData['deposit_amount'];
			if ($row->deposit_amount > 0)
			{
				$row->payment_status = 0;
			}
			else
			{
				$row->payment_status = 1;
			}
			$row->group_id      = 0;
			$row->published     = 0;
			$row->register_date = gmdate('Y-m-d H:i:s');
			if (isset($data['user_id']))
			{
				$row->user_id = $data['user_id'];
			}
			else
			{
				$row->user_id = $user->get('id');
			}
			$row->number_registrants = $quantities[$i];
			$row->event_id           = $eventId;
			if ($i == 0)
			{
				$row->cart_id = 0;
				//Store registration code
				while (true)
				{
					$registrationCode = JUserHelper::genRandomPassword(10);
					$query->clear();
					$query->select('COUNT(*)')
						->from('#__eb_registrants')
						->where('registration_code=' . $db->quote($registrationCode));
					$db->setQuery($query);
					$total = $db->loadResult();
					if (!$total)
					{
						break;
					}
				}
				$row->registration_code = $registrationCode;
			}
			else
			{
				$row->cart_id = $cartId;
			}
			$row->id       = 0;
			$row->language = $language;
			$row->store();
			$form->storeData($row->id, $data);
			if ($i == 0)
			{
				$cartId = $row->id;
			}
			if ($row->coupon_id > 0)
			{
				$couponId = $row->coupon_id;
			}
			JPluginHelper::importPlugin('eventbooking');
			$dispatcher = JDispatcher::getInstance();
			$dispatcher->trigger('onAfterStoreRegistrant', array($row));
		}

		$query->clear();
		$query->select('title' . $fieldSuffix . ' AS title')
			->from('#__eb_events')
			->where('id IN (' . implode(',', $items) . ')')
			->order('FIND_IN_SET(id, "' . implode(',', $items) . '")');

		$db->setQuery($query);
		$eventTitles         = $db->loadColumn();
		$data['event_title'] = implode(', ', $eventTitles);
		if ($couponId > 0)
		{
			$sql = 'UPDATE #__eb_coupons SET used = used + 1 WHERE id=' . (int) $couponId;
			$db->setQuery($sql);
			$db->execute();
		}
		$cart->reset();
		$session = JFactory::getSession();
		$session->set('eb_registration_code', $row->registration_code);
		if ($fees['amount'] > 0)
		{
			if ($fees['deposit_amount'] > 0)
			{
				$data['amount'] = $fees['deposit_amount'];
			}
			else
			{
				$data['amount'] = $fees['amount'];
			}
			$row->load($cartId);
			require_once JPATH_COMPONENT . '/payments/' . $paymentMethod . '.php';
			$query->clear();
			$query->select('params')
				->from('#__eb_payment_plugins')
				->where('name=' . $db->quote($paymentMethod));
			$db->setQuery($query);
			$params       = new JRegistry($db->loadResult());
			$paymentClass = new $paymentMethod($params);
			$paymentClass->processPayment($row, $data);
		}
		else
		{
			$row->load($cartId);
			$row->payment_date = gmdate('Y-m-d H:i:s');
			$row->published    = 1;
			$row->store();

			// Update status of all registrants
			$query->clear();
			$query->update('#__eb_registrants')
				->set('published = 1')
				->set('payment_date=NOW()')
				->where('cart_id = ' . $row->id);
			$db->setQuery($query);
			$db->execute();
			EventbookingHelper::sendEmails($row, $config);
			JPluginHelper::importPlugin('eventbooking');
			$dispatcher = JDispatcher::getInstance();
			$dispatcher->trigger('onAfterPaymentSuccess', array($row));

			return 1;
		}
	}

	/**
	 * Get information of events which user added to cart
	 *
	 * @return array|mixed
	 */
	function getData()
	{
		$config = EventbookingHelper::getConfig();
		$cart   = new EventbookingHelperCart();
		$rows   = $cart->getEvents();
		if ($config->show_price_including_tax)
		{
			for ($i = 0, $n = count($rows); $i < $n; $i++)
			{
				$row       = $rows[$i];
				$taxRate   = $row->tax_rate;
				$row->rate = round($row->rate * (1 + $taxRate / 100), 2);
				if ($config->show_discounted_price)
				{
					$row->discounted_price = round($row->discounted_price * (1 + $taxRate / 100), 2);
				}
			}
		}

		return $rows;
	}
} 