<?php
/**
 * @package         Regular Labs Library
 * @version         16.4.23089
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2016 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once dirname(__DIR__) . '/assignment.php';

class RLAssignmentsUsers extends RLAssignment
{
	function passUserGroupLevels()
	{
		$user = JFactory::getUser();

		if (!empty($user->groups))
		{
			$groups = array_values($user->groups);
		}
		else
		{
			$groups = $user->getAuthorisedGroups();
		}

		return $this->passSimple($groups);
	}

	function passUsers()
	{
		return $this->passSimple(JFactory::getUser()->get('id'));
	}
}
