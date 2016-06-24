<?php
/**
 * @package     MPF
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2015 Ossolution Team, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

/**
 * Form Field class for the Joomla MPF.
 * Supports a message form field
 *
 * @package     Joomla.MPF
 * @subpackage  Form
 */
class MPFFormFieldMessage extends MPFFormField
{

	/**
	 * The form field type.
	 *
	 * @var    string
	 *	 
	 */
	protected $type = 'Message';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *	 
	 */
	public function getInput($bootstrapHelper = null)
	{
		$controlGroupAttributes = 'id="field_' . $this->name . '" ';

		if (!$this->visible)
		{
			$controlGroupAttributes .= ' style="display:none;" ';
		}

		return '<div class="control-group osm-message" ' . $controlGroupAttributes . '>' . $this->description . '</div>';
	}

	public function getControlGroup($bootstrapHelper = null)
	{
		return $this->getInput($bootstrapHelper);
	}
}