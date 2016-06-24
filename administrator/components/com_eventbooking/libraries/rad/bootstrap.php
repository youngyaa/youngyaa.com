<?php
/**
 * Register the prefix so that the classes in RAD library can be auto-load
 */
defined('_JEXEC') or die();

error_reporting(0);
define('EB_TBC_DATE', '2099-12-31 00:00:00');

JLoader::registerPrefix('RAD', dirname(__FILE__));
$app = JFactory::getApplication();
JLoader::registerPrefix('Eventbooking', JPATH_BASE . '/components/com_eventbooking');
if ($app->isAdmin())
{
	JLoader::register('EventbookingHelper', JPATH_ROOT . '/components/com_eventbooking/helper/helper.php');
	JLoader::register('EventbookingHelperIcs', JPATH_ROOT . '/components/com_eventbooking/helper/ics.php');
	JLoader::register('EventbookingHelperHtml', JPATH_ROOT . '/components/com_eventbooking/helper/html.php');	
	JLoader::register('EventbookingHelperCart', JPATH_ROOT . '/components/com_eventbooking/helper/cart.php');
	JLoader::register('EventbookingHelperRoute', JPATH_ROOT . '/components/com_eventbooking/helper/route.php');
	JLoader::register('EventbookingHelperJquery', JPATH_ROOT . '/components/com_eventbooking/helper/jquery.php');
	JLoader::register('EventbookingHelperData', JPATH_ROOT . '/components/com_eventbooking/helper/data.php');
	JLoader::register('EventbookingHelperDatabase', JPATH_ROOT . '/components/com_eventbooking/helper/database.php');
}
else
{
	JLoader::register('EventbookingModelRegistrants', JPATH_ADMINISTRATOR . '/components/com_eventbooking/model/registrants.php');
	JLoader::register('EventbookingModelEvents', JPATH_ADMINISTRATOR . '/components/com_eventbooking/model/events.php');
}
JLoader::register('os_payments', JPATH_ROOT . '/components/com_eventbooking/payments/os_payments.php');
JLoader::register('os_payment', JPATH_ROOT . '/components/com_eventbooking/payments/os_payment.php');
JLoader::register('JFile', JPATH_LIBRARIES . '/joomla/filesystem/file.php');




