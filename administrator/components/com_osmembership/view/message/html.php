<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2013 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * HTML View class for Membership Pro component
 *
 * @static
 * @package        Joomla
 * @subpackage     Membership Pro
 */
class OsMembershipViewMessageHtml extends MPFViewHtml
{

	public function display()
	{
		$this->item      = OSMembershipHelper::getMessages();
		$this->languages = OSMembershipHelper::getLanguages();

		parent::display();
	}
}