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

require_once JPATH_ADMINISTRATOR . '/components/com_eventbooking/libraries/rad/bootstrap.php';
EventbookingHelper::loadLanguage();
$config   = EventbookingHelper::getConfig();
$document = JFactory::getDocument();
$input    = JFactory::getApplication()->input;
$option   = $input->getCmd('option');
$rootUrl  = JUri::base(true);
if ($option != 'com_eventbooking')
{
	$document->addStyleSheet($rootUrl . "/media/com_eventbooking/assets/css/style.css");
	if ($config->load_jquery !== '0')
	{
		EventbookingHelper::loadJQuery();
	}
	JHtml::_('script', JUri::root() . '/media/com_eventbooking/assets/js/noconflict.js', false, false);

	if ($config->calendar_theme)
	{
		$theme = $config->calendar_theme;
	}
	else
	{
		$theme = 'default';
	}
	$document->addStylesheet($rootUrl . '/media/com_eventbooking/assets/css/themes/' . $theme . '.css');
}
$document->addScript($rootUrl . '/media/com_eventbooking/assets/js/minicalendar.js');
EventbookingHelper::addLangLinkForAjax();


$currentDateData = EventbookingModelCalendar::getCurrentDateData();
$year            = $currentDateData['year'];
$month           = (int) $params->get('default_month', 0);
if (!$month)
{
	$month = $currentDateData['month'];
}

// Get calendar data for the current month and year
$model = new EventbookingModelCalendar(array('remember_states' => false, 'ignore_request' => true));
$model->setState('month', $month)
	->setState('year', $year)
	->setState('mini_calendar', 1);
$rows = $model->getData();
$data = EventbookingHelperData::getCalendarData($rows, $year, $month, true);

$days     = array();
$startDay = (int) $config->calendar_start_date;
for ($i = 0; $i < 7; $i++)
{
	$days[$i] = EventbookingHelperData::getDayNameHtmlMini(($i + $startDay) % 7, true);
}

$listMonth = array(JText::_('EB_JAN'), JText::_('EB_FEB'), JText::_('EB_MARCH'),
	JText::_('EB_APR'), JText::_('EB_MAY'), JText::_('EB_JUNE'), JText::_('EB_JUL'),
	JText::_('EB_AUG'), JText::_('EB_SEP'), JText::_('EB_OCT'), JText::_('EB_NOV'),
	JText::_('EB_DEC'));

$itemId = (int) $params->get('item_id');
if (!$itemId)
{
	$itemId = EventbookingHelper::getItemid();
}

require(JModuleHelper::getLayoutPath('mod_eb_minicalendar', 'default'));
?>