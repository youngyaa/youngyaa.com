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

//Require the controller

if (!JFactory::getUser()->authorise('core.manage', 'com_osmembership'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

include JPATH_ADMINISTRATOR . '/components/com_osmembership/config.php';
require_once JPATH_ADMINISTRATOR . '/components/com_osmembership/loader.php';

// Setup database to work with multilingual site if needed
if (JLanguageMultilang::isEnabled() && !OSMembershipHelper::isSyncronized())
{
	OSMembershipHelper::setupMultilingual();
}

$input = new MPFInput();
MPFController::getInstance($input->getCmd('option'), $input, $MPConfig)
	->execute()
	->redirect();