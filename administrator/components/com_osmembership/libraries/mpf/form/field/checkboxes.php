<?php
/**
 * @package     MPF
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2015 Ossolution Team, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die();

/**
 * Form Field class for the Joomla MPF.
 * Supports a checkbox list custom field.
 *
 * @package     Joomla.MPF
 * @subpackage  Form
 */
class MPFFormFieldCheckboxes extends MPFFormField
{

	/**
	 * The form field type.
	 *
	 * @var string
	 *
	 */
	protected $type = 'Checkboxes';

	protected $values;

	protected $size;

	public function __construct($row, $value, $fieldSuffix)
	{
		parent::__construct($row, $value, $fieldSuffix);

		$this->values = $row->values;
		$size         = (int) $row->size;
		if ($size)
		{
			$this->size = $size;
		}
		else
		{
			$this->size = 1; // Each item in one line by default
		}
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return string The field input markup.
	 *
	 */
	protected function getInput($bootstrapHelper = null)
	{
		$html       = array();
		$options    = (array) $this->getOptions();
		$attributes = $this->buildAttributes();
		if (is_array($this->value))
		{
			$selectedOptions = $this->value;
		}
		elseif (strpos($this->value, "\r\n"))
		{
			$selectedOptions = explode("\r\n", $this->value);
		}
		elseif (is_string($this->value) && is_array(json_decode($this->value)))
		{
			$selectedOptions = json_decode($this->value);
		}
		else
		{
			$selectedOptions = array($this->value);
		}

		$i         = 0;
		$span      = intval(12 / $this->size);
		$rowFluid  = $bootstrapHelper ? $bootstrapHelper->getClassMapping('row-fluid') : 'row-fluid';
		$spanClass = $bootstrapHelper ? $bootstrapHelper->getClassMapping('span' . $span) : 'span' . $span;

		$html[]        = '<fieldset id="' . $this->name . '" class="' . $rowFluid . ' clearfix"' . '>';
		$html[]        = '<ul class="nav clearfix">';
		$numberOptions = count($options);
		foreach ($options as $option)
		{
			$i++;
			$optionValue = trim($option);
			if (empty($optionValue))
			{
				continue;
			}
			$checked = in_array($optionValue, $selectedOptions) ? 'checked' : '';
			$html[]  = '<li class="' . $spanClass . '">';
			$html[]  = '<label for="' . $this->name . $i . '" ><input type="checkbox" id="' . $this->name . $i . '" name="' . $this->name .
				'[]" value="' . htmlspecialchars($optionValue, ENT_COMPAT, 'UTF-8') . '"' . $checked . $attributes . $this->extraAttributes . '/> ' .
				$option . '</label>';
			$html[]  = '</li>';
			if ($i % $this->size == 0 && $i < $numberOptions)
			{
				$html[] = '</ul>';
				$html[] = '<ul class="nav clearfix">';
			}
		}
		$html[] = '</ul>';

		// End the checkbox field output.
		$html[] = '</fieldset>';

		return implode($html);
	}

	protected function getOptions()
	{
		if (is_array($this->values))
		{
			$values = $this->values;
		}
		elseif (strpos($this->values, "\r\n") !== false)
		{
			$values = explode("\r\n", $this->values);
		}
		else
		{
			$values = array($this->values);
		}

		return $values;
	}
}