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
class OSMembershipViewCouponHtml extends MPFViewItem
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
		$options[]              = JHtml::_('select.option', 0, JText::_('OSM_ALL_PLANS'), 'id', 'title');
		$options                = array_merge($options, $db->loadObjectList());
		$this->lists['plan_id'] = JHtml::_('select.genericlist', $options, 'plan_id', '', 'id', 'title', $this->item->plan_id);

		$options                    = array();
		$options[]                  = JHtml::_('select.option', 0, JText::_('%'));
		$options[]                  = JHtml::_('select.option', 1, '$');
		$this->lists['coupon_type'] = JHtml::_('select.genericlist', $options, 'coupon_type', '', 'value', 'text', $this->item->coupon_type);

		$this->nullDate = '0000-00-00';
	}


	protected function addToolbar()
	{
		$layout = $this->getLayout();
		if ($layout == 'default')
		{
			parent::addToolbar();
		}
	}
}