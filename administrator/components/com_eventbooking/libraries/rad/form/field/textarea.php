<?php
/**
 * Form Field class for the Joomla RAD.
 * Supports a textarea inut.
 *
 * @package     Joomla.RAD
 * @subpackage  Form
 */
class RADFormFieldTextarea extends RADFormField
{

	protected $type = 'Textarea';

	/**
	 * Method to instantiate the form field object.
	 *
	 * @param   JTable  $row  the table object store form field definitions
	 * @param	mixed	$value the initial value of the form field
	 *
	 */
	public function __construct($row, $value)
	{
		parent::__construct($row, $value);
		if ($row->place_holder)
		{
			$this->attributes['placeholder'] = $row->place_holder;
		}
		if ($row->max_length)
		{
			$this->attributes['maxlength'] = $row->max_length;
		}
		if ($row->rows > 0)
		{
			$this->attributes['rows'] = $row->rows;
		}
		if ($row->cols > 0)
		{
			$this->attributes['cols'] = $row->cols;
		}
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 */
	public function getInput($bootstrapHelper = null)
	{
		$attributes = $this->buildAttributes();
		return '<textarea name="' . $this->name . '" id="' . $this->name . '"' . $attributes . $this->row->extra_attributes . ' >' .
			 htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '</textarea>';
	}
}