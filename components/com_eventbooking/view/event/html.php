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

class EventbookingViewEventHtml extends RADViewHtml
{

	public function display()
	{
		$layout = $this->getLayout();
		if ($layout == 'form')
		{
			$this->displayForm();

			return;
		}

		$app    = JFactory::getApplication();
		$active = $app->getMenu()->getActive();
		$user   = JFactory::getUser();
		$config = EventbookingHelper::getConfig();
		$model  = $this->getModel();
		$state  = $model->getState();
		$item   = $model->getEventData();

		// Check to make sure the event is valid and user is allowed to access to it
		if (empty($item) || !in_array($item->access, $user->getAuthorisedViewLevels()))
		{
			$app->redirect('index.php', JText::_('EB_INVALID_EVENT'));
		}

		//Use short description in case user don't enter long description
		if (strlen(trim(strip_tags($item->description))) == 0)
		{
			$item->description = $item->short_description;
		}

		$categoryId = $state->catid;
		if ($active)
		{
			$pathway = $app->getPathway();
			if (isset($active->query['view']) && ($active->query['view'] == 'categories' || $active->query['view'] == 'category'))
			{
				$parentId = (int) $active->query['id'];
				if ($categoryId)
				{
					$paths = EventbookingHelperData::getCategoriesBreadcrumb($categoryId, $parentId);
					for ($i = count($paths) - 1; $i >= 0; $i--)
					{
						$category = $paths[$i];
						$pathUrl  = EventbookingHelperRoute::getCategoryRoute($category->id, $this->Itemid);
						$pathway->addItem($category->name, $pathUrl);
					}
					$pathway->addItem($item->title);
				}
			}
			elseif (isset($active->query['view']) && ($active->query['view'] == 'calendar'))
			{
				$pathway->addItem($item->title);
			}
		}

		$item->description = JHtml::_('content.prepare', $item->description);

		if ($item->location_id)
		{
			$this->location = EventbookingHelperDatabase::getLocation($item->location_id);
		}

		if ($config->event_custom_field && file_exists(JPATH_ROOT . '/components/com_eventbooking/fields.xml'))
		{
			EventbookingHelperData::prepareCustomFieldsData(array($item));
			$this->paramData = $item->paramData;
		}

		$params = EventbookingHelper::getViewParams($active, array('event'));

		// Process page meta data
		if (!$params->get('page_title'))
		{
			$pageTitle = JText::_('EB_EVENT_PAGE_TITLE');
			$pageTitle = str_replace('[EVENT_TITLE]', $item->title, $pageTitle);
			$category  = EventbookingHelperDatabase::getCategory($item->category_id);
			$pageTitle = str_replace('[CATEGORY_NAME]', $category->name, $pageTitle);
			$params->set('page_title', $pageTitle);
		}

		EventbookingHelperHtml::prepareDocument($params, $item);

		if ($config->multiple_booking)
		{
			EventbookingHelperJquery::colorbox('eb-colorbox-addcart', '800px', '450px', 'false', 'false');
		}

		if ($config->show_list_of_registrants)
		{
			EventbookingHelperJquery::colorbox('eb-colorbox-register-lists');
		}

		$width = (int) $config->map_width;
		if (!$width)
		{
			$width = 800;
		}
		$height = (int) $config->map_height;
		if (!$height)
		{
			$height = 600;
		}

		EventbookingHelperJquery::colorbox('eb-colorbox-map', $width . 'px', $height . 'px', 'true', 'false');
		if ($config->show_invite_friend)
		{
			EventbookingHelperJquery::colorbox('eb-colorbox-invite');
		}

		JPluginHelper::importPlugin('eventbooking');
		$dispatcher = JDispatcher::getInstance();
		$plugins    = $dispatcher->trigger('onEventDisplay', array($item));

		if ($this->input->get('tmpl', '') == 'component')
		{
			$this->showTaskBar = false;
		}
		else
		{
			$this->showTaskBar = true;
		}

		$this->viewLevels      = $user->getAuthorisedViewLevels();
		$this->item            = $item;
		$this->config          = $config;
		$this->userId          = $user->id;
		$this->plugins         = $plugins;
		$this->nullDate        = JFactory::getDbo()->getNullDate();
		$this->rowGroupRates   = EventbookingHelperDatabase::getGroupRegistrationRates($item->id);
		$this->bootstrapHelper = new EventbookingHelperBootstrap($config->twitter_bootstrap_version);

		parent::display();
	}

	/**
	 * Display form which allows add/edit event
	 *
	 * @throws Exception
	 */
	protected function displayForm()
	{
		$db          = JFactory::getDbo();
		$query       = $db->getQuery(true);
		$user        = JFactory::getUser();
		$item        = $this->model->getData();
		$config      = EventbookingHelper::getConfig();
		$fieldSuffix = EventbookingHelper::getFieldSuffix();
		if ($config->submit_event_form_layout == 'simple')
		{
			$this->setLayout('simple');
		}
		if ($item->id)
		{
			$ret = EventbookingHelper::checkEditEvent($item->id);
		}
		else
		{
			$ret = EventbookingHelper::checkAddEvent();
		}
		if (!$ret)
		{
			//Redirect users to login page if they are not logged in
			$user = JFactory::getUser();
			if (!$user->id)
			{
				$currentUrl = JUri::current();
				JFactory::getApplication()->redirect('index.php?option=com_users&view=login&return=' . base64_encode($currentUrl));
			}
			else
			{
				$url = JRoute::_('index.php');
				JFactory::getApplication()->redirect($url, JText::_('EB_NO_ADDING_EVENT_PERMISSION'));
			}
		}

		$prices = EventbookingHelperDatabase::getGroupRegistrationRates($item->id);

		//Get list of location
		$options = array();
		$query->select('id, name')
			->from('#__eb_locations')
			->where('published = 1')
			->order('name');
		if (!$config->show_all_locations_in_event_submission_form)
		{
			$query->where('user_id = ' . (int) $user->id);
		}
		$db->setQuery($query);
		$options[]            = JHtml::_('select.option', '', JText::_('Select Location'), 'id', 'name');
		$options              = array_merge($options, $db->loadObjectList());
		$lists['location_id'] = JHtml::_('select.genericlist', $options, 'location_id', '', 'id', 'name', $item->location_id);

		// Categories dropdown
		$query->clear();
		$query->select("id, parent, parent AS parent_id, name" . $fieldSuffix . " AS name, name" . $fieldSuffix . " AS title")
			->from('#__eb_categories')
			->where('published = 1')
			->order('name' . $fieldSuffix);
		$db->setQuery($query);
		$rows     = $db->loadObjectList();
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

		$lists['main_category_id']           = JHtml::_('select.genericlist', $options, 'main_category_id',
			array(
				'option.text.toHtml' => false,
				'option.text'        => 'text',
				'option.value'       => 'value',
				'list.attr'          => '',
				'list.select'        => $mainCategoryId));
		$lists['category_id']                = JHtml::_('select.genericlist', $options, 'category_id[]',
			array(
				'option.text.toHtml' => false,
				'option.text'        => 'text',
				'option.value'       => 'value',
				'list.attr'          => 'class="inputbox"  size="5" multiple="multiple"',
				'list.select'        => $additionalCategories));
		$options                             = array();
		$options[]                           = JHtml::_('select.option', 1, JText::_('%'));
		$options[]                           = JHtml::_('select.option', 2, $config->currency_symbol);
		$lists['discount_type']              = JHtml::_('select.genericlist', $options, 'discount_type', ' class="input-mini" ', 'value', 'text',
			$item->discount_type);
		$lists['early_bird_discount_type']   = JHtml::_('select.genericlist', $options, 'early_bird_discount_type', ' class="input-mini" ', 'value',
			'text', $item->early_bird_discount_type);
		$options                             = array();
		$options[]                           = JHtml::_('select.option', 0, JText::_('EB_INDIVIDUAL_GROUP'));
		$options[]                           = JHtml::_('select.option', 1, JText::_('EB_INDIVIDUAL_ONLY'));
		$options[]                           = JHtml::_('select.option', 2, JText::_('EB_GROUP_ONLY'));
		$options[]                           = JHtml::_('select.option', 3, JText::_('EB_DISABLE_REGISTRATION'));
		$lists['registration_type']          = JHtml::_('select.genericlist', $options, 'registration_type', ' class="inputbox" ', 'value', 'text',
			$item->registration_type);
		$lists['access']                     = JHtml::_('access.level', 'access', $item->access, 'class="inputbox"', false);
		$lists['registration_access']        = JHtml::_('access.level', 'registration_access', $item->registration_access, 'class="inputbox"', false);
		$lists['enable_cancel_registration'] = JHtml::_('select.booleanlist', 'enable_cancel_registration', ' class="inputbox" ',
			$item->enable_cancel_registration);
		$lists['enable_auto_reminder']       = JHtml::_('select.booleanlist', 'enable_auto_reminder', ' class="inputbox" ', $item->enable_auto_reminder);

		$lists['published'] = JHtml::_('select.booleanlist', 'published', ' class="inputbox" ', $item->published);
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
		$lists['event_date_hour']   = JHtml::_('select.integerlist', 0, 23, 1, 'event_date_hour', ' class="input-mini" ', $selectedHour);
		$lists['event_date_minute'] = JHtml::_('select.integerlist', 0, 55, 5, 'event_date_minute', ' class="input-mini" ', $selectedMinute, '%02d');
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
		$lists['event_end_date_hour']   = JHtml::_('select.integerlist', 0, 23, 1, 'event_end_date_hour', ' class="input-mini" ', $selectedHour);
		$lists['event_end_date_minute'] = JHtml::_('select.integerlist', 0, 55, 5, 'event_end_date_minute', ' class="input-mini" ', $selectedMinute,
			'%02d');

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
		$lists['registration_start_hour']   = JHtml::_('select.integerlist', 0, 23, 1, 'registration_start_hour', ' class="inputbox input-mini" ', $selectedHour);
		$lists['registration_start_minute'] = JHtml::_('select.integerlist', 0, 55, 5, 'registration_start_minute', ' class="inputbox input-mini" ', $selectedMinute, '%02d');

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

		//Custom field handles
		if ($config->event_custom_field)
		{
			$registry = new JRegistry();
			$registry->loadString($item->custom_fields);
			$data         = new stdClass();
			$data->params = $registry->toArray();
			$form         = JForm::getInstance('pmform', JPATH_ROOT . '/components/com_eventbooking/fields.xml', array(), false, '//config');
			$form->bind($data);
			$this->form = $form;
		}
		$this->item     = $item;
		$this->prices   = $prices;
		$this->lists    = $lists;
		$this->nullDate = $db->getNullDate();
		$this->config   = $config;
		$this->return   = $this->input->getString('return');

		parent::display();
	}
}