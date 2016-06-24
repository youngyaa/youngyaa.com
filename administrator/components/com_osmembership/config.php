<?php
/**
 * @version        3.8
 * @package        Joomla
 * @subpackage     Payment Form
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2010 - 2015 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die();
if (JFactory::getApplication()->isAdmin())
{
	$MPConfig = array(
		'default_controller_class' => 'OSMembershipController',
		'default_view'             => 'dashboard',
		'class_prefix'             => 'OSMembership',
		'language_prefix'          => 'OSM');
}
else
{
	$MPConfig = array(
		'default_controller_class' => 'OSMembershipController',
		'default_view'             => 'plans',
		'class_prefix'             => 'OSMembership',
		'language_prefix'          => 'OSM',
	);
}

