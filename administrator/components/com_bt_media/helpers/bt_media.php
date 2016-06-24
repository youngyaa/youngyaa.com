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
// No direct access
defined('_JEXEC') or die;
/**
 * Bt_media helper.
 */
class Bt_mediaHelper {

    /**
     * Configure the Linkbar.
     */
    public static function addSubmenu($vName = '') {
        if (class_exists('JHtmlSidebar')) {
            JHtmlSidebar::addEntry(
                    JText::_('COM_BT_MEDIA_MENU_CPANEL_TITLE'), 'index.php?option=com_bt_media&view=controlpanel', $vName == 'controlpanel'
            );
            JHtmlSidebar::addEntry(
                    JText::_('COM_BT_MEDIA_MENU_CATEGORYS_TITLE'), 'index.php?option=com_bt_media&view=categories', $vName == 'categories'
            );

            JHtmlSidebar::addEntry(
                    JText::_('COM_BT_MEDIA_MENU_MEDIASMANAGEMENT_TITLE'), 'index.php?option=com_bt_media&view=list', $vName == 'list'
            );

            JHtmlSidebar::addEntry(
                    JText::_('COM_BT_MEDIA_MENU_TAG_TITLE'), 'index.php?option=com_bt_media&view=tags', $vName == 'tags'
            );
        } else {
            JSubMenuHelper::addEntry(
                    JText::_('COM_BT_MEDIA_MENU_CPANEL_TITLE'), 'index.php?option=com_bt_media&view=controlpanel', $vName == 'controlpanel'
            );

            JSubMenuHelper::addEntry(
                    JText::_('COM_BT_MEDIA_MENU_CATEGORYS_TITLE'), 'index.php?option=com_bt_media&view=categories', $vName == 'categories'
            );

            JSubMenuHelper::addEntry(
                    JText::_('COM_BT_MEDIA_MENU_MEDIASMANAGEMENT_TITLE'), 'index.php?option=com_bt_media&view=list', $vName == 'list'
            );

            JSubMenuHelper::addEntry(
                    JText::_('COM_BT_MEDIA_MENU_TAG_TITLE'), 'index.php?option=com_bt_media&view=tags', $vName == 'tags'
            );
        }
    }

    /**
     * Gets a list of the actions that can be performed.
     *
     * @return	JObject
     * @since	1.6
     */
    public static function getActions() {
        $user = JFactory::getUser();
        $result = new JObject;

        $assetName = 'com_bt_media';

        $actions = array(
            'core.admin',
            'core.manage',
            'core.create',
            'core.edit',
            'core.edit.state',
            'core.edit.own',
            'core.delete',
            'media.delete.own',
            'media.upload.image',
            'media.get.image',
            'media.upload.video',
            'media.get.video',
            'media.multi.upload'
        );

        foreach ($actions as $action) {
            $result->set($action, $user->authorise($action, $assetName));
        }

        return $result;
    }
}
