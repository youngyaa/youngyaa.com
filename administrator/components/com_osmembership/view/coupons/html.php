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
 * HTML View class for Membership Pro component
 *
 * @static
 * @package        Joomla
 * @subpackage     Membership Pro
 */
class OSMembershipViewCouponsHtml extends MPFViewList
{

	protected function prepareView()
	{
		parent::prepareView();

		$db            = JFactory::getDbo();
		$config        = OSMembershipHelper::getConfig();
		$discountTypes = array(0 => '%', 1 => $config->currency_symbol);
		$query         = $db->getQuery(true);
		$query->select('id, title')
			->from('#__osmembership_plans')
			->where('published = 1')
			->order('ordering');
		$db->setQuery($query);
		$options                       = array();
		$options[]                     = JHtml::_('select.option', 0, JText::_('OSM_PLAN'), 'id', 'title');
		$options                       = array_merge($options, $db->loadObjectList());
		$this->lists['filter_plan_id'] = JHtml::_('select.genericlist', $options, 'filter_plan_id', ' onchange="submit();" ', 'id', 'title',
			$this->state->filter_plan_id);

		$this->dateFormat    = $config->date_format;
		$this->nullDate      = $db->getNullDate();
		$this->discountTypes = $discountTypes;
	}
}