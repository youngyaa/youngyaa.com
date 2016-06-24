<?php
/**
 * @package 	mod_bt_media_gallery - BT Media Gallery Module
 * @version		1.0.0
 * @created		Aug 2013
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

class modBtMediaItemsGalleryHelper {

    // get twitter feed
    public static function getItems(&$params) {
        // Get an instance of the generic articles model
        $model = JModelLegacy::getInstance('List', 'Bt_mediaModel', array('ignore_request' => true));

        $comParams = JComponentHelper::getParams('com_bt_media');
        // Set the filters based on the module params
        $model->setState('params', $comParams);
        $model->setState('filter.categories', $params->get('catid'));
        $model->setState('list.start', 0);
        $model->setState('list.limit', (int) $params->get('show_limit_items', 10));
        JFactory::getApplication()->input->set('filter_ordering', $params->get('item_sort'));
        JFactory::getApplication()->input->set('filter_type', $params->get('item_type'));
        JFactory::getApplication()->input->set('filter_featured', $params->get('item_featured'));
        JFactory::getApplication()->input->set('filter_search', NULL);
        $model->setState('list.direction', 'DESC');

        $items = $model->getItems();
        return $items;
    }

}