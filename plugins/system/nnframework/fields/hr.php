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

require_once dirname(__DIR__) . '/helpers/functions.php';

class JFormFieldNN_HR extends JFormField
{
	public $type = 'HR';

	protected function getLabel()
	{
		return '';
	}

	protected function getInput()
	{
		NNFrameworkFunctions::stylesheet('nnframework/style.min.css', '16.4.5735');

		return '<div class="nn_panel nn_hr"></div>';
	}
}
