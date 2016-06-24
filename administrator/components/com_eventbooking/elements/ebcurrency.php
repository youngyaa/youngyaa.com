<?php
/**
 * @version        	2.0.0
 * @package        	Joomla
 * @subpackage		Event Booking
 * @author  		Tuan Pham Ngoc
 * @copyright    	Copyright (C) 2010 - 2015 Ossolution Team
 * @license        	GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die();

class JFormFieldEBCurrency extends JFormField
{

	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var $_name = 'ebcurrency';

	function getInput()
	{
		$db = JFactory::getDBO();
		$sql = "SELECT currency_code, currency_name  FROM #__eb_currencies ORDER BY currency_name ";
		$db->setQuery($sql);
		$options = array();
		$options[] = JHtml::_('select.option', '', JText::_('Select Currency'), 'currency_code', 'currency_name');
		$options = array_merge($options, $db->loadObjectList());
		
		return JHtml::_('select.genericlist', $options, $this->name, ' class="inputbox" ', 'currency_code', 'currency_name', $this->value);
	}
}   
