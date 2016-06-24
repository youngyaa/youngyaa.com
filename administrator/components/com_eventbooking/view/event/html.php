<?php
/**
 * @version            2.0.4
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2015 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die;

class EventbookingViewEventHtml extends RADViewItem
{
	protected function prepareView()
	{
		parent::prepareView();

		$db     = JFactory::getDbo();
		$query  = $db->getQuery(true);
		$item   = $this->item;
		$prices = EventbookingHelperDatabase::getGroupRegistrationRates($item->id);
		$config = EventbookingHelper::getConfig();

		//Locations dropdown
		$options                    = array();
		$options[]                  = JHtml::_('select.option', 0, JText::_('EB_SELECT_LOCATION'), 'id', 'name');
		$options                    = array_merge($options, EventbookingHelperDatabase::getAllLocations());
		$this->lists['location_id'] = JHtml::_('select.genericlist', $options, 'location_id', ' class="inputbox" ', 'id', 'name', $item->location_id);

		// Categories dropdown
		$rows     = EventbookingHelperDatabase::getAllCategories();
		$children = array();
		if ($rows)
		{
			// first pass - collect children
			foreach ($rows as $v)
			{
				$pt   = $v->parent;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push($list, $v);
				$children[$pt] = $list;
			}
		}
		$list    = JHtml::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);
		$options = array();
		foreach ($list as $listItem)
		{
			$options[] = JHtml::_('select.option', $listItem->id, '&nbsp;&nbsp;&nbsp;' . $listItem->treename);
		}
		if ($item->id)
		{
			$query->clear();
			$query->select('category_id')
				->from('#__eb_event_categories')
				->where('event_id=' . $item->id)
				->where('main_category=1');
			$db->setQuery($query);
			$mainCategoryId = $db->loadResult();
			$query->clear();
			$query->select('category_id')
				->from('#__eb_event_categories')
				->where('event_id=' . $item->id)
				->where('main_category=0');
			$db->setQuery($query);
			$additionalCategories = $db->loadColumn();
		}
		else
		{
			$mainCategoryId       = 0;
			$additionalCategories = array();
		}
		$this->lists['main_category_id']         = JHtml::_('select.genericlist', $options, 'main_category_id', array(
			'option.text.toHtml' => false,
			'option.text'        => 'text',
			'option.value'       => 'value',
			'list.attr'          => '',
			'list.select'        => $mainCategoryId
		));
		$this->lists['category_id']              = JHtml::_('select.genericlist', $options, 'category_id[]', array(
			'option.text.toHtml' => false,
			'option.text'        => 'text',
			'option.value'       => 'value',
			'list.attr'          => 'class="inputbox"  size="5" multiple="multiple"',
			'list.select'        => $additionalCategories
		));
		$options                                 = array();
		$options[]                               = JHtml::_('select.option', 1, JText::_('%'));
		$options[]                               = JHtml::_('select.option', 2, $config->currency_symbol);
		$this->lists['discount_type']            = JHtml::_('select.genericlist', $options, 'discount_type', ' class="input-small" ', 'value', 'text', $item->discount_type);
		$this->lists['early_bird_discount_type'] = JHtml::_('select.genericlist', $options, 'early_bird_discount_type', 'class="input-small"', 'value', 'text', $item->early_bird_discount_type);
		$this->lists['late_fee_type'] = JHtml::_('select.genericlist', $options, 'late_fee_type', 'class="input-small"', 'value', 'text', $item->late_fee_type);
		if ($config->activate_deposit_feature)
		{
			$this->lists['deposit_type'] = JHtml::_('select.genericlist', $options, 'deposit_type', ' class="input-small" ', 'value', 'text', $item->deposit_type);
		}
		if (!$item->id)
		{
			$item->registration_type = $config->registration_type;
		}
		$options                          = array();
		$options[]                        = JHtml::_('select.option', 0, JText::_('EB_INDIVIDUAL_GROUP'));
		$options[]                        = JHtml::_('select.option', 1, JText::_('EB_INDIVIDUAL_ONLY'));
		$options[]                        = JHtml::_('select.option', 2, JText::_('EB_GROUP_ONLY'));
		$options[]                        = JHtml::_('select.option', 3, JText::_('EB_DISABLE_REGISTRATION'));
		$this->lists['registration_type'] = JHtml::_('select.genericlist', $options, 'registration_type', ' class="input-xlarge" ', 'value', 'text', $item->registration_type);

		$options                      = array();
		$options[]                    = JHtml::_('select.option', 0, JText::_('EB_USE_GLOBAL_CONFIGURATION'));
		$options[]                    = JHtml::_('select.option', 1, JText::_('EB_INDIVIDUAL_ONLY'));
		$options[]                    = JHtml::_('select.option', 2, JText::_('EB_GROUP_ONLY'));
		$options[]                    = JHtml::_('select.option', 3, JText::_('EB_INDIVIDUAL_GROUP'));
		$this->lists['enable_coupon'] = JHtml::_('select.genericlist', $options, 'enable_coupon', ' class="inputbox" ', 'value', 'text', $item->enable_coupon);

		$this->lists['access']                     = JHtml::_('access.level', 'access', $item->access, 'class="inputbox"', false);
		$this->lists['registration_access']        = JHtml::_('access.level', 'registration_access', $item->registration_access, 'class="inputbox"', false);
		$this->lists['enable_cancel_registration'] = JHtml::_('select.booleanlist', 'enable_cancel_registration', ' class="inputbox" ', $item->enable_cancel_registration);
		$this->lists['enable_auto_reminder']       = JHtml::_('select.booleanlist', 'enable_auto_reminder', ' class="inputbox" ', $item->enable_auto_reminder);
		$this->lists['published']                  = JHtml::_('select.booleanlist', 'published', ' class="inputbox" ', $item->published);
		if ($item->event_date != $db->getNullDate())
		{
			$selectedHour   = date('G', strtotime($item->event_date));
			$selectedMinute = date('i', strtotime($item->event_date));
		}
		else
		{
			$selectedHour   = 0;
			$selectedMinute = 0;
		}
		$this->lists['event_date_hour']   = JHtml::_('select.integerlist', 0, 23, 1, 'event_date_hour', ' class="inputbox input-mini" ', $selectedHour);
		$this->lists['event_date_minute'] = JHtml::_('select.integerlist', 0, 55, 5, 'event_date_minute', ' class="inputbox input-mini" ', $selectedMinute, '%02d');
		if ($item->event_end_date != $db->getNullDate())
		{
			$selectedHour   = date('G', strtotime($item->event_end_date));
			$selectedMinute = date('i', strtotime($item->event_end_date));
		}
		else
		{
			$selectedHour   = 0;
			$selectedMinute = 0;
		}
		$this->lists['event_end_date_hour']   = JHtml::_('select.integerlist', 0, 23, 1, 'event_end_date_hour', ' class="inputbox input-mini" ', $selectedHour);
		$this->lists['event_end_date_minute'] = JHtml::_('select.integerlist', 0, 55, 5, 'event_end_date_minute', ' class="inputbox input-mini" ', $selectedMinute, '%02d');

		// Cut off time
		if ($item->cut_off_date != $db->getNullDate())
		{
			$selectedHour   = date('G', strtotime($item->cut_off_date));
			$selectedMinute = date('i', strtotime($item->cut_off_date));
		}
		else
		{
			$selectedHour   = 0;
			$selectedMinute = 0;
		}
		$this->lists['cut_off_hour']   = JHtml::_('select.integerlist', 0, 23, 1, 'cut_off_hour', ' class="inputbox input-mini" ', $selectedHour);
		$this->lists['cut_off_minute'] = JHtml::_('select.integerlist', 0, 55, 5, 'cut_off_minute', ' class="inputbox input-mini" ', $selectedMinute, '%02d');

		// Registration start time
		if ($item->registration_start_date != $db->getNullDate())
		{
			$selectedHour   = date('G', strtotime($item->registration_start_date));
			$selectedMinute = date('i', strtotime($item->registration_start_date));
		}
		else
		{
			$selectedHour   = 0;
			$selectedMinute = 0;
		}
		$this->lists['registration_start_hour']   = JHtml::_('select.integerlist', 0, 23, 1, 'registration_start_hour', ' class="inputbox input-mini" ', $selectedHour);
		$this->lists['registration_start_minute'] = JHtml::_('select.integerlist', 0, 55, 5, 'registration_start_minute', ' class="inputbox input-mini" ', $selectedMinute, '%02d');
		//Terms and condition article

		$query->clear();
		$query->select('id, title')
			->from('#__content')
			->where('`state` = 1')
			->order('title');
		$db->setQuery($query);
		$rows                      = $db->loadObjectList();
		$options                   = array();
		$options[]                 = JHtml::_('select.option', 0, JText::_('EB_SELECT_ARTICLE'), 'id', 'title');
		$options                   = array_merge($options, $rows);
		$this->lists['article_id'] = JHtml::_('select.genericlist', $options, 'article_id', 'class="inputbox"', 'id', 'title', $item->article_id);

		$query->clear();
		$query->select('id, CONCAT(username, "(", name, " )") AS name')
			->from('#__users')
			->where('block = 0')
			->order('username');
		$db->setQuery($query);
		$options                   = array();
		$options[]                 = JHtml::_('select.option', '0', JText::_('EB_SELECT_USER'), 'id', 'name');
		$options                   = array_merge($options, $db->loadObjectList());
		$this->lists['created_by'] = JHtml::_('select.genericlist', $options, 'created_by', ' class="inputbox" ', 'id', 'name', $item->created_by);
		$nullDate                  = $db->getNullDate();
		//Custom field handles
		if ($config->event_custom_field)
		{
			$registry = new JRegistry;
			$registry->loadString($item->custom_fields);
			$data         = new stdClass();
			$data->params = $registry->toArray();
			$form         = JForm::getInstance('pmform', JPATH_ROOT . '/components/com_eventbooking/fields.xml', array(), false, '//config');
			$form->bind($data);
			$this->form = $form;
		}

		$query->clear();
		$options   = array();
		$options[] = JHtml::_('select.option', '', JText::_('EB_ALL_PAYMENT_METHODS'), 'id', 'title');
		$query->select('id, title')
			->from('#__eb_payment_plugins')
			->where('published=1');
		$db->setQuery($query);
		$this->lists['payment_methods'] = JHtml::_('select.genericlist', array_merge($options, $db->loadObjectList()), 'payment_methods[]', ' class="inputbox" multiple="multiple" ', 'id', 'title', explode(',', $item->payment_methods));

		$query->clear();
		$query->select('currency_code, currency_name')
			->from('#__eb_currencies')
			->order('currency_name');
		$db->setQuery($query);
		$options                        = array();
		$options[]                      = JHtml::_('select.option', '', JText::_('EB_SELECT_CURRENCY'), 'currency_code', 'currency_name');
		$options                        = array_merge($options, $db->loadObjectList());
		$this->lists['currency_code']   = JHtml::_('select.genericlist', $options, 'currency_code', ' class="inputbox" ', 'currency_code', 'currency_name', $item->currency_code);
		$this->lists['discount_groups'] = JHtml::_('access.usergroup', 'discount_groups[]', explode(',', $item->discount_groups),
			' multiple="multiple" size="6" ', false);

		#Plugin support
		JPluginHelper::importPlugin('eventbooking');
		$dispatcher = JDispatcher::getInstance();
		//Trigger plugins
		$results = $dispatcher->trigger('onEditEvent', array($item));

		$this->prices   = $prices;
		$this->nullDate = $nullDate;
		$this->config   = $config;
		$this->plugins  = $results;
	}
}