<?php
/**
 * @package 	plgSearchBt_media - BT Media Gallery Component
 * @version		1.0
 * @created		Aug 2013
 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

require_once JPATH_SITE . '/components/com_bt_media/router.php';

/**
 * BT Media Gallery plugin
 */
class plgSearchBt_media extends JPlugin {

    /**
     * @return array An array of search areas
     */
    function onContentSearchAreas() {
        JPlugin::loadLanguage('plg_search_bt_media', JPATH_ADMINISTRATOR);
        static $areas = array(
    'bt_media' => 'BT_MEDIA_ITEMS'
        );
        return $areas;
    }

    function onContentSearch($text, $phrase = '', $ordering = '', $areas = null) {
        $db = JFactory::getDbo();
        $app = JFactory::getApplication();
        $user = JFactory::getUser();
        $groups = implode(',', $user->getAuthorisedViewLevels());
        $tag = JFactory::getLanguage()->getTag();
        JPlugin::loadLanguage('plg_search_bt_media', JPATH_ADMINISTRATOR);

        require_once JPATH_SITE . '/components/com_bt_media/router.php';
        require_once JPATH_SITE . '/administrator/components/com_search/helpers/search.php';

        $searchText = $text;
        if (is_array($areas)) {
            if (!array_intersect($areas, array_keys($this->onContentSearchAreas()))) {
                return array();
            }
        }
        $limit = $this->params->def('search_limit', 50);

        $nullDate = $db->getNullDate();
        $date = JFactory::getDate();
        $now = $date->toSql();

        $text = trim($text);
        if ($text == '') {
            return array();
        }

        $wheres = array();
        switch ($phrase) {
            case 'exact':
                $text = $db->Quote('%' . $db->escape($text, true) . '%', false);
                $wheres2 = array();
                $wheres2[] = 'a.name LIKE ' . $text;
                $wheres2[] = 'a.description LIKE ' . $text;
                $where = '(' . implode(') OR (', $wheres2) . ')';
                break;

            case 'all':
            case 'any':
            default:
                $words = explode(' ', $text);
                $wheres = array();
                foreach ($words as $word) {
                    $word = $db->Quote('%' . $db->escape($word, true) . '%', false);
                    $wheres2 = array();
                    $wheres2[] = 'a.name LIKE ' . $word;
                    $wheres2[] = 'a.description LIKE ' . $word;
                    $wheres[] = implode(' OR ', $wheres2);
                }
                $where = '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';
                break;
        }

        $morder = '';
        switch ($ordering) {
            case 'oldest':
                $order = 'a.created_date ASC';
                break;

            case 'popular':
                $order = 'a.hits DESC';
                break;

            case 'alpha':
                $order = 'a.name ASC';
                break;

            case 'category':
                $order = 'c.name ASC, a.name ASC';
                $morder = 'a.name ASC';
                break;

            case 'newest':
            default:
                $order = 'a.created_date DESC';
                break;
        }

        $rows = array();
        $query = $db->getQuery(true);


        if ($limit > 0) {
            $query->clear();
            //sqlsrv changes
            $case_when = ' CASE WHEN ';
            $case_when .= $query->charLength('a.alias');
            $case_when .= ' THEN ';
            $a_id = $query->castAsChar('a.id');
            $case_when .= $query->concatenate(array($a_id, 'a.alias'), ':');
            $case_when .= ' ELSE ';
            $case_when .= $a_id . ' END as slug';

            $case_when1 = ' CASE WHEN ';
            $case_when1 .= $query->charLength('c.alias');
            $case_when1 .= ' THEN ';
            $c_id = $query->castAsChar('c.id');
            $case_when1 .= $query->concatenate(array($c_id, 'c.alias'), ':');
            $case_when1 .= ' ELSE ';
            $case_when1 .= $c_id . ' END as catslug';

            $query->select('a.id AS id, a.name AS title, \'\' as metadesc, \'\' as metakey, a.created_date AS created');
            $query->select('a.description AS text, a.alias');
            $query->select('c.id as catid, c.name AS section, ' . $case_when . ',' . $case_when1 . ', ' . '\'2\' AS browsernav');

            $query->from('#__bt_media_items AS a');
            $query->innerJoin('#__bt_media_categories AS c ON a.cate_id =c.id');
            $query->where('(' . $where . ')' . 'AND a.state=1 AND c.state = 1 AND a.access IN (' . $groups . ') '
                    . 'AND c.access IN (' . $groups . ') ');
            $query->group('a.id, a.name, a.created_date, a.description,c.name, a.alias, c.alias, c.id');
            $query->order($order);

            // Filter by language
            if ($app->isSite() && $app->getLanguageFilter()) {
                $query->where('a.language in (' . $db->Quote($tag) . ',' . $db->Quote('*') . ')');
                $query->where('c.language in (' . $db->Quote($tag) . ',' . $db->Quote('*') . ')');
            }

            $db->setQuery($query, 0, $limit);
            $list = $db->loadObjectList();
            $limit -= count($list);

            if (isset($list)) {
                foreach ($list as $key => $item) {
                    $list[$key]->href = JRoute::_('index.php?option=com_bt_media&view=detail&id=' . $item->id.':'.$item->alias); //&id=' . $item->slug . '&catid_rel=' . $item->catslug);
                }
            }
            $rows[] = $list;
        }

        $results = array();
        if (count($rows)) {
            foreach ($rows as $row) {
                $new_row = array();
                if ($row) {
                    foreach ($row as $key => $article) {
                        if (searchHelper::checkNoHTML($article, $searchText, array('text', 'title', 'metadesc', 'metakey'))) {
                            $new_row[] = $article;
                        }
                    }
                    $results = array_merge($results, (array) $new_row);
                }
            }
        }

        return $results;
    }

}
