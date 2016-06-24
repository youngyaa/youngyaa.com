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

class EventbookingViewLocationHtml extends RADViewHtml
{

	/**
	 * Display events from a location
	 */
	public function display()
	{
		$layout = $this->getLayout();
		if ($layout == 'form')
		{
			$this->displayForm();

			return;
		}

		$app      = JFactory::getApplication();
		$active   = $app->getMenu()->getActive();
		$model    = $this->getModel();
		$items    = $model->getData();
		$location = EventbookingHelperDatabase::getLocation($this->input->getInt('location_id'));
		$config   = EventbookingHelper::getConfig();
		if ($config->process_plugin)
		{
			for ($i = 0, $n = count($items); $i < $n; $i++)
			{
				$item                    = $items[$i];
				$item->short_description = JHtml::_('content.prepare', $item->short_description);
			}
		}

		if ($config->event_custom_field && $config->show_event_custom_field_in_category_layout)
		{
			EventbookingHelperData::prepareCustomFieldsData($items);
		}

		// Process page meta data
		$params = EventbookingHelper::getViewParams($active, array('location'));
		if (!$params->get('page_title'))
		{
			if (!empty($location->name))
			{
				$params->set('page_title', $location->name);
			}
		}
		EventbookingHelperHtml::prepareDocument($params, $location);

		$user                  = JFactory::getUser();
		$this->items           = $items;
		$this->config          = $config;
		$this->location        = $location;
		$this->pagination      = $model->getPagination();
		$this->nullDate        = JFactory::getDbo()->getNullDate();
		$this->viewLevels      = $user->getAuthorisedViewLevels();
		$this->userId          = $user->get('id');
		$this->bootstrapHelper = new EventbookingHelperBootstrap($config->twitter_bootstrap_version);

		parent::display();
	}

	/**
	 * Display Form to allow adding location for event
	 *
	 * @throws Exception
	 */
	protected function displayForm()
	{

		if (!JFactory::getUser()->authorise('eventbooking.addlocation', 'com_eventbooking'))
		{
			JFactory::getApplication()->redirect('index.php', JText::_('EB_NO_PERMISSION'));

			return;
		}

		$config             = EventbookingHelper::getConfig();
		$item               = $this->model->getLocationData();
		$options            = array();
		$options[]          = JHtml::_('select.option', '', JText::_('Select Country'), 'id', 'name');
		$options            = array_merge($options, EventbookingHelperDatabase::getAllCountries());
		$lists['country']   = JHtml::_('select.genericlist', $options, 'country', ' class="inputbox" ', 'id', 'name', $item->country);
		$lists['published'] = JHtml::_('select.booleanlist', 'published', '', $item->published);
		$this->item         = $item;
		$this->lists        = $lists;
		$this->config       = $config;

		$this->bootstrapHelper = new EventbookingHelperBootstrap($config->twitter_bootstrap_version);

		parent::display();
	}
}
