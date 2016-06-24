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

/**
 * HTML View class for the Membership Pro component
 *
 * @package        Joomla
 * @subpackage     Membership Pro
 */
class OSMembershipViewCancelHtml extends MPFViewHtml
{
	public $hasModel = false;

	public function display()
	{
		$this->setLayout('default');
		$messageObj  = OSMembershipHelper::getMessages();
		$fieldSuffix = OSMembershipHelper::getFieldSuffix();
		if (strlen(strip_tags($messageObj->{'cancel_message' . $fieldSuffix})))
		{
			$message = $messageObj->{'cancel_message' . $fieldSuffix};
		}
		else
		{
			$message = $messageObj->cancel_message;
		}
		$this->message = $message;

		parent::display();
	}
}