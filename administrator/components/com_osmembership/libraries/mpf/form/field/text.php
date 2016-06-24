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
 * Supports a a text input.
 *
 * @package     Joomla.MPF
 * @subpackage  Form
 */
class MPFFormFieldText extends MPFFormField
{

	/**
	 * Field Type
	 * 
	 * @var string
	 */
	protected $type = 'Text';	
		
	public function __construct($row, $value = null, $fieldSuffix = null)
	{
		parent::__construct($row, $value, $fieldSuffix);		
		if ($row->place_holder)
		{			
			$this->attributes['placeholder'] = $row->place_holder;
		}
		if ($row->max_length)
		{
			$this->attributes['maxlength'] = $row->max_length;
		}
		if ($row->size)
		{
			$this->attributes['size'] = $row->size;
		}
	}

	public function getInput($bootstrapHelper = null)
	{
		$attributes = $this->buildAttributes();
		return '<input type="text" name="' . $this->name . '" id="' . $this->name . '" value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') .
			 '"' . $attributes . $this->extraAttributes . ' />';
	}
}