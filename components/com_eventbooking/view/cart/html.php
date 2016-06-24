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

class EventbookingViewCartHtml extends RADViewHtml
{

	/**
	 * Display interface to user
	 */
	public function display()
	{
		$layout = $this->getLayout();
		if ($layout != 'mini')
		{
			$this->setLayout('default');
		}
		$config     = EventbookingHelper::getConfig();
		$categoryId = (int) JFactory::getSession()->get('last_category_id', 0);
		if (!$categoryId)
		{
			//Get category ID of the current event			
			$cart     = new EventbookingHelperCart();
			$eventIds = $cart->getItems();
			if (count($eventIds))
			{
				$db          = JFactory::getDbo();
				$query       = $db->getQuery(true);
				$lastEventId = $eventIds[count($eventIds) - 1];
				$query->select('category_id')
					->from('#__eb_event_categories')
					->where('event_id = ' . (int) $lastEventId);
				$db->setQuery($query);
				$categoryId = $db->loadResult();
			}
		}
		$items = $this->model->getData();

		//Generate javascript string
		$jsString = " var arrEventIds = new Array() \n; var arrQuantities = new Array();\n";
		for ($i = 0, $n = count($items); $i < $n; $i++)
		{
			$item = $items[$i];
			if ($item->event_capacity == 0)
			{
				$availableQuantity = -1;
			}
			else
			{
				$availableQuantity = $item->event_capacity - $item->total_registrants;
			}
			$jsString .= "arrEventIds[$i] = $item->id ;\n";
			$jsString .= "arrQuantities[$i] = $availableQuantity ;\n";
		}

		// Continue shopping url
		if ($categoryId)
		{
			$this->continueUrl = JRoute::_(EventbookingHelperRoute::getCategoryRoute($categoryId, $this->Itemid));
		}
		else
		{
			$this->continueUrl = JRoute::_('index.php?Itemid=' . EventbookingHelper::getItemid());
		}

		$this->items           = $items;
		$this->config          = $config;
		$this->categoryId      = $categoryId;
		$this->jsString        = $jsString;
		$this->bootstrapHelper = new EventbookingHelperBootstrap($config->twitter_bootstrap_version);

		parent::display();
	}
}