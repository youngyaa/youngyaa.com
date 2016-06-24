<?php

class RADFormFieldHeading extends RADFormField
{

	/**
	 * The form field type.
	 *
	 * @var string
	 *
	 */
	protected $type = 'Heading';

	/**
	 * Method to get the field input markup.
	 *
	 * @return string The field input markup.
	 *        
	 */
	protected function getInput($bootstrapHelper = null)
	{
		$controlGroupAttributes = 'id="field_' . $this->name . '" ';
		if ($this->hideOnDisplay)
		{
			$controlGroupAttributes .= ' style="display:none;" ';
		}
		return '<h3 class="eb-heading" ' . $controlGroupAttributes . '>' . $this->title . '</h3>';
	}

	/**
	 * Get control group used to display on form
	 *
	 * @see RADFormField::getControlGroup()
	 */
	public function getControlGroup($bootstrapHelper = null)
	{
		return $this->getInput($bootstrapHelper = null);
	}

	/**
	 * Get output used for displaying on email and the detail page
	 *
	 * @see RADFormField::getOutput()
	 */
	public function getOutput($tableLess = true, $bootstrapHelper = null)
	{
		if ($tableLess)
		{
			return $this->getInput($bootstrapHelper);
		}
		else
		{
			return '<tr>' . '<td class="eb-heading" colspan="2">' . $this->title . '</td></tr>';
		}
	}
}