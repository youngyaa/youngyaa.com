<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     OSMembership
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die();

class OSMembershipViewFieldRaw extends MPFViewHtml
{

	public function display()
	{
		$this->setLayout('options');
		$db      = JFactory::getDbo();
		$query   = $db->getQuery(true);
		$fieldId = JFactory::getApplication()->input->getInt('field_id');
		$query->select('`values`')
			->from('#__osmembership_fields')
			->where('id=' . $fieldId);
		$db->setQuery($query);
		$options       = explode("\r\n", $db->loadResult());
		$this->options = $options;

		parent::display();
	}
}