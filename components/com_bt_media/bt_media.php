<?php

/**
 * @package     com_bt_media - BT Media
 * @version	1.0.0
 * @created	Oct 2012
 * @author	BowThemes
 * @email	support@bowthems.com
 * @website	http://bowthemes.com
 * @support	Forum - http://bowthemes.com/forum/
 * @copyright   Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');
JLoader::register('Bt_mediaHelper', JPATH_COMPONENT . '/helpers/bt_media.php');
JLoader::register('Bt_mediaController', JPATH_COMPONENT . '/controller.php');
JLoader::register('Bt_mediaLegacyHelper', JPATH_ADMINISTRATOR . '/components/com_bt_media/helpers/legacy.php');
if (JFactory::getApplication()->input->get("format") != 'raw') {
    $params = JFactory::getApplication()->getParams('com_bt_media');
    Bt_mediaHelper::addSiteScript($params);
}

// Execute the task.
$controller = JControllerLegacy::getInstance('Bt_media');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
