<?php
/**
 * @version        2.0.0
 * @package        Joomla
 * @subpackage     Event Booking
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2010 - 2015 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die;

error_reporting(0);
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root().'modules/mod_eb_googlemap/asset/style.css');
$document->addStyleSheet(JUri::root().'media/com_eventbooking/assets/css/style.css');
require_once JPATH_ADMINISTRATOR . '/components/com_eventbooking/libraries/rad/bootstrap.php';
require_once dirname(__FILE__).'/helper.php';
$config = EventbookingHelper::getConfig();
if ($config->load_jquery !== '0')
{
	EventbookingHelper::loadJQuery();
}
if ($config->load_bootstrap_css_in_frontend !== '0')
{
	EventbookingHelper::loadBootstrap();
}
JHtml::_('script', EventbookingHelper::getURL() . 'media/com_eventbooking/assets/js/noconflict.js', false, false);

// params
$width = $params->get('width', 100);
$height = $params->get('height', 400);
$ebMap = new modEventBookingGoogleMapHelper($module,$params);

require( JModuleHelper::getLayoutPath( 'mod_eb_googlemap' ) );
?>
