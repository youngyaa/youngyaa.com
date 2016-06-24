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

class EventbookingViewCategoriesHtml extends RADViewHtml
{

	public function display()
	{
		$app    = JFactory::getApplication();
		$active = $app->getMenu()->getActive();
		$params = EventbookingHelper::getViewParams($active, array('categories'));

		$config     = EventbookingHelper::getConfig();
		$model      = $this->getModel();
		$items      = $model->getData();
		$pagination = $model->getPagination();
		$categoryId = (int) $model->getState('id');

		// If category id is passed, make sure it is valid and the user is allowed to access
		if ($categoryId)
		{
			$category = EventbookingHelperDatabase::getCategory($categoryId);
			if (empty($category) || !in_array($category->access, JFactory::getUser()->getAuthorisedViewLevels()))
			{
				$app->redirect('index.php', JText::_('EB_INVALID_CATEGORY_OR_NOT_AUTHORIZED'));
			}

			$this->category = $category;
		}

		// Build page title if it has not been set from menu title
		if (!$params->get('page_title'))
		{
			if (!empty($category))
			{
				$pageTitle = JText::_('EB_SUB_CATEGORIES_PAGE_TITLE');
				$pageTitle = str_replace('[CATEGORY_NAME]', $category->name, $pageTitle);
			}
			else
			{
				$pageTitle = JText::_('EB_CATEGORIES_PAGE_TITLE');
			}

			$params->set('page_title', $pageTitle);
		}

		EventbookingHelperHtml::prepareDocument($params, isset($category) ? $category : null);

		// Process content plugin  for categories
		if ($config->process_plugin)
		{
			for ($i = 0, $n = count($items); $i < $n; $i++)
			{
				$item              = $items[$i];
				$item->description = JHtml::_('content.prepare', $item->description);
			}
			if (!empty($category))
			{
				$category->description = JHtml::_('content.prepare', $category->description);
			}
		}

		$this->categoryId = $categoryId;
		$this->config     = $config;
		$this->items      = $items;
		$this->pagination = $pagination;
		$this->params     = $params;

		parent::display();
	}
}