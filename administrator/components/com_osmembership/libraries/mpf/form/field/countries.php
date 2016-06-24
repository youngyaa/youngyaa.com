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
 * Supports a custom field which display list of countries
 *
 * @package     Joomla.MPF
 * @subpackage  Form
 */
class MPFFormFieldCountries extends MPFFormFieldList
{

	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'Countries';

	/**
	 * The query.
	 *
	 * @var    string
	 * @since  3.2
	 */
	protected $query;

	public function __construct($row, $value, $fieldSuffix)
	{
		parent::__construct($row, $value, $fieldSuffix);
		
		$this->query = 'SELECT name AS value, name AS text FROM #__osmembership_countries WHERE published = 1 ORDER BY name';
	}

	/**
	 * Method to get the custom field options.
	 * Use the query attribute to supply a query to generate the list.
	 *
	 * @return  array  The field option objects.
	 *
	 */
	protected function getOptions()
	{
		try
		{
			$db = JFactory::getDbo();
			// Set the query and get the result list.
			$db->setQuery($this->query);
			$options = array();
			$options[] = JHtml::_('select.option', '', JText::_('OSM_SELECT_COUNTRY'));
			$options = array_merge($options, $db->loadObjectlist());			
		}
		catch (Exception $e)
		{
			$options = array();
		}
		
		return $options;
	}
}
