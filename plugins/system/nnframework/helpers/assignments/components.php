<?php
/**
 * @package         NoNumber Framework
 * @version         16.4.5735
 * 
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2016 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once dirname(__DIR__) . '/assignment.php';

class NNFrameworkAssignmentsComponents extends NNFrameworkAssignment
{
	function passComponents()
	{
		return $this->passSimple(strtolower($this->request->option));
	}
}
