<?php

/* @package 	xmap_com_bt_portfolio - BT Portfolio Xmap Plugin
 * @version		1.0.0
 * @created		Dec 2012
 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');
JLoader::register('Bt_mediaLegacyHelper', JPATH_ADMINISTRATOR.'/components/com_bt_media/helpers/legacy.php');

class xmap_com_bt_media {

    function prepareMenuItem($node, &$params) {
        $link_query = parse_url($node->link);
        if (!isset($link_query['query'])) {
            return;
        }
        
        parse_str(html_entity_decode($link_query['query']), $link_vars);

        $view = JArrayHelper::getValue($link_vars, 'view', '');
        $params['add_images'] = JArrayHelper::getValue($params, 'add_images', 0);
        $id = JArrayHelper::getValue($link_vars, 'id', 0);
        
        switch ($view) {
            case'category':
                if ($id) {
                    $node->uid = 'com_bt_mediac' . $id;
                } else {
                    $node->uid = 'com_bt_mediac';
                }
                $node->expandible = true;
                break;
            case 'tag':
                $node->uid = 'com_bt_mediat';
                $node->expandible = true;
                break;
            case'list':
                $node->uid = 'com_bt_mediam';
                $node->expandible = true;
                break;
            case'detail':
                $node->uid = 'com_bt_medias'. $id;
                $node->expandible = false;
                break;
        }
    }

    static function getTree($xmap, $parent, &$params) {
        $db = JFactory::getDBO();
        $app = JFactory::getApplication();
        $result = null;
        $link_query = parse_url($parent->link);
        if (!isset($link_query['query'])) {
            return;
        }
        parse_str(html_entity_decode($link_query['query']), $link_vars);
        $view = JArrayHelper::getValue($link_vars, 'view', '');
        $id = JArrayHelper::getValue($link_vars, 'id', 0);
        $tags = JArrayHelper::getValue($link_vars, 'tags', '');
        $categories = JArrayHelper::getValue($link_vars, 'categories', '');
        $expand_categories = JArrayHelper::getValue($params, 'expand_categories', 1);
        $expand_categories = ( $expand_categories == 1 || ( $expand_categories == 2 && $xmap->view == 'xml') || ( $expand_categories == 3 && $xmap->view == 'html') || $xmap->view == 'navigator');
        $params['expand_categories'] = $expand_categories;
        //Max Item
        $params['max_item'] = intval(JArrayHelper::getValue($params, 'max_item', 0));
        //add image
        $add_images = JArrayHelper::getValue($params, 'add_images', 0);
        $params['add_images'] = $add_images;


        //get Category priority and Category Change frequency
        $priority = JArrayHelper::getValue($params, 'cat_priority', $parent->priority);
        $changefreq = JArrayHelper::getValue($params, 'cat_changefreq', $parent->changefreq);
        if ($priority == '-1')
            $priority = $parent->priority;
        if ($changefreq == '-1')
            $changefreq = $parent->changefreq;
        $params['cat_priority'] = $priority;
        $params['cat_changefreq'] = $changefreq;
        // Item Priority and Item Change frequency
        $priority = JArrayHelper::getValue($params, 'item_priority', $parent->priority);
        if ($priority == '-1')
            $priority = $parent->priority;
        $changefreq = JArrayHelper::getValue($params, 'item_changefreq', $parent->changefreq);
        if ($changefreq == '-1')
            $changefreq = $parent->changefreq;
        $params['item_priority'] = $priority;
        $params['item_changefreq'] = $changefreq;
        switch ($view) {
            case 'category':
                if ($params['expand_categories']) {
                    $result = self::expandMediaCategory($xmap, $parent, ($id ? $id : 0), $params, $parent->id);
                }
                break;
            case'tag':
                $result = self::mediaitemTag($xmap, $parent, ($tags ? $tags : ''), $params, $parent->id);
                break;
            case'list':
                $result = self::mediaitemMngt($xmap, $parent, ($categories ? $categories : ''), $params, $parent->id);
                break;
            case'detail':
                break;
            default:
                break;
        }
        
        return $result;
    }

    static function expandMediaCategory($xmap, $parent, $catid, &$params, $itemid) {
        $db = JFactory::getDBO();
        $query = 'SELECT a.id, a.name, a.alias, a.access '
                . 'FROM #__bt_media_categories AS a  where a.parent_id = ' . $catid . ' AND a.state = 1';
        $db->setQuery($query);
        $items = $db->loadObjectList();
        if (count($items) > 0) {
            $xmap->changeLevel(1);
            foreach ($items as $item) {
                $node = new stdclass();
                $node->id = $parent->id;
                $node->uid = $parent->uid . $item->id;
                $node->browserNav = $parent->browserNav;
                $node->priority = $params['cat_priority'];
                $node->changefreq = $params['cat_changefreq'];
                $node->name = $item->name;
                $node->expandible = true;
                $node->secure = $parent->secure;
                $node->newsItem = 0;
                $node->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
                $node->link = JFactory::getURI()->toString(array('scheme', 'host', 'port')) . JRoute::_('index.php?option=com_bt_media&catid=' . $node->slug . '&view=category');
                if ($xmap->printNode($node)) {
                    self::expandMediaCategory($xmap, $parent, $item->id, $params, $node->link);
                    self::limititem($xmap, $parent, $item->id, $params, $itemid);
                }
            }
            $xmap->changeLevel(-1);
        }
        $query2 = 'SELECT a.id '
                . 'FROM #__bt_media_items AS a  where a.cate_id = ' . $catid . ' AND a.state = 1';
        $db->setQuery($query2);
        $items2 = $db->loadObjectList();
        if (count($items2) > 0) {
            $xmap->changeLevel(1);
            foreach ($items2 as $item) {
                $xmap->printNode(self::mediaitem($item->id, $params, $parent));
            }
            $xmap->changeLevel(-1);
        }

        return true;
    }
    

    static function mediaitemMngt($xmap, $parent, $categories, &$params, $itemid) {
        $db = JFactory::getDBO();
        $query = $db->getQuery(TRUE);
        $query->select('a.*');
        $query->from('`#__bt_media_items` AS a');
        $query->select('c.id as category_id');
        $query->join('INNER', '#__bt_media_categories AS c ON a.cate_id = c.id');
        $query->where('c.state=1');
        $query->where('a.state=1');
        
        $showSubItems = $parent->params->get('show_sub_media', 0);
        if($categories && $categories[0] != ''){
            if (isset($showSubItems) && $showSubItems != "") {
                if ($showSubItems == 1) {
                    $listCatId = self::getAllChildCategory($categories, $categories);
                    if ($listCatId && count($listCatId) > 0) {
                        $cats = implode(',', $listCatId);
                        $query->where("a.cate_id IN(" . $cats . ")");
                    } else {
                        $query->where("a.cate_id = '" . $categories[0] . "'");
                    }
                }
                if ($showSubItems == 0) {
                    $query->where("a.cate_id IN(" . implode(', ', $categories) . ")");
                }
            }
        }
        
        $db->setQuery($query);
        $items = $db->loadObjectList();
        if (count($items) > 0) {
            $xmap->changeLevel(1);
            foreach ($items as $item) {
                $xmap->printNode(self::mediaitem($item->id, $params, $parent));
            }
            $xmap->changeLevel(-1);
        }
        return true;
    }
    
    static function mediaitemTag($xmap, $parent, $tags, &$params, $itemid) {
        $db = JFactory::getDBO();
        $query = $db->getQuery(TRUE);
        $query->select('a.*');
        $query->from('#__bt_media_tags AS a');
        $query->select('ai.*');
        $query->join('INNER', '#__bt_media_tags_xref as ai ON ai.tag_id = a.id');

        $query->select('i.id as media_id');
        $query->join('INNER', '#__bt_media_items as i ON ai.item_id = i.id');
        $query->where('i.state=1');
        $query->where('a.state=1');
        $list_tag = explode(',', $tags);
        if (count($list_tag) > 0) {
            $tagnames = array();
            foreach ($list_tag as $name) {
                if (trim($name) != '') {
                    if (Bt_mediaLegacyHelper::getTagByName(trim($name)) != NULL) {
                        $tagnames[] = Bt_mediaLegacyHelper::getTagByName(trim($name));
                    }
                }
            }
            $list_tag_id = implode(',', $tagnames);
            if ($list_tag_id) {
                $query->where('a.id IN (' . $list_tag_id . ')');
            }
        }
        $db->setQuery($query);
        $items = $db->loadObjectList();
        if (count($items) > 0) {
            $xmap->changeLevel(1);
            foreach ($items as $item) {
                $xmap->printNode(self::mediaitem($item->media_id, $params, $parent));
            }
            $xmap->changeLevel(-1);
        }
        return true;
    }

    static function limititem($xmap, $parent, $catid, &$params, $itemid) {

        $db = JFactory::getDBO();
        if ($params['max_item'] != 0) {
            $limit = "LIMIT " . $params['max_item'];
        } else {
            $limit = "";
        }
        $query = "SELECT a.* from #__bt_media_items AS a where a.cate_id = '$catid'  and a.state = 1 $limit";
        $db->setQuery($query);
        $items = $db->loadObjectList();
        if (count($items) > 0) {
            $xmap->changeLevel(1);
            foreach ($items as $item) {
                $node = new stdclass();
                $node->id = $parent->id;
                $node->uid = $parent->uid . 'item' . $item->id;
                $node->browserNav = $parent->browserNav;
                $node->priority = $params['item_priority'];
                $node->changefreq = $params['item_changefreq'];
                $node->name = $item->name;
                $node->expandible = true;
                $node->secure = $parent->secure;
                $node->newsItem = 0;
                $node->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
                $node->catslug = $catid;
                $node->link = JFactory::getURI()->toString(array('scheme', 'host', 'port')) . JRoute::_('index.php?option=com_bt_media&id=' . $node->slug . '&view=detail');
                if ($xmap->printNode($node)) {
                    self::limititem($xmap, $parent, $item->id, $params, $itemid);
                    if ($params['add_images']) {
                        $string = "SELECT a.*  from #__bt_media_items AS a where a.item_id = $item->id  ";
                        $db->setQuery($string);
                        $addimages = $db->loadObjectList();
                        $images = '<img class="image" src="' . $params->get('file_save', 'images/bt_media').'/images/thumbnail/'.$addimages->image_path . '"/>';
                        $node->images = XmapHelper::getImages($images, $params['max_item'] ? $params['max_item'] : 1000);
                    }
                }
            }
            $xmap->changeLevel(-1);
        }
        return true;
    }

    static function mediaitem($id, &$params, $parent) {
        $db = JFactory::getDBO();
        $query = 'SELECT a.id ,a.name,a.alias FROM #__bt_media_items AS a where a.id = ' . $id . ' AND a.state = 1';
        $db->setQuery($query);
        $item = $db->loadObject();
        if ($item) {

            $node = new stdclass();
            $node->id = $parent->id;
            $node->uid = $parent->uid . 'item' . $id;
            $node->browserNav = $parent->browserNav;
            $node->priority = $params['item_priority'];
            $node->changefreq = $params['item_changefreq'];
            $node->name = $item->name;
            $node->expandible = true;
            $node->secure = $parent->secure;
            $node->newsItem = 0;
            $node->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
            $node->link = JFactory::getURI()->toString(array('scheme', 'host', 'port')) . JRoute::_('index.php?option=com_bt_media&id=' . $node->slug . '&view=detail&Itemid='.$node->id);
            if ($params['add_images']) {
                $images = '<img class="image" src="' .$params->get('file_save', 'images/bt_media').'/images/thumbnail/'. $item->image_path . '"/>';
                $node->images = XmapHelper::getImages($images, $params['max_item'] ? $params['max_item'] : 1000);
            }
            return $node;
        }
        return $item;
    }
    
    static function getChildCategory($id) {
        $db = $this->getDbo();
        $rs = array();
        $subQuery = $db->getQuery(TRUE);
        $subQuery->select('id');
        $subQuery->from('#__bt_media_categories');
        $subQuery->where('parent_id=' . $id);
        $db->setQuery($subQuery);
        $listCatId = $db->loadRowList();
        foreach ($listCatId as $value) {
            $rs[] = $value[0];
        }
        return $rs;
    }

    static function getAllChildCategory($arrayCatId, &$catResult) {
        $newArrayCatId = array();
        if (!empty($arrayCatId)) {
            foreach ($arrayCatId as $id) {
                $listCat = self::getChildCategory($id);
                $catResult = array_merge($catResult, $listCat);
                $newArrayCatId = array_merge($newArrayCatId, $listCat);
            }
            self::getAllChildCategory($newArrayCatId, $catResult);
        }
        return $catResult;
    }

}

?>