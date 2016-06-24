<?php
/**
 * @version        1.6.7
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die;

class os_payment
{
	var $paymentFee = false;
	/**
	 * Title of payment method
	 * @var string
	 */
	var $title = null;
	/**
	 * Name of payment method
	 *
	 * @var string
	 */
	var $_name = null;
	/**
	 * Creditcard payment method ?
	 *
	 * @var string
	 */
	var $_creditCard = false;
	/**
	 * Require card cvv code ?
	 *
	 * @var boolean
	 */
	var $_cardCvv = false;
	/**
	 * Require creditcard type ?
	 *
	 * @var boolean
	 */
	var $_cardType = false;
	/**
	 * Require card holder name ?
	 *
	 * @var boolean
	 */
	var $_cardHolderName = false;

	function os_payment()
	{
		$this->loadLanguage();
	}

	/**
	 * Getter method for name property
	 *
	 * @return string
	 */
	function getName()
	{
		return $this->_name;
	}

	/**
	 * Setter method for name property
	 *
	 * @param string $value
	 */
	function setName($value)
	{
		$this->_name = $value;
	}

	function getTitle()
	{
		return $this->title;
	}

	function setTitle($title)
	{
		$this->title = $title;
	}

	/**
	 * Getter method for cardType property
	 *
	 * @return boolean
	 */
	function getCreditCard()
	{
		if ($this->_creditCard)
			return 1;
		else
			return 0;
	}

	/**
	 * Setter method for creditCard
	 *
	 * @param  $value
	 */
	function setCreditCard($value)
	{
		$this->_creditCard = $value;
	}

	/**
	 * Setter method for cardCvv
	 *
	 * @return boolean
	 */
	function getCardCvv()
	{
		if ($this->_cardCvv)
			return 1;
		else
			return 0;
	}

	/**
	 * Setter method for cardCvv
	 *
	 * @param boolean
	 */
	function setCardCvv($value)
	{
		$this->_cardCvv = $value;
	}

	/**
	 * Getter method for cardType
	 *
	 * @return boolean
	 */
	function getCardType()
	{
		if ($this->_cardType)
			return 1;
		else
			return 0;
	}

	/**
	 * Setter method for CardType property
	 *
	 * @param boolean $value
	 */
	function setCardType($value)
	{
		$this->_cardType = $value;
	}

	/**
	 * Getter method for CardHolderName
	 *
	 * @return boolean
	 */
	function getCardHolderName()
	{
		if ($this->_cardHolderName)
			return 1;
		else
			return 0;
	}

	/**
	 * Setter method for CardHolderName
	 *
	 * @param boolean $value
	 */
	function setCardHolderName($value)
	{
		$this->_cardHolderName = $value;
	}

	/**
	 * Load language file for this payment plugin
	 *
	 */
	function loadLanguage()
	{
		$pluginName = $this->getName();
		$lang       = JFactory::getLanguage();
		$tag        = $lang->getTag();
		if (!$tag)
			$tag = 'en-GB';
		$lang->load($pluginName, JPATH_ROOT, $tag);
	}
}
?>