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
 * Supports a generic list of options.
 *
 * @package     Joomla.MPF
 * @subpackage  Form
 */
class MPFFormFieldList extends MPFFormField
{

	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'List';

	protected $values;

	protected $multiple = false;

	public function __construct($row, $value, $fieldSuffix)
	{
		parent::__construct($row, $value, $fieldSuffix);
		if ($row->size)
		{
			$this->attributes['size'] = $row->size;
		}
		if ($row->multiple)
		{
			$this->attributes['multiple'] = true;
			$this->multiple = true;
		}
		$this->values = $row->values;
	}

	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput($bootstrapHelper = null)
	{
		// Get the field options.
		$options = (array) $this->getOptions();
		$attributes = $this->buildAttributes();
		if ($this->multiple)
		{
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
		}
		else
		{
			$selectedOptions = $this->value;
		}
		return JHtml::_('select.genericlist', $options, $this->name . ($this->multiple ? '[]' : ''), trim($attributes . $this->extraAttributes), 
			'value', 'text', $selectedOptions);
	}

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   11.1
	 */
	protected function getOptions()
	{
		$options = array();
		$options[] = JHtml::_('select.option', '', JText::_('OSM_SELECT'));
		if (is_array($this->values))
		{
			$values = $this->values;
		}
		elseif (strpos($this->values, "\r\n") !== FALSE)
		{
			$values = explode("\r\n", $this->values);
		}
		else
		{
			$values = explode(",", $this->values);
		}
		foreach ($values as $value)
		{
			$options[] = JHtml::_('select.option', trim($value), $value);
		}
		return $options;
	}
}
