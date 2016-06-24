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
defined('_JEXEC') or die();

class EventbookingViewCategoryHtml extends RADViewHtml
{

	public function display()
	{
		$app        = JFactory::getApplication();
		$session    = JFactory::getSession();
		$user       = JFactory::getUser();
		$active     = $app->getMenu()->getActive();
		$config     = EventbookingHelper::getConfig();
		$model      = $this->getModel();
		$state      = $model->getState();
		$categoryId = $state->id;
		if ($categoryId)
		{
			$category = EventbookingHelperDatabase::getCategory($categoryId);
			if (empty($category) || !in_array($category->access, JFactory::getUser()->getAuthorisedViewLevels()))
			{
				$app->redirect('index.php', JText::_('EB_INVALID_CATEGORY_OR_NOT_AUTHORIZED'));
			}
		}
		else
		{
			$category = null;
		}
		$items      = $model->getData();
		$pagination = $model->getPagination();
		if ($config->process_plugin)
		{
			for ($i = 0, $n = count($items); $i < $n; $i++)
			{
				$item                    = $items[$i];
				$item->short_description = JHtml::_('content.prepare', $item->short_description);
			}
			if (!empty($category))
			{
				$category->description = JHtml::_('content.prepare', $category->description);
			}
		}

		if ($config->event_custom_field && $config->show_event_custom_field_in_category_layout)
		{
			EventbookingHelperData::prepareCustomFieldsData($items);
		}

		//Handle breadcrumb
		if ($active)
		{
			if (isset($active->query['view']) && ($active->query['view'] == 'categories' || $active->query['view'] == 'category'))
			{
				$parentId = (int) $active->query['id'];
				if ($state->id)
				{
					$pathway = $app->getPathway();
					$paths   = EventbookingHelperData::getCategoriesBreadcrumb($state->id, $parentId);
					for ($i = count($paths) - 1; $i >= 0; $i--)
					{
						$path    = $paths[$i];
						$pathUrl = EventbookingHelperRoute::getCategoryRoute($path->id, $this->Itemid);
						$pathway->addItem($path->name, $pathUrl);
					}
				}
			}
		}

		$session->set('last_category_id', $categoryId);

		//Override layout for this category
		$layout = $this->getLayout();
		if ($layout == '' || $layout == 'default')
		{
			if (!empty($category->layout))
			{
				$this->setLayout($category->layout);
			}
		}

		$layout = $this->getLayout();
		if ($layout == 'calendar')
		{
			$this->displayCalendar();

			return;
		}

		// Load sub-categories of the current category
		if ($categoryId > 0)
		{
			JLoader::register('EventbookingModelCategories', JPATH_ROOT . '/components/com_eventbooking/models/categories.php');

			$model            = new EventbookingModelCategories(
				array(
					'table_prefix'    => '#__eb_',
					'remember_states' => false,
					'ignore_request'  => true
				)
			);
			$this->categories = $model->setState('limitstart', 0)
				->setState('limit', 0)
				->setState('filter_order', 'tbl.ordering')
				->setState('id', $categoryId)
				->getData();
		}
		else
		{
			$this->categories = array();
		}

		if ($config->multiple_booking)
		{
			EventbookingHelperJquery::colorbox('eb-colorbox-addcart', '800px', '450px', 'false', 'false');
		}

		if ($config->show_list_of_registrants)
		{
			EventbookingHelperJquery::colorbox('eb-colorbox-register-lists');
		}

		if ($config->show_location_in_category_view || ($this->getLayout() == 'timeline'))
		{
			$width  = (int) $config->get('map_width', 800);
			$height = (int) $config->get('map_height', 600);
			EventbookingHelperJquery::colorbox('eb-colorbox-map', $width . 'px', $height . 'px', 'true', 'false');
		}

		// Process page meta data
		$params = EventbookingHelper::getViewParams($active, array('category'));

		if (!$params->get('page_title'))
		{
			if (!empty($category->name))
			{
				$pageTitle = JText::_('EB_CATEGORY_PAGE_TITLE');
				$pageTitle = str_replace('[CATEGORY_NAME]', $category->name, $pageTitle);
				$params->set('page_title', $pageTitle);
			}
		}

		EventbookingHelperHtml::prepareDocument($params, $category);

		$this->viewLevels      = $user->getAuthorisedViewLevels();
		$this->userId          = $user->id;
		$this->items           = $items;
		$this->pagination      = $pagination;
		$this->config          = $config;
		$this->category        = $category;
		$this->nullDate        = JFactory::getDbo()->getNullDate();
		$this->params          = $params;
		$this->bootstrapHelper = new EventbookingHelperBootstrap($config->twitter_bootstrap_version);

		parent::display();
	}

	/**
	 * Display calendar view to user in a category
	 *
	 */
	protected function displayCalendar()
	{
		$config          = EventbookingHelper::getConfig();
		$currentDateData = EventbookingModelCalendar::getCurrentDateData();
		//Initialize default month and year
		$month = $this->input->getInt('month', 0);
		$year  = $this->input->getInt('year', 0);
		if (!$month)
		{
			$month = $this->input->getInt('default_month', 0);
			if (!$month)
			{
				$month = $currentDateData['month'];
			}
		}

		if (!$year)
		{
			$year = $this->input->getInt('default_year', 0);
			if (!$year)
			{
				$year = $currentDateData['year'];
			}
		}
		$categoryId = $this->input->getInt('id');

		$category = EventbookingHelperDatabase::getCategory($categoryId);
		$model    = new EventbookingModelCalendar(array('remember_states' => false, 'ignore_request' => true));
		$model->setState('month', $month)
			->setState('year', $year)
			->setState('id', $categoryId);
		$rows        = $model->getData();
		$this->data  = EventbookingHelperData::getCalendarData($rows, $year, $month);
		$this->month = $month;
		$this->year  = $year;
		$listMonth   = array(
			JText::_('EB_JAN'),
			JText::_('EB_FEB'),
			JText::_('EB_MARCH'),
			JText::_('EB_APR'),
			JText::_('EB_MAY'),
			JText::_('EB_JUNE'),
			JText::_('EB_JULY'),
			JText::_('EB_AUG'),
			JText::_('EB_SEP'),
			JText::_('EB_OCT'),
			JText::_('EB_NOV'),
			JText::_('EB_DEC'));
		$options     = array();
		foreach ($listMonth as $key => $monthName)
		{
			if ($key < 9)
			{
				$value = "0" . ($key + 1);
			}
			else
			{
				$value = $key + 1;
			}
			$options[] = JHtml::_('select.option', $value, $monthName);
		}
		$this->searchMonth = JHtml::_('select.genericlist', $options, 'month', 'class="input-medium" onchange="submit();" ', 'value', 'text', $month);
		$options           = array();
		for ($i = $year - 3; $i < ($year + 5); $i++)
		{
			$options[] = JHtml::_('select.option', $i, $i);
		}
		$this->searchYear      = JHtml::_('select.genericlist', $options, 'year', 'class="input-small" onchange="submit();" ', 'value', 'text', $year);
		$this->category        = $category;
		$this->config          = $config;
		$this->listMonth       = $listMonth;
		$this->bootstrapHelper = new EventbookingHelperBootstrap($config->twitter_bootstrap_version);

		parent::display();
	}
}