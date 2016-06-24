<?php

/**
 * @package 	mod_bt_media_items_gallery - BT Media Items Gallery Module
 * @version		1.0
 * @created		Feb 2012
 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
$com_path = '/components/com_bt_media/';
jimport('joomla.application.component.model');
JLoader::register('Bt_mediaHelper', JPATH_SITE. $com_path.'/helpers/bt_media.php');
JModelLegacy::addIncludePath(JPATH_SITE . $com_path . '/models', 'Bt_mediaModel');
JTable::addIncludePath(JPATH_ADMINISTRATOR . $com_path . '/tables', 'Bt_mediaTable');

class modBtMediaListTagsHelper {

    // get twitter feed
    public static function getItems(&$params) {
        // Get an instance of the generic articles model
        $model = JModelLegacy::getInstance('Tags', 'Bt_mediaModel', array('ignore_request' => true));

        if ($model) {
            // Set the filters based on the module params
            $model->setState('list.start', 0);
            $model->setState('list.limit', (int) $params->get('numbertags', 20));
            $model->setState('list.ordering', $params->get('item_sort'));
            $items = $model->getItems();
            return $items;
        }else{
            JError::raiseError('100', MOD_BT_MEDIA_LIST_TAGS_COM_NOT_INSTALL);
            return FALSE;
        }
    }
}