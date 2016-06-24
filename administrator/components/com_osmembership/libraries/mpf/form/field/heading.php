<?php
/**
 * @package     MPF
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2015 Ossolution Team, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die();

class MPFFormFieldHeading extends MPFFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *	 
	 */
	protected  $type = 'Heading';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *	 
	 */
	protected function getInput($bootstrapHelper = null)
	{
		$controlGroupAttributes = 'id="field_' . $this->name . '" ';

		if (!$this->visible)
		{
			$controlGroupAttributes .= ' style="display:none;" ';
		}

		return '<h3 class="osm-heading" ' . $controlGroupAttributes . '>' . JText::_($this->title) . '</h3>';
	}


	public function getControlGroup($bootstrapHelper = null)
	{
		return $this->getInput($bootstrapHelper);
	}
}