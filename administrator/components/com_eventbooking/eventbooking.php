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

//Basic ACL support
if (!JFactory::getUser()->authorise('core.manage', 'com_eventbooking'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

require_once JPATH_ADMINISTRATOR . '/components/com_eventbooking/libraries/rad/bootstrap.php';

if (JLanguageMultilang::isEnabled() && !EventbookingHelper::isSynchronized())
{
	EventbookingHelper::setupMultilingual();
}

if (isset($_POST['language']))
{
	$_REQUEST['language'] = $_POST['language'];
}

$config = EventbookingHelper::getComponentSettings('admin');
$input  = new RADInput();
RADController::getInstance($input->getCmd('option'), $input, $config)
	->execute()
	->redirect();
