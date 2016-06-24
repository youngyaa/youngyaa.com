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

require_once dirname(__DIR__) . '/helpers/field.php';

class JFormFieldRL_AssignmentSelection extends RLFormField
{
	public $type = 'AssignmentSelection';

	protected function getLabel()
	{
		return '';
	}

	protected function getInput()
	{
		$this->params = $this->element->attributes();

		RLFunctions::stylesheet('regularlabs/style.min.css', '16.5.10919');

		require_once __DIR__ . '/toggler.php';
		$toggler = new RLFieldToggler;

		$this->value = (int) $this->value;
		$label       = $this->get('label');
		$param_name  = $this->get('name');
		$noshow      = $this->get('noshow', 0);
		$showclose   = $this->get('showclose', 0);

		$html = array();

		if (!$label)
		{
			if (!$noshow)
			{
				$html[] = $toggler->getInput(array('div' => 1));
			}

			$html[] = $toggler->getInput(array('div' => 1));

			return '</div>' . implode('', $html);
		}

		$label = RLText::html_entity_decoder(JText::_($label));

		$html[] = '</div>';
		if (!$noshow)
		{
			$html[] = $toggler->getInput(array('div' => 1, 'param' => 'show_assignments|' . $param_name, 'value' => '1|1,2'));
		}

		$class = 'well well-small rl_well';
		if ($this->value === 1)
		{
			$class .= ' alert-success';
		}
		else if ($this->value === 2)
		{
			$class .= ' alert-error';
		}
		$html[] = '<div class="' . $class . '">';
		if ($showclose && JFactory::getUser()->authorise('core.admin'))
		{
			$html[] = '<button type="button" class="close rl_remove_assignment">&times;</button>';
		}

		$html[] = '<div class="control-group">';

		$html[] = '<div class="control-label">';
		$html[] = '<label><h4 class="rl_assignmentselection-header">' . $label . '</h4></label>';
		$html[] = '</div>';

		$html[] = '<div class="controls">';
		$html[] = '<fieldset id="' . $this->id . '"  class="radio btn-group">';

		$onclick = ' onclick="RegularLabsScripts.setToggleTitleClass(this, 0)"';
		$html[]  = '<input type="radio" id="' . $this->id . '0" name="' . $this->name . '" value="0"' . ((!$this->value) ? ' checked="checked"' : '') . $onclick . '>';
		$html[]  = '<label class="rl_btn-ignore" for="' . $this->id . '0">' . JText::_('RL_IGNORE') . '</label>';

		$onclick = ' onclick="RegularLabsScripts.setToggleTitleClass(this, 1)"';
		$html[]  = '<input type="radio" id="' . $this->id . '1" name="' . $this->name . '" value="1"' . (($this->value === 1) ? ' checked="checked"' : '') . $onclick . '>';
		$html[]  = '<label class="rl_btn-include" for="' . $this->id . '1">' . JText::_('RL_INCLUDE') . '</label>';

		$onclick = ' onclick="RegularLabsScripts.setToggleTitleClass(this, 2)"';
		$onclick .= ' onload="RegularLabsScripts.setToggleTitleClass(this, ' . $this->value . ', 7)"';
		$html[] = '<input type="radio" id="' . $this->id . '2" name="' . $this->name . '" value="2"' . (($this->value === 2) ? ' checked="checked"' : '') . $onclick . '>';
		$html[] = '<label class="rl_btn-exclude" for="' . $this->id . '2">' . JText::_('RL_EXCLUDE') . '</label>';

		$html[] = '</fieldset>';
		$html[] = '</div>';

		$html[] = '</div>';
		$html[] = '<div class="clearfix"> </div>';

		$html[] = $toggler->getInput(array('div' => 1, 'param' => $param_name, 'value' => '1,2'));
		$html[] = '<div><div>';

		return '</div>' . implode('', $html);
	}
}
