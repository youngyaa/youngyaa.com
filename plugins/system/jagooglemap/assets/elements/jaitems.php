<?php
/**
 * ------------------------------------------------------------------------
 * JA System Google Map plugin for Joomla 2.5 & J3.4
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */

defined('JPATH_BASE') or die;

/**
 * Supports a modal contact picker.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_contact
 * @since       1.6
 */
class JFormFieldJaitems extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since   1.6
	 */
	protected $type = 'Jaitems';

	protected function getLabel()
	{
		return '';
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		$doc = JFactory::getDocument();

		$url = JURI::root(true) . '/plugins/system/jagooglemap/assets/elements/jaitems/';
		$doc->addScript($url.'script.js');
		$doc->addScript($url.'jalist.js');
		$doc->addStyleSheet($url.'style.css');
		//$doc->addStyleSheet($url.'jalist.css');


		$options = array(
			'field' => $this,
			'attributes' => $this->element,
			'items' => $this->getItems()
		);
		//$layout = new JLayoutFile('items', JPATH_ROOT.'/plugins/system/jagooglemap/layouts');
		//return $layout->render($options);
		return $this->renderLayout(JPATH_ROOT.'/plugins/system/jagooglemap/layouts/items.php', $options);

	}

	function getItems() {
		$items = array();
		foreach ($this->element->children() as $element)
		{
			// clone element to make it as field
			$fdata = preg_replace ('/<(\/?)item(\s|>)/mi', '<\1field\2', $element->asXML());
			// remove cols, rows, size attributes
			//$fdata = preg_replace ('/\s(cols|rows|size)=(\'|")\d+(\'|")/mi', '', $fdata);
			// change type text to textarea
			//$fdata = str_replace ('type="text"', 'type="textarea"', $fdata);

			$felement = new SimpleXMLElement($fdata);
			$field = JFormHelper::loadFieldType((string)$felement['type']);
			if ($field === false) {
				$field = JFormHelper::loadFieldType('text');
			}
			// Setup the JFormField object.
			$field->setForm($this->form);
			if ($field->setup($felement, null, $this->group.'.'.$this->fieldname)) {
				$items[] = $field;
			}
		}

		return $items;
	}

	public function renderLayout($path, $displayData)
	{
		$layoutOutput = '';

		// If there exists such a layout file, include it and collect its output
		if (!empty($path))
		{
			ob_start();
			include $path;
			$layoutOutput = ob_get_contents();
			ob_end_clean();
		}

		return $layoutOutput;
	}
}
