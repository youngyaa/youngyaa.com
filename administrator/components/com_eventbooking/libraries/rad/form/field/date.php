<?php

class RADFormFieldDate extends RADFormField
{

	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 */
	protected $type = 'Date';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 */
	protected function getInput($bootstrapHelper = null)
	{
		$config     = EventbookingHelper::getConfig();
		$dateFormat = $config->date_field_format ? $config->date_field_format : '%Y-%m-%d';
		$attributes = $this->buildAttributes();
		try
		{
			return JHtml::_('calendar', $this->value, $this->name, $this->name, $dateFormat, ".$attributes.");
		}
		catch (Exception $e)
		{
			return JHtml::_('calendar', '', $this->name, $this->name, $dateFormat, ".$attributes.") . ' Value <strong>' . $this->value . '</strong> is invalid. Please correct it with format YYYY-MM-DD';
		}
	}
}

;