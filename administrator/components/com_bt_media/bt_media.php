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


// no direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_bt_media')) 
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');
JLoader::register('Bt_mediaHelper', JPATH_ADMINISTRATOR . '/components/com_bt_media/helpers/bt_media.php');
JLoader::register('Bt_mediaLegacyHelper', JPATH_ADMINISTRATOR . '/components/com_bt_media/helpers/legacy.php');
JLoader::register('BTView', JPATH_COMPONENT.'/views/view.php');
if(!define('COM_BT_MEDIA_VERSION', '1.3'))  {
    define('COM_BT_MEDIA_VERSION', '1.3');
}
$controller	= JControllerLegacy::getInstance('Bt_media');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
