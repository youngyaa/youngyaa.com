<?php
/**
 * Supports a custom field which display list of countries
 *
 * @package     Joomla.RAD
 * @subpackage  Form
 */
class RADFormFieldCountries extends RADFormFieldList
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
	 */
	protected $query;
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
		
		$this->query = 'SELECT name AS value, name AS text FROM #__eb_countries WHERE published = 1 ORDER BY name';
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
			$options[] = JHtml::_('select.option', '', JText::_('EB_SELECT_COUNTRY'));
			$options = $db->loadObjectlist();
		}
		catch (Exception $e)
		{
			$options = array();
		}
		
		return $options;
	}
}
