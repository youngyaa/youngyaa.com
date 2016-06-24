<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

if (file_exists(JPATH_ROOT . '/components/com_osmembership/osmembership.php'))
{
	$view = $params->get('view', 'plans');
	$queryString = $params->get('query_string', '');
	require_once JPATH_ADMINISTRATOR . '/components/com_osmembership/loader.php';
	OSMembershipHelper::loadLanguage();
	
	$config = OSMembershipHelper::getConfig();
	if (empty($config->debug))
	{
		error_reporting(0);
	}
	else
	{
		error_reporting(E_ALL);
	}
	
	$request = array('option' => 'com_osmembership', 'view' => $view);
	if ($queryString)
	{
		parse_str($queryString, $vars);
		$request = array_merge($request, $vars);
	}
	$input   = new MPFInput($request);
	$config  = array(
		'default_controller_class' => 'OSMembershipController',
		'default_view'             => 'plans',
		'class_prefix'             => 'OSMembership',
		'language_prefix'          => 'OSM',
		'remember_states'			=> false,
		'ignore_request'			=> false
	);

	//Initialize the controller, execute the task (display) to display the view
	MPFController::getInstance('com_osmembership', $input, $config)
		->execute();
}