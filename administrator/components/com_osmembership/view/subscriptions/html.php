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
class OSMembershipViewSubscriptionsHtml extends MPFViewList
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
		$this->lists['plan_id'] = JHtml::_('select.genericlist', $options, 'plan_id', ' class="inputbox" onchange="submit();" ', 'id', 'title', $this->state->plan_id);

		$options = array();
		$options[] = JHtml::_('select.option', 0, JText::_('OSM_ALL_SUBSCRIPTIONS'));
		$options[] = JHtml::_('select.option', 1, JText::_('OSM_NEW_SUBSCRIPTION'));
		$options[] = JHtml::_('select.option', 2, JText::_('OSM_SUBSCRIPTION_RENEWAL'));
		$options[] = JHtml::_('select.option', 3, JText::_('OSM_SUBSCRIPTION_UPGRADE'));
		$this->lists['subscription_type'] = JHtml::_('select.genericlist', $options, 'subscription_type', ' class="inputbox" onchange="submit();" ', 'value', 'text', $this->state->subscription_type);

		$options = array();
		$options[] = JHtml::_('select.option', -1, JText::_('OSM_ALL'));
		$options[] = JHtml::_('select.option', 0, JText::_('OSM_PENDING'));
		$options[] = JHtml::_('select.option', 1, JText::_('OSM_ACTIVE'));
		$options[] = JHtml::_('select.option', 2, JText::_('OSM_EXPIRED'));
		$options[] = JHtml::_('select.option', 3, JText::_('OSM_CANCELLED_PENDING'));
		$options[] = JHtml::_('select.option', 4, JText::_('OSM_CANCELLED_REFUNDED'));
		$this->lists['published'] = JHtml::_('select.genericlist', $options, 'published', ' class="input-box" onchange="submit();" ', 'value', 'text', $this->state->published);
		$this->config = OSMembershipHelper::getConfig();
	}
}