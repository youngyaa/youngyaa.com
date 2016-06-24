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
 * Supports a textarea inut.
 *
 * @package     Joomla.MPF
 * @subpackage  Form
 */
class MPFFormFieldTextarea extends MPFFormField
{

	protected $type = 'Textarea';

	/**
	 * Visable attributes, which will be displayed on field settings form
	 *
	 * @var array
	 */
	public static $visibleProperties = array('rows', 'cols', 'place_holder', 'max_length');

	/**
	 * Required properties, which will be used for js validate before the field is saved
	 *
	 * @var array
	 */
	public static $requiredProperties = array();

	public function __construct($row, $value, $fieldSuffix)
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
		if ($row->rows)
		{
			$this->attributes['rows'] = $row->rows;
		}
		if ($row->cols)
		{
			$this->attributes['cols'] = $row->cols;
		}
	}

	public function getInput($bootstrapHelper = null)
	{
		$attributes = $this->buildAttributes();
		return '<textarea name="' . $this->name . '" id="' . $this->name . '"' . $attributes . $this->extraAttributes . ' >' .
			 htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '</textarea>';
	}
}