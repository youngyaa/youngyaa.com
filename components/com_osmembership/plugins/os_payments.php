<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die;

class os_payments
{
	/**
	 * Get list of payment methods
	 *
	 * @return array
	 */
	public static function getPaymentMethods($onlyRecurring = false, $methodIds = null)
	{
		static $methods;
		if (!$methods)
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('*')
				->from('#__osmembership_plugins')
				->where('published = 1')
				->where('`access` IN ('.implode(',', JFactory::getUser()->getAuthorisedViewLevels()).')')
				->order('`ordering`');
			
			if ($onlyRecurring)
			{
				$query->where('(support_recurring_subscription = 1 OR name = "os_offline")');
			}

			if ($methodIds)
			{
				$query->where('id IN (' . $methodIds . ')');
			}
			$db->setQuery($query);
			$rows = $db->loadObjectList();
			foreach ($rows as $row)
			{
				if (file_exists(JPATH_ROOT . '/components/com_osmembership/plugins/' . $row->name . '.php'))
				{
					require_once JPATH_ROOT . '/components/com_osmembership/plugins/' . $row->name . '.php';
					$params        = new JRegistry($row->params);
					$method        = new $row->name($params);
					$method->setTitle($row->title);
					if ($params->get('payment_fee_amount') > 0 || $params->get('payment_fee_percent'))
					{
						$method->paymentFee = true;
					}
					$methods[] = $method;
				}
			}
		}

		return $methods;
	}

	/**
	 * Write the javascript objects to show the page
	 *
	 * @return string
	 */
	public static function writeJavascriptObjects()
	{
		$methods  = os_payments::getPaymentMethods();
		$jsString = " methods = new PaymentMethods();\n";
		if (count($methods))
		{
			foreach ($methods as $method)
			{
				$jsString .= " method = new PaymentMethod('" . $method->getName() . "'," . $method->getCreditCard() . "," . $method->getCardType() . "," . $method->getCardCvv() . "," . $method->getCardHolderName() . ");\n";
				$jsString .= " methods.Add(method);\n";
			}
		}
		echo $jsString;
	}

	/**
	 * Load payment method object
	 *
	 * @param $name string Name of payment method
	 *
	 * @return object
	 */
	public static function loadPaymentMethod($name)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__osmembership_plugins')
			->where('name = ' . $db->quote($name));
		$db->setQuery($query);

		return $db->loadObject();
	}

	/**
	 * Get default payment plugin
	 *
	 * @return string
	 */
	public static function getDefautPaymentMethod($methodIds = null)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('name')
			->from('#__osmembership_plugins')
			->where('published = 1')
			->where('`access` IN ('.implode(',', JFactory::getUser()->getAuthorisedViewLevels()).')')
			->order('ordering');

		if ($methodIds)
		{
			$query->where('id IN (' . $methodIds . ')');
		}

		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * Get the payment method object based on it's name
	 *
	 * @param string $name
	 *
	 * @return object
	 */
	public static function getPaymentMethod($name)
	{
		$methods = os_payments::getPaymentMethods();
		foreach ($methods as $method)
		{
			if ($method->getName() == $name)
			{
				return $method;
			}
		}

		return null;
	}
}