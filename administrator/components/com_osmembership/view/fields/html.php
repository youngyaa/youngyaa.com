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
/**
 * HTML View class for OS Membership Component
 *
 * @static
 * @package        Joomla
 * @subpackage     Membership Pro
 */
class OSMembershipViewFieldsHtml extends MPFViewList
{

	protected function prepareView()
	{
		parent::prepareView();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id, title')
			->from('#__osmembership_plans')
			->where('published = 1')
			->order('ordering');
		$db->setQuery($query);
		$options = array();
		$options[] = JHtml::_('select.option', 0, JText::_('OSM_ALL_PLANS'), 'id', 'title');
		$options = array_merge($options, $db->loadObjectList());
		$this->lists['plan_id'] = JHtml::_('select.genericlist', $options, 'plan_id', ' onchange="submit();" ', 'id', 'title',
			$this->state->plan_id);

		$options = array();
		$options[] = JHtml::_('select.option', 1, JText::_('Show Core Fields'));
		$options[] = JHtml::_('select.option', 2, JText::_('Hide Core Fields'));
		$this->lists['show_core_field'] = JHtml::_('select.genericlist', $options, 'show_core_field', ' class="input-medium" onchange="submit();" ', 'value',
			'text', $this->state->show_core_field);
	}
}