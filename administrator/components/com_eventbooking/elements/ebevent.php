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
jimport('joomla.form.formfield');

class JFormFieldEBEvent extends JFormField
{

	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var $_name = 'ebevent';

	function getInput()
	{
		$db = JFactory::getDBO();
		$sql = "SELECT id, title  FROM #__eb_events WHERE published = 1 ORDER BY title ";
		$db->setQuery($sql);
		$options = array();
		$options[] = JHtml::_('select.option', '0', JText::_('Select Event'), 'id', 'title');
		$options = array_merge($options, $db->loadObjectList());
		return JHtml::_('select.genericlist', $options, $this->name, ' class="inputbox" ', 'id', 'title', $this->value);
	}
}
