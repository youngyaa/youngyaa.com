<?php
/**
 * @version            2.0.4
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2015 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die;
jimport('joomla.form.formfield');

class JFormFieldEBLocation extends JFormField
{

	/**
	 * Element name
	 *
	 * @access    protected
	 * @var        string
	 */
	var $_name = 'eblocation';

	function getInput()
	{
		require_once JPATH_ROOT . '/components/com_eventbooking/helper/database.php';
		$options   = array();
		$options[] = JHtml::_('select.option', '0', JText::_('Select Location'), 'id', 'name');
		$options   = array_merge($options, EventbookingHelperDatabase::getAllLocations());

		return JHtml::_('select.genericlist', $options, $this->name, '', 'id', 'name', $this->value);
	}
}
