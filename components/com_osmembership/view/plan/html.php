<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * HTML View class for OS Membership component
 *
 * @static
 * @package        Joomla
 * @subpackage     Membership Pro
 */
class OSMembershipViewPlanHtml extends MPFViewHtml
{

	public function display()
	{
		$app  = JFactory::getApplication();
		$item = $this->getModel()->getData();
		if (!$item->id)
		{
			$app->redirect('index.php', JText::_('OSM_INVALID_SUBSCRIPTION_PLAN'));
		}
		if (!in_array($item->access, JFactory::getUser()->getAuthorisedViewLevels()))
		{
			$app->redirect('index.php', JText::_('OSM_NOT_ALLOWED_PLAN'));
		}
		$taxRate = OSMembershipHelper::calculateTaxRate($item->id);
		$config  = OSMembershipHelper::getConfig();
		if ($config->show_price_including_tax && $taxRate > 0)
		{
			$item->price        = $item->price * (1 + $taxRate / 100);
			$item->trial_amount = $item->trial_amount * (1 + $taxRate / 100);
		}
		$item->short_description = JHtml::_('content.prepare', $item->short_description);
		$item->description       = JHtml::_('content.prepare', $item->description);

		// Process page title and meta data
		$active = $app->getMenu()->getActive();
		$params = OSMembershipHelper::getViewParams($active, array('plan'));
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

		$this->item            = $item;
		$this->config          = $config;
		$this->bootstrapHelper = new OSMembershipHelperBootstrap($config->twitter_bootstrap_version);
		$this->params          = $params;
		$this->setLayout('default');

		parent::display();
	}
}