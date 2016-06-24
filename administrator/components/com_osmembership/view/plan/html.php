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
class OSMembershipViewPlanHtml extends MPFViewItem
{

	protected function prepareView()
	{
		parent::prepareView();

		JPluginHelper::importPlugin('osmembership');
		$dispatcher = JDispatcher::getInstance();
		$db         = JFactory::getDbo();
		$query      = $db->getQuery(true);

		$item  = $this->item;
		$lists = &$this->lists;

		//Trigger plugins
		$results                         = $dispatcher->trigger('onEditSubscriptionPlan', array($item));
		$lists['enable_renewal']         = JHtml::_('select.booleanlist', 'enable_renewal', ' class="inputbox" ', $item->enable_renewal);
		$lists['lifetime_membership']    = JHtml::_('select.booleanlist', 'lifetime_membership', ' class="inputbox" ', $item->lifetime_membership);
		$lists['recurring_subscription'] = JHtml::_('select.booleanlist', 'recurring_subscription', ' class="inputbox" ',
			$item->recurring_subscription);
		$lists['thumb']                  = JHtml::_('list.images', 'thumb', $item->thumb, ' ', '/media/com_osmembership/');

		$lists['category_id'] = OSMembershipHelperHtml::buildCategoryDropdown($item->category_id, 'category_id');

		$options                           = array();
		$options[]                         = JHtml::_('select.option', 'D', JText::_('OSM_DAYS'));
		$options[]                         = JHtml::_('select.option', 'W', JText::_('OSM_WEEKS'));
		$options[]                         = JHtml::_('select.option', 'M', JText::_('OSM_MONTHS'));
		$options[]                         = JHtml::_('select.option', 'Y', JText::_('OSM_YEARS'));
		$lists['trial_duration_unit']      = JHtml::_('select.genericlist', $options, 'trial_duration_unit', ' class="input-medium" ', 'value', 'text',
			$item->trial_duration_unit);
		$lists['subscription_length_unit'] = JHtml::_('select.genericlist', $options, 'subscription_length_unit', ' class="input-medium" ', 'value', 'text',
			$item->subscription_length_unit);


		$query->clear();
		$query->select('id, title')
			->from('#__osmembership_plans')
			->where('published = 1')
			->where('id != ' . (int) $item->id)
			->order('ordering');
		$db->setQuery($query);
		$this->plans = $db->loadObjectList();

		//Get list of renew and upgrade options
		if ($item->id > 0)
		{
			$query->clear();
			$query->select('number_days, price')
				->from('#__osmembership_renewrates')
				->where('plan_id = ' . $item->id)
				->order('id');
			$db->setQuery($query);
			$prices = $db->loadObjectList();

			$query->clear();
			$query->select('*')
				->from('#__osmembership_upgraderules')
				->where('from_plan_id = ' . $item->id);
			$db->setQuery($query);
			$upgradeRules = $db->loadObjectList();
		}
		else
		{
			$prices       = array();
			$upgradeRules = array();
		}

		// Terms and condition
		$query->clear();
		$query->select('id, title')
			->from('#__content')
			->order('title');
		$db->setQuery($query);
		$options                                  = array();
		$options[]                                = JHtml::_('select.option', 0, JText::_('OSM_SELECT_ARTICLE'), 'id', 'title');
		$options                                  = array_merge($options, $db->loadObjectList());
		$lists['terms_and_conditions_article_id'] = JHtml::_('select.genericlist', $options, 'terms_and_conditions_article_id', '', 'id', 'title', $item->terms_and_conditions_article_id);

		// Payment methods
		$query->clear();
		$options   = array();
		$options[] = JHtml::_('select.option', '', JText::_('OSM_ALL_PAYMENT_METHODS'), 'id', 'title');
		$query->select('id, title')
			->from('#__osmembership_plugins')
			->where('published=1');
		$db->setQuery($query);
		$lists['payment_methods'] = JHtml::_('select.genericlist', array_merge($options, $db->loadObjectList()), 'payment_methods[]', ' class="inputbox" multiple="multiple" ', 'id', 'title', explode(',', $item->payment_methods));

		// Login redirect
		require_once JPATH_ADMINISTRATOR . '/components/com_menus/helpers/menus.php';

		$groups = array();
		$items  = MenusHelper::getMenuLinks();
		foreach ($items as $menu)
		{
			$groups[$menu->menutype] = array();
			foreach ($menu->links as $link)
			{
				$groups[$menu->menutype][] = JHtml::_('select.option', $link->value, $link->text);
			}
		}
		array_unshift($groups, array(JHtml::_('select.option', 0, JText::_('OSM_SELECT_MENU_ITEM'))));

		$lists['login_redirect_menu_id'] = JHtml::_(
			'select.groupedlist', $groups, 'login_redirect_menu_id',
			array('id'                 => 'menu_item', 'list.select' => $item->login_redirect_menu_id, 'group.items' => null, 'option.key.toHtml' => false,
			      'option.text.toHtml' => false
			)
		);

		// Currency code
		$currencies = require_once JPATH_ROOT . '/components/com_osmembership/helper/currencies.php';
		$options    = array();
		$options[]  = JHtml::_('select.option', '', JText::_('OSM_DEFAULT_CURRENCY'));
		foreach ($currencies as $code => $title)
		{
			$options[] = JHtml::_('select.option', $code, $title);
		}

		$lists['currency'] = JHtml::_('select.genericlist', $options, 'currency', ' class="inputbox" ', 'value', 'text', $item->currency);

		$this->prices       = $prices;
		$this->upgradeRules = $upgradeRules;
		$this->plugins      = $results;
		$this->nullDate     = $db->getNullDate();

		return true;
	}
}