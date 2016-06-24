<?php

/**
 * @package 	mod_bt_media_categories - BT Media Categories Module
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


class modBtMediaCategoryHelper {

    // get twitter feed
    public static function showListCategories($params) {
        $sub = $params->get('show_sub', 1);
        return self::makeHTML($params->get('catid', 0), $sub, $params);
    }

    public static function makeHTML($categoryId, $sub, $params) {
        $active = JRequest::getVar('option') == 'com_bt_media'
                    && JRequest::getVar('view') == 'category';

        $html = array();
        $arr = self::getAllSubCat($categoryId, $params);
        if ($sub == "1") {
            $html[] = '<ul class="root menu nav">';
            foreach ($arr as $cat) {
                $html[] = self::createList($cat->id, $params);
            }
            $html[] = '</ul>';
        } else {
            $html[] = '<ul class="root menu nav">';
            foreach ($arr as $cat) {
                $html[] = '<li class="item ' .(($active && JRequest::getInt('catid') == $cat->id) ? 'active' : '') . '"><a href="' . JRoute::_('index.php?option=com_bt_media&view=category&catid=' . $cat->id . ':'.$cat->alias).'" >' .(($params->get('show_item_count', 0) == 1) ? $cat->name . ' (' . $cat->item_count . ')' : $cat->name). '</a></li>';
            }
            $html[] = '</ul>';
        }
        return implode($html);
    }

    private static function createList($id, $params) {
        $active = JRequest::getVar('option') == 'com_bt_media'
            && JRequest::getVar('view') == 'category';
        $html = array();
        $category = self::getCatDetail($id);
        if ($category) {
            $sub_cat = self::getAllSubCat($id, $params);
            if (count($sub_cat) > 0) {
                $html[] = '<li class="item ' .(($active && JRequest::getInt('catid') == $category->id) ? 'active' : '') . '"><a href="' . JRoute::_('index.php?option=com_bt_media&view=category&catid=' . $category->id.':'.$category->alias) . '" >' . (($params->get('show_item_count', 0) == 1) ? $category->name . ' (' . $category->item_count . ')' : $category->name) . '</a>';
                $html[] = '<ul>';
                foreach ($sub_cat as $cat) {
                    $html[] = self::createList($cat->id, $params);
                }
                $html[] = '</ul>';
                $html[] = '</li>';
            } else {
                $html[] = '<li class="item ' .(($active && JRequest::getInt('catid') == $category->id) ? 'active' : '') . '"><a href="' . JRoute::_('index.php?option=com_bt_media&view=category&catid=' . $category->id.':'.$category->alias) . '" >' . (($params->get('show_item_count', 0) == 1) ? $category->name . ' (' . $category->item_count . ')' : $category->name) . '</a></li>';
            }
        }
        return implode($html);
    }

    private static function getAllSubCat($id, $params) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(TRUE);
        $query->select('c.*, (SELECT COUNT(id) FROM #__bt_media_items WHERE cate_id= c.id) AS item_count');
        $query->from('#__bt_media_categories as c');
        $query->where('parent_id=' . $id);
		$query->where('state=1');
        $query->order($params->get('order', 'id ASC'));
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    private static function getCatDetail($id) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(TRUE);
        $query->select('*, (SELECT COUNT(id) FROM #__bt_media_items WHERE cate_id = '.$id.')  AS item_count');
        $query->from('#__bt_media_categories');
        $query->where('id=' . $id);
        $query->where('state=1');
        $db->setQuery($query);
        return $db->loadObject();
    }

}