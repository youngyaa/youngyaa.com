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

require_once dirname(__DIR__) . '/helpers/functions.php';

class JFormFieldRL_HR extends JFormField
{
	public $type = 'HR';

	protected function getLabel()
	{
		return '';
	}

	protected function getInput()
	{
		RLFunctions::stylesheet('regularlabs/style.min.css', '16.4.23089');

		return '<div class="rl_panel rl_hr"></div>';
	}
}
