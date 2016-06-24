<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 Ossolution Team
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
class OSMembershipViewTaxesHtml extends MPFViewList
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
		$options[] = JHtml::_('select.option', 0, JText::_('OSM_PLAN'), 'id', 'title');
		$options = array_merge($options, $db->loadObjectList());
		$this->lists['filter_plan_id'] = JHtml::_('select.genericlist', $options, 'filter_plan_id', ' onchange="submit();" ', 'id', 'title', $this->state->filter_plan_id);

		// Build countries dropdown
		$query->clear();
		$options   = array();
		$options[] = JHtml::_('select.option', 0, JText::_('OSM_SELECT_COUNTRY'));
		$query->select('name AS value, name AS text')
			->from('#__osmembership_countries')
			->where('published = 1')
			->order('name');
		$db->setQuery($query);

		$options          = array_merge($options, $db->loadObjectList());
		$this->lists['filter_country'] = JHtml::_('select.genericlist', $options, 'filter_country', ' onchange="submit();" ', 'value', 'text', $this->state->filter_country);

		$defaultCountry = OSMembershipHelper::getConfigValue('default_country');
		$countryCode    = OSmembershipHelper::getCountryCode($defaultCountry);

		if (OSMembershipHelperEuvat::isEUCountry($countryCode))
		{
			$this->showVies = true;
			$options   = array();
			$options[] = JHtml::_('select.option', -1, JText::_('OSM_VIES'));
			$options[] = JHtml::_('select.option', 0, JText::_('OSM_NO'));
			$options[] = JHtml::_('select.option', 1, JText::_('OSM_YES'));
			$this->lists['filter_vies'] = JHtml::_('select.genericlist', $options, 'filter_vies', ' onchange="submit();" ', 'value', 'text', $this->state->filter_vies);
		}
		else
		{
			$this->showVies = false;
		}
	}
}