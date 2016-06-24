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

class os_offline extends os_payment
{
	/**
	 * Constructor functions, init some parameter
	 *
	 * @param object $params
	 */
	function os_offline($params)
	{
		parent::setName('os_offline');
		parent::os_payment();
		parent::setCreditCard(false);
		parent::setCardType(false);
		parent::setCardCvv(false);
		parent::setCardHolderName(false);
	}

	/**
	 * Process payment
	 *
	 */
	function processPayment($row, $data)
	{
		$Itemid = JRequest::getint('Itemid');
		$config = OSMembershipHelper::getConfig();
		OSMembershipHelper::sendEmails($row, $config);
		JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_osmembership&view=complete&Itemid=' . $Itemid, false, false));
	}
}