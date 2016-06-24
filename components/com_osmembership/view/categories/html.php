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
defined('_JEXEC') or die;

/**
 * HTML View class for Membership Pro component
 *
 * @package        Joomla
 * @subpackage     Membership Pro
 */
class OSMembershipViewCategoriesHtml extends MPFViewHtml
{
	public function display()
	{
		$app   = JFactory::getApplication();
		$model = $this->getModel();
		$items = $model->getData();

		$categoryId = (int) $model->getState()->get('id', 0);


		// If category id is passed, make sure it is valid and the user is allowed to access
		if ($categoryId)
		{
			$category = OSMembershipHelperDatabase::getCategory($categoryId);
			if (empty($category) || !in_array($category->access, JFactory::getUser()->getAuthorisedViewLevels()))
			{
				$app->redirect('index.php', JText::_('OSM_INVALID_CATEGORY_OR_NOT_AUTHORIZED'));
			}

			$this->category = $category;
		}

		//Process content plugin in the description
		for ($i = 0, $n = count($items); $i < $n; $i++)
		{
			$item              = $items[$i];
			$item->description = JHtml::_('content.prepare', $item->description);
		}

		// Process page title and meta data
		$active = $app->getMenu()->getActive();
		$params = OSMembershipHelper::getViewParams($active, array('categories'));
		if ($active)
		{
			$document  = JFactory::getDocument();
			$appConfig = JFactory::getConfig();
			if ($params->get('page_title'))
			{

				$pageTitle = $params->get('page_title');
			}
			else
			{
				$pageTitle = $active->title;
			}
			$siteNamePosition = $appConfig->get('sitename_pagetitles');
			if ($siteNamePosition == 0)
			{
				$document->setTitle($pageTitle);
			}
			elseif ($siteNamePosition == 1)
			{
				$document->setTitle($appConfig->get('sitename') . ' - ' . $pageTitle);
			}
			else
			{
				$document->setTitle($pageTitle . ' - ' . $appConfig->get('sitename'));
			}

			if ($params->get('menu-meta_keywords'))
			{
				$document->setMetadata('keywords', $params->get('menu-meta_keywords'));
			}

			if ($params->get('menu-meta_description'))
			{
				$document->setDescription($params->get('menu-meta_description'));
			}

			if ($params->get('robots'))
			{
				$document->setMetadata('robots', $params->get('robots'));
			}
		}

		$this->categoryId = $categoryId;
		$this->config     = OSMembershipHelper::getConfig();
		$this->items      = $items;
		$this->pagination = $model->getPagination();
		$this->params     = $params;

		parent::display();
	}
}