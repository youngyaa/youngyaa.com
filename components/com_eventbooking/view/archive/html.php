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

class EventbookingViewArchiveHtml extends RADViewHtml
{

	public function display()
	{
		$app    = JFactory::getApplication();
		$active = $app->getMenu()->getActive();
		$model  = $this->getModel();
		$state  = $model->getState();
		$items  = $model->getData();
		$config = EventbookingHelper::getConfig();

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

		$category = null;
		if ($state->id)
		{
			$category = EventbookingHelperDatabase::getCategory($state->id);
		}

		if ($config->show_list_of_registrants)
		{
			EventbookingHelperJquery::colorbox('eb-colorbox-register-lists');
		}

		if ($config->show_location_in_category_view)
		{
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
		}

		// Process page meta data
		$params = EventbookingHelper::getViewParams($active, array('archive'));

		if (!$params->get('page_title'))
		{
			$params->set('page_title', JText::_('EB_EVENTS_ARCHIVE'));
		}

		EventbookingHelperHtml::prepareDocument($params, $category);

		$this->items           = $items;
		$this->pagination      = $model->getPagination();
		$this->config          = $config;
		$this->categoryId      = $state->id;
		$this->category        = $category;
		$this->nullDate        = JFactory::getDbo()->getNullDate();
		$this->bootstrapHelper = new EventbookingHelperBootstrap($config->twitter_bootstrap_version);

		parent::display();
	}
}