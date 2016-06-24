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
defined('_JEXEC') or die;
jimport('joomla.form.formfield');

class JFormFieldOSMPlan extends JFormField
{
	/**
	 * Element name
	 *
	 * @access    protected
	 * @var        string
	 */
	var $_name = 'osmplan';

	function getInput()
	{
		$db  = JFactory::getDbo();
		$sql = "SELECT id, title  FROM #__osmembership_plans WHERE published = 1 ORDER BY ordering ";
		$db->setQuery($sql);
		$options   = array();
		$options[] = JHtml::_('select.option', '0', JText::_('Select Plan'), 'id', 'title');
		$options   = array_merge($options, $db->loadObjectList());

		return JHtml::_('select.genericlist', $options, $this->name, ' class="inputbox" ', 'id', 'title', $this->value);
	}
}
