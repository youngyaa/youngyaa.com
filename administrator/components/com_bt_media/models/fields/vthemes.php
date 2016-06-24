<?php
/**
 * @package 	bt_portfolio - BT Portfolio Component
 * @version		1.2.6
 * @created		Feb 2012
 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// No direct access to this file
defined('_JEXEC') or die;
 
// import the list field type
jimport('joomla.form.helper');
jimport('joomla.filesystem.folder');
JFormHelper::loadFieldClass('list');
 
/**
 * PortfolioCategory Form Field class for the bt_portfolio component
 */
class JFormFieldVThemes extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'VThemes';
 
	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$options = array();

		// Get the path in which to search for file options.
		$path = (string) $this->element['path'];
		if (!is_dir($path)) {
			$path = JPATH_ROOT.'/'.$path;
		}

		// Get a list of folders in the search path with the given filter.
		$folders = JFolder::folders($path);

		// Build the options list from the list of folders.
		if (is_array($folders)) {
			foreach($folders as $folder) {
				$options[] = JHtml::_('select.option', $folder, $folder);
			}
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}