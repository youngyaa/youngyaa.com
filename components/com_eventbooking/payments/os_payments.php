<?php
/**
 * @version        	2.0.0
 * @package        	Joomla
 * @subpackage		Event Booking
 * @author  		Tuan Pham Ngoc
 * @copyright    	Copyright (C) 2010 - 2015 Ossolution Team
 * @license        	GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die();

class os_payments
{

	public static $methods = null;

	/**
	 * Get list of payment methods
	 *
	 * @param $methodIds string
	 *
	 * @return array
	 */
	public static function getPaymentMethods($methodIds = null)
	{
		if (!self::$methods)
		{
			$path = JPATH_ROOT . '/components/com_eventbooking/payments/';
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('*')
				->from('#__eb_payment_plugins')
				->where('published=1')
				->where('`access` IN ('.implode(',', JFactory::getUser()->getAuthorisedViewLevels()).')')
				->order('ordering');
			if ($methodIds)
			{
				$query->where('id IN (' . $methodIds . ')');
			}
			$db->setQuery($query);
			$rows = $db->loadObjectList();
			foreach ($rows as $row)
			{
				if (file_exists($path . $row->name . '.php'))
				{
					require_once $path . $row->name . '.php';
					$params        = new JRegistry($row->params);
					$method = new $row->name($params);
					$method->setTItle($row->title);
					if ($params->get('payment_fee_amount') > 0 || $params->get('payment_fee_percent'))
					{
						$method->paymentFee = true;
					}
					self::$methods[] = $method;
				}
			}
		}
		return self::$methods;
	}

	/**
	 * Write the javascript objects to show the page
	 *
	 * @return string
	 */
	public static function writeJavascriptObjects()
	{
		$methods = self::getPaymentMethods();
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
	 * Load information about the payment method
	 *
	 * @param string $name Name of the payment method
	 *
	 * @return object
	 */
	public static function loadPaymentMethod($name)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__eb_payment_plugins')
			->where('name = '. $db->quote($name));
		$db->setQuery($query);

		return $db->loadObject();
	}

	/**
	 * Get default payment gateway
	 *
	 * @param $methodIds string Ids of the available payment method
	 *
	 * @return string
	 */
	public static function getDefautPaymentMethod($methodIds = null)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('name')
			->from('#__eb_payment_plugins')
			->where('published=1')
			->where('`access` IN ('.implode(',', JFactory::getUser()->getAuthorisedViewLevels()).')')
			->order('ordering');
		if ($methodIds)
		{
			$query->where('id IN (' . $methodIds . ')');
		}
		$db->setQuery($query, 0, 1);

		return $db->loadResult();
	}

	/**
	 * Get the payment method object based on it's name
	 *
	 * @param string $name
	 * @return object
	 */
	public static function getPaymentMethod($name)
	{
		$methods = self::getPaymentMethods();
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
?>