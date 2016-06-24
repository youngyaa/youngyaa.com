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

class JFormFieldRL_TextAreaPlus extends RLFormField
{
	public $type = 'TextAreaPlus';

	protected function getLabel()
	{
		$this->params = $this->element->attributes();
		$resize       = $this->get('resize', 0);

		$label = RLText::html_entity_decoder(JText::_($this->get('label')));

		$html = '<label id="' . $this->id . '-lbl" for="' . $this->id . '"';
		if ($this->description)
		{
			$html .= ' class="hasTooltip" title="<strong>' . $label . '</strong><br>' . JText::_($this->description) . '">';
		}
		else
		{
			$html .= '>';
		}

		$html .= $label;

		if ($resize)
		{
			JHtml::_('jquery.framework');

			RLFunctions::script('regularlabs/script.min.js', '16.5.10919');
			RLFunctions::stylesheet('regularlabs/style.min.css', '16.5.10919');

			$html .= '<br><span role="button" class="rl_resize_textarea rl_maximize"'
				. ' data-id="' . $this->id . '"  data-min="' . $this->get('height', 80) . '" data-max="' . $resize . '">'
				. '<span class="rl_resize_textarea_maximize">'
				. '[ + ]'
				. '</span>'
				. '<span class="rl_resize_textarea_minimize">'
				. '[ - ]'
				. '</span>'
				. '</span>';
		}

		$html .= '</label>';

		return $html;
	}

	protected function getInput()
	{
		$width  = $this->get('width', 600);
		$height = $this->get('height', 80);
		$class  = trim('rl_textarea ' . $this->get('class'));
		$class  = 'class="' . $class . '"';
		$type   = $this->get('texttype');

		if (is_array($this->value))
		{
			$this->value = trim(implode("\n", $this->value));
		}

		if ($type == 'html')
		{
			// Convert <br> tags so they are not visible when editing
			$this->value = str_replace('<br>', "\n", $this->value);
		}
		else if ($type == 'regex')
		{
			// Protects the special characters
			$this->value = str_replace('[:REGEX_ENTER:]', '\n', $this->value);
		}

		$this->value = htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8');

		return '<textarea name="' . $this->name . '" cols="' . (round($width / 7.5)) . '" rows="' . (round($height / 15)) . '" style="width:' . (($width == '600') ? '100%' : $width . 'px') . ';height:' . $height . 'px" ' . $class . ' id="' . $this->id . '" >' . $this->value . '</textarea>';
	}
}
