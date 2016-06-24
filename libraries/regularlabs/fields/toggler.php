<?php
/**
 * @package         Regular Labs Library
 * @version         16.5.10919
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2016 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once dirname(__DIR__) . '/helpers/functions.php';

/**
 * To use this, make a start xml param tag with the param and value set
 * And an end xml param tag without the param and value set
 * Everything between those tags will be included in the slide
 *
 * Available extra parameters:
 * param            The name of the reference parameter
 * value            a comma separated list of value on which to show the framework
 */
class JFormFieldRL_Toggler extends JFormField
{
	public $type = 'Toggler';

	protected function getLabel()
	{
		return '';
	}

	protected function getInput()
	{
		$field = new RLFieldToggler;

		return $field->getInput($this->element->attributes());
	}
}

class RLFieldToggler
{
	function getInput($params)
	{
		$this->params = $params;

		$option = JFactory::getApplication()->input->get('option');

		// do not place toggler stuff on JoomFish pages
		if ($option == 'com_joomfish')
		{
			return '';
		}

		$param  = $this->get('param');
		$value  = $this->get('value');
		$nofx   = $this->get('nofx');
		$method = $this->get('method');
		$div    = $this->get('div', 0);

		JHtml::_('jquery.framework');
		RLFunctions::script('regularlabs/script.min.js', '16.5.10919');
		RLFunctions::script('regularlabs/toggler.min.js', '16.5.10919');

		$param = preg_replace('#^\s*(.*?)\s*$#', '\1', $param);
		$param = preg_replace('#\s*\|\s*#', '|', $param);

		$html = array();
		if ($param != '')
		{
			$param      = preg_replace('#[^a-z0-9-\.\|\@]#', '_', $param);
			$param      = str_replace('@', '_', $param);
			$set_groups = explode('|', $param);
			$set_values = explode('|', $value);
			$ids        = array();
			foreach ($set_groups as $i => $group)
			{
				$count = $i;
				if ($count >= count($set_values))
				{
					$count = 0;
				}
				$value = explode(',', $set_values[$count]);
				foreach ($value as $val)
				{
					$ids[] = $group . '.' . $val;
				}
			}

			if (!$div)
			{
				$html[] = '</div></div>';
			}

			$html[] = '<div id="' . rand(1000000, 9999999) . '___' . implode('___', $ids) . '" class="rl_toggler';
			if ($nofx)
			{
				$html[] = ' rl_toggler_nofx';
			}
			if ($method == 'and')
			{
				$html[] = ' rl_toggler_and';
			}
			$html[] = '">';

			if (!$div)
			{
				$html[] = '<div><div>';
			}
		}
		else
		{
			$html[] = '</div>';
		}

		return implode('', $html);
	}

	private function get($val, $default = '')
	{
		if (!isset($this->params[$val]) || (string) $this->params[$val] == '')
		{
			return $default;
		}

		return (string) $this->params[$val];
	}
}
