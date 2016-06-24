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
class OSMembershipViewTaxHtml extends MPFViewItem
{

	protected function prepareView()
	{
		parent::prepareView();
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id, title')
			->from('#__osmembership_plans')
			->where('published = 1')
			->order('ordering');
		$db->setQuery($query);
		$options                = array();
		$options[]              = JHtml::_('select.option', 0, JText::_('OSM_ALL_PLANS'), 'id', 'title');
		$options                = array_merge($options, $db->loadObjectList());
		$this->lists['plan_id'] = JHtml::_('select.genericlist', $options, 'plan_id', '', 'id', 'title', $this->item->plan_id);

		$query->clear();
		$options   = array();
		$options[] = JHtml::_('select.option', '', JText::_('OSM_ALL_COUNTRIES'));
		$query->select('name AS value, name AS text')
			->from('#__osmembership_countries')
			->where('published = 1')
			->order('name');
		$db->setQuery($query);
		$options                = array_merge($options, $db->loadObjectList());
		$this->lists['country'] = JHtml::_('select.genericlist', $options, 'country', ' class="inputbox" ', 'value', 'text', $this->item->country, 'country');

		$defaultCountry = OSMembershipHelper::getConfigValue('default_country');
		$countryCode    = OSMembershipHelper::getCountryCode($defaultCountry);
		if (OSMembershipHelperEuvat::isEUCountry($countryCode))
		{
			$this->lists['vies'] = JHtml::_('select.booleanlist', 'vies', ' class="inputbox" ', $this->item->vies);
		}

		// States
		$options        = array();
		$stateCountries = array(
			'United States',
			'Canada'
		);
		$options[]      = JHtml::_('select.option', '', 'N/A');
		foreach ($stateCountries as $country)
		{
			$options[] = JHtml::_('select.option', '<OPTGROUP>', $country);
			// Get list of states belong to this country
			$query->clear();
			$query->select('state_2_code AS value, state_name AS text')
				->from('#__osmembership_states AS a')
				->innerJoin('#__osmembership_countries AS b ON a.country_id = b.id ')
				->where('b.name = ' . $db->quote($country));
			$db->setQuery($query);
			$options   = array_merge($options, $db->loadObjectList());
			$options[] = JHtml::_('select.option', '</OPTGROUP>');
		}

		$this->lists['state'] = JHtml::_('select.genericlist', $options, 'state', ' class="inputbox" ', 'value', 'text', $this->item->state, 'state');

		return true;
	}
}