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

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Media_items records.
 */
class Bt_mediaModelList extends JModelList {

    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array()) {
        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since	1.6
     */
    protected function populateState($ordering = null, $direction = null) {

        // List state information.        
        parent::populateState($ordering, $direction);
        // Initialise variables.

        $app = JFactory::getApplication();
        $params = $app->getParams();
        $jinput = $app->input;

        // List state information
        if ($params->get('show_list_limit_item') && $params->get('show_list_limit_item') != 0) {
            $limit = (int) $params->get('show_list_limit_item');
        } else {
            $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
        }
        $this->setState('list.limit', $limit);


        $limitstart = JFactory::getApplication()->input->getInt('limitstart', 0);
        $this->setState('list.start', $limitstart);
        //Filtering media_type
        $this->setState('filter.ordering', $app->getUserStateFromRequest($this->context . '.filter.ordering', 'filter_ordering', '', 'string'));
        //Filtering categories
        if ($jinput->getString('view') == 'list') {
            $this->setState('filter.categories', $app->getUserStateFromRequest($this->context . '.filter.categories', 'categories', '', 'Array'));
        }
        if ($jinput->getString('view') == 'category') {
            $this->setState('filter.categories', array(JFactory::getApplication()->input->getInt('catid')));
        }


        //Filtering by language
        $this->setState('filter.language', $app->getLanguageFilter());


        if (empty($ordering)) {
            $ordering = 'a.ordering';
        }

        $this->setState('params', $params);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return	JDatabaseQuery
     * @since	1.6
     */
    protected function getListQuery() {
        // Create a new query object.
        $jinput = JFactory::getApplication()->input;
        $db = $this->getDbo();
        $user = JFactory::getUser();
        $groups = implode(',', $user->getAuthorisedViewLevels());
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
                $this->getState(
                        'list.select', 'a.*'
                )
        );

        $query->from('`#__bt_media_items` AS a');


        // Join over the users for the checked out user.
        $query->select('uc.name AS editor');
        $query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

        // Join over the created by field 'created_by'
        $query->select('created_by.name AS created_by');
        $query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');

        // Join category
        $query->select('c.name as category_name, c.alias as category_alias');
        $query->join('INNER', '#__bt_media_categories AS c ON a.cate_id = c.id');
        $query->where('c.access IN (' . $groups . ')');
        $query->where('c.state=1');

        //access view level
        $query->where('a.access IN (' . $groups . ')');


        $params = $this->getState('params');
        if ($jinput->getString('filter_type') && $jinput->getString('filter_type') != "") {
            $query->where("a.media_type = '" . $jinput->getString('filter_type') . "'");
        } elseif ($params->get('show_media_type') && $params->get('show_media_type') != '') {
            $query->where("a.media_type = '" . $params->get('show_media_type') . "'");
        }


        //order by featured
        if ($jinput->getString('filter_direction')) {
            $orderD = $jinput->getString('filter_direction');
        } else
        if ($params->get('order_type')) {
            $orderD = $params->get('order_type');
        } else {
            $orderD = $this->getState('list.direction');
        }
        $orderby = '';
        if ($jinput->getString('filter_ordering')) {
            $orderby = $jinput->getString('filter_ordering');
        } else
        if ($params->get('show_ordering')) {
            $orderby = $params->get('show_ordering');
        }

        if ($orderby) {
            if ($orderby == 'rating') {
                $query->order('a.vote_sum/a.vote_count ' . $orderD);
            } else
            if ($orderby == 'random') {
                $query->order("RAND()");
            } else {
                $query->order("a." . $orderby . ' ' . $orderD);
            }
        }

        $query->order('a.featured ' . $orderD);
        $query->order('a.hits ' . $orderD);


        // Filter by published state
        $published = $this->getState('filter.state');
        if (is_numeric($published)) {
            $query->where('a.state = ' . (int) $published);
        } else {
            $query->where('(a.state = 1)');
        }


        // Filter by search in title
        $search = $jinput->getString('filter_search');
        if (!empty($search) && $search != JText::_('COM_BT_MEDIA_ENTER_YOUR_KEYWORD')) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int) substr($search, 3));
            } else {
                $search = $db->Quote('%' . $db->escape($search, true) . '%');
                $query->where('( a.name LIKE ' . $search . ')');
            }
        }



        //Filtering cate_id
        $filter_by_cate = $this->state->get("filter.categories");
        if ($filter_by_cate && count($filter_by_cate) > 0 && $filter_by_cate[0] != "") {
            $showSubItems = $params->get('show_sub_media', 0);
            if ($showSubItems == 1) {
                $listCatId = $this->getAllChildCategory($filter_by_cate, $filter_by_cate);
                if ($listCatId && count($listCatId) > 0) {
                    $cats = implode(',', $listCatId);
                    $query->where("a.cate_id IN(" . $cats . ")");
                } else {
                    $query->where("a.cate_id = '" . $filter_by_cate[0] . "'");
                }
            }
            if ($showSubItems == 0) {
                $query->where("a.cate_id IN(" . implode(', ', $filter_by_cate) . ")");
            }
        }

        if ($jinput->getString('filter_featured') && $jinput->getString('filter_featured') != "") {
            $query->where("a.featured = '" . $jinput->getString('filter_featured') . "'");
        }

        $query->where('a.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
        return $query;
    }

    private function getChildCategory($id) {
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

    private function getAllChildCategory($arrayCatId, &$catResult) {
        $newArrayCatId = array();
        if (!empty($arrayCatId)) {
            foreach ($arrayCatId as $id) {
                $listCat = $this->getChildCategory($id);
                $catResult = array_merge($catResult, $listCat);
                $newArrayCatId = array_merge($newArrayCatId, $listCat);
            }
            $this->getAllChildCategory($newArrayCatId, $catResult);
        }
        return $catResult;
    }

    public function hits($itemid) {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->update('#__bt_media_items')->set('hits=hits+1')->where('id=' . $itemid);
        $db->setQuery($query);
        $db->execute();
    }

}

