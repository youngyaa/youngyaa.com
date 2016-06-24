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

class JFormFieldRL_PlainText extends RLFormField
{
	public $type = 'PlainText';

	protected function getLabel()
	{
		RLFunctions::stylesheet('regularlabs/style.min.css', '16.5.10919');

		$this->params = $this->element->attributes();

		$label   = $this->prepareText($this->get('label'));
		$tooltip = $this->prepareText($this->get('description'));

		if (!$label && !$tooltip)
		{
			return '';
		}

		if (!$label)
		{
			return '<div>' . $tooltip . '</div>';
		}

		if (!$tooltip)
		{
			return '<div>' . $label . '</div>';
		}

		return '<label class="hasTooltip" title="<strong>' . $label . '</strong><br>' . htmlentities($tooltip) . '">'
		. $label . '</label>';
	}

	protected function getInput()
	{
		$this->params = $this->element->attributes();

		$text = $this->prepareText($this->value);

		if (!$text)
		{
			return '';
		}

		return '<fieldset class="rl_plaintext">' . $text . '</fieldset>';
	}
}
