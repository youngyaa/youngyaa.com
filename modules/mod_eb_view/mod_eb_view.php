<?php
/**
 * @version        2.0.3
 * @package        Joomla
 * @subpackage     Events Booking
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2010 - 2015 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

error_reporting(0);

if (file_exists(JPATH_ROOT . '/components/com_eventbooking/eventbooking.php'))
{	
	require_once JPATH_ADMINISTRATOR . '/components/com_eventbooking/libraries/rad/bootstrap.php';	
	$view = $params->get('view', 'categories');
	$queryString = $params->get('query_string', '');
	EventbookingHelper::loadLanguage();
	$request = array('option' => 'com_eventbooking', 'view' => $view);
	if ($queryString)
	{
		parse_str($queryString, $vars);
		$request = array_merge($request, $vars);
	}
		
	if (!isset($request['Itemid']))
	{
		$request['Itemid'] = EventbookingHelper::getItemid();	
	}
	$input   = new RADInput($request);
	$config  = EventbookingHelper::getComponentSettings('site');
	RADController::getInstance('com_eventbooking', $input, $config)
			->execute();
}