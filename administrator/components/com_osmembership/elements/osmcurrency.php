<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die();

class JFormFieldOSMCurrency extends JFormField
{

	/**
	 * Element name
	 *
	 * @access    protected
	 * @var        string
	 */
	var $_name = 'osmcurrency';

	function getInput()
	{
		$db  = JFactory::getDbo();
		$sql = "SELECT currency_code, currency_name  FROM #__osmembership_currencies ORDER BY currency_name ";
		$db->setQuery($sql);
		$options   = array();
		$options[] = JHtml::_('select.option', '', JText::_('Select Currency'), 'currency_code', 'currency_name');
		$options   = array_merge($options, $db->loadObjectList());
		$options[] = JHtml::_('select.option', 'TRY', JText::_('Turkish Lira'), 'currency_code', 'currency_name');

		return JHtml::_('select.genericlist', $options, $this->name, ' class="inputbox" ', 'currency_code', 'currency_name', $this->value);
	}
}