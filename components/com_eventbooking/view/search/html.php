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

class EventbookingViewSearchHtml extends RADViewHtml
{

	public function display()
	{
		$document   = JFactory::getDocument();
		$model      = $this->getModel();
		$items      = $model->getData();
		$pagination = $model->getPagination();
		$document->setTitle(JText::_('EB_SEARCH_RESULT'));
		$config = EventbookingHelper::getConfig();
		if ($config->process_plugin)
		{
			for ($i = 0, $n = count($items); $i < $n; $i++)
			{
				$item                    = $items[$i];
				$item->short_description = JHtml::_('content.prepare', $item->short_description);;
			}
		}
		if ($config->multiple_booking)
		{
			EventbookingHelperJquery::colorbox('eb-colorbox-addcart', '800px', '450px', 'false', 'false');
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

		if ($config->event_custom_field && $config->show_event_custom_field_in_category_layout)
		{
			EventbookingHelperData::prepareCustomFieldsData($items);
		}
		$this->viewLevels      = JFactory::getUser()->getAuthorisedViewLevels();
		$this->items           = $items;
		$this->pagination      = $pagination;
		$this->config          = $config;
		$this->nullDate        = JFactory::getDbo()->getNullDate();
		$this->bootstrapHelper = new EventbookingHelperBootstrap($config->twitter_bootstrap_version);

		parent::display();
	}
}
