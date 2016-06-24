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
JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_bt_media/tables');

/**
 * @param	array	A named array
 * @return	array
 */
function Bt_mediaBuildRoute(&$query) {
    $segments = array();
    if (isset($query['tags']) && $query['tags']) {
        unset($query['tags']);
        unset($query['view']);
        return $segments;
    }
    if (!isset($query['Itemid'])) {
        $itemID = BTMediaFindItemID();
        if ($itemID) {
            $query['Itemid'] = $itemID;
        }
    }
    if (isset($query['view'])) {
        if (isset($query['id'])) {
            $arr = explode(':', $query['id'], 2);
            $id = $arr[0];
            if ($query['view'] == 'detail') {
                if (isset($query['layout']) && $query['layout'] == 'edit') {
                    $segments[] = $query['layout'];
                    unset($query['layout']);
                }
                if (isset($query['cat_rel'])) {
                    $table = JTable::getInstance('Category', 'Bt_mediaTable');
                    $table->load($query['cat_rel']);
                    $segments[] = $query['cat_rel'] . ':' . $table->alias;
                    unset($query['cat_rel']);
                }
                if (isset($query['id'])) {
                    if (count($arr) == 2) {
                        $segments[] = $query['id'];
                    } else {
                        $table = JTable::getInstance('Detail', 'Bt_mediaTable');
                        $table->load($id);
                        $segments[] = $arr[0] . ':' . $table->alias;
                    }
                }
            }
            if ($query['view'] == 'tag') {
                $segments[] = 'tag';
                if (count($arr) == 2) {
                    $segments[] = $query['id'];
                } else {
                    $table = JTable::getInstance('Tag', 'Bt_mediaTable');
                    $table->load($id);
                    $segments[] = $arr[0] . ':' . $table->alias;
                }
            }
            unset($query['id']);
        } else {
            if ($query['view'] == 'detail') {
                if (isset($query['layout']) && $query['layout'] == 'edit') {
                    $segments[] = $query['layout'];
                    unset($query['layout']);
                }
            }
        }
        if (isset($query['catid'])) {
            $arr = explode(':', $query['catid'], 2);
            $id = $arr[0];
            if ($query['view'] == 'category') {
                if (count($arr) == 2) {
                    $segments[] = $query['catid'];
                } else {
                    $table = JTable::getInstance('Category', 'Bt_mediaTable');
                    $table->load($id);
                    $segments[] = $arr[0] . ':' . $table->alias;
                }
            }
            unset($query['catid']);
        }
        unset($query['view']);
    }
    return $segments;
}

/**
 * @param	array	A named array
 * @param	array
 *
 * Formats:
 *
 * index.php?/media_category/task/id/Itemid
 *
 * index.php?/media_category/id/Itemid
 */
function Bt_mediaParseRoute($segments) {
    $vars = array();

    // view is always the first element of the array
    $count = count($segments);
    foreach ($segments as & $segment) {
        $segment = str_replace(':', '-', $segment);
        $segment = str_replace('_', '.', $segment);
    }

    switch ($count) {
        case 1:
            $vars['view'] = 'category';
            $vars['catid'] = substr($segments[0], 0, strpos($segments[0], '-'));
            break;
        case 2:
            if ($segments[0] == 'tag') {
                $vars['view'] = 'tag';
            } else {
                $vars['view'] = 'detail';
                if ($segments[0] == 'edit') {
                    $vars['layout'] = 'edit';
                }
                $vars['cat_rel'] = substr($segments[0], 0, strpos($segments[0], '-'));
            }
            $vars['id'] = substr($segments[1], 0, strpos($segments[1], '-'));
            break;
        case 3:
            if ($segments[0] == 'tag') {
                $vars['view'] = $segments[0];
                $table = JTable::getInstance('Tag', 'Bt_mediaTable');
                $table->load(array('alias' => $segments[1]));
                $vars['id'] = $table->id;
            }
            break;
    }


    return $vars;
}

if (!function_exists('BTMediaFindItemID')) {

    function BTMediaFindItemID() {
        $lang = JFactory::getLanguage()->getTag();
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $groups = implode(',', $user->getAuthorisedViewLevels());
        $query = "select id from #__menu where type='component' and link like '%index.php?option=com_bt_media%' and published = 1 and language = '".$lang."' and access in(" . $groups . ") order by lft limit 1";
        $db->setQuery($query);
        return $db->loadResult();
    }

}
