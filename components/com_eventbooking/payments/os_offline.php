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
		$app = JFactory::getApplication();
		$Itemid = JRequest::getint('Itemid');
		$config = EventbookingHelper::getConfig();
		if ($row->is_group_billing)
		{
			EventbookingHelper::updateGroupRegistrationRecord($row->id);
		}
		EventbookingHelper::sendEmails($row, $config);
		$url = JRoute::_('index.php?option=com_eventbooking&view=complete&Itemid=' . $Itemid, false, false);
		$app->redirect($url);
	}
}