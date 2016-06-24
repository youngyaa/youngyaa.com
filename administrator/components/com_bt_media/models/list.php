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
 * Methods supporting a list of Bt_media records.
 */
class Bt_mediaModellist extends JModelList {

    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'name', 'a.name',
                'image_path', 'a.image_path',
                'video_path', 'a.video_path',
                'cate_id', 'a.cate_id',
                'ordering', 'a.ordering',
                'state', 'a.state',
                'created_by', 'a.created_by',
                'source_of_media', 'a.source_of_media',
                'media_type', 'a.media_type',
                'language', 'a.language',
                'access', 'a.access',
                'hits', 'a.hits',
                'featured', 'a.featured',
            );
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     */
    protected function populateState($ordering = null, $direction = null) {
        // Initialise variables.
        $app = JFactory::getApplication('administrator');

        // Load the filter state.
        $search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
        $this->setState('filter.state', $published);


        //Filtering cate_id
        $this->setState('filter.cate_id', $app->getUserStateFromRequest($this->context . '.filter.cate_id', 'filter_cate_id', '', 'string'));

        //Filtering media_type
        $this->setState('filter.media_type', $app->getUserStateFromRequest($this->context . '.filter.media_type', 'filter_media_type', '', 'string'));

        //Filtering language
        $this->setState('filter.language', $app->getUserStateFromRequest($this->context . '.filter.language', 'filter_language', '', 'string'));

        //Filtering access
        $this->setState('filter.access', $app->getUserStateFromRequest($this->context . '.filter.access', 'filter_access', '', 'string'));

        // Load the parameters.
        $params = JComponentHelper::getParams('com_bt_media');
        $this->setState('params', $params);

        // List state information.
        parent::populateState('a.name', 'asc');
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param	string		$id	A prefix for the store id.
     * @return	string		A store id.
     * @since	1.6
     */
    protected function getStoreId($id = '') {
        // Compile the store id.
        $id.= ':' . $this->getState('filter.search');
        $id.= ':' . $this->getState('filter.state');

        return parent::getStoreId($id);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return	JDatabaseQuery
     * @since	1.6
     */
    protected function getListQuery() {
        // Create a new query object.
        $db = $this->getDbo();
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


        $query->select('mc.name as category_name');
        $query->join('INNER', '#__bt_media_categories AS mc ON mc.id=a.cate_id');

        // Join over the user field 'created_by'
        $query->select('created_by.name AS created_by');
        $query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');

        // Join over the user field access
        $query->select('ag.title AS access');
        $query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

        $query->select('lang.title AS language_title');
        $query->join('LEFT', '#__languages AS lang ON lang.lang_code = a.language');


        // Filter by published state
        $published = $this->getState('filter.state');
        if (is_numeric($published)) {
            $query->where('a.state = ' . (int) $published);
        } else if ($published === '') {
            $query->where('(a.state IN (0, 1))');
        }


        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int) substr($search, 3));
            } else {
                $search = $db->Quote('%' . $db->escape($search, true) . '%');
                $query->where('( a.name LIKE ' . $search . '  OR  a.source_of_media LIKE ' . $search . ' )');
            }
        }



        //Filtering cate_id
        $filter_cate_id = $this->state->get("filter.cate_id");
        if ($filter_cate_id) {
            $query->where("a.cate_id = '" . $filter_cate_id . "'");
        }

        //Filtering media_type
        $filter_media_type = $this->state->get("filter.media_type");
        if ($filter_media_type) {
            $query->where("a.media_type = '" . $filter_media_type . "'");
        }
        
        //Filter by language
        $filter_by_language = $this->getState('filter.language');
        if ($filter_by_language) {
            $query->where("a.language = '" . $filter_by_language . "'");
        }
        
        //Filter by access level
        $filter_by_access = $this->getState('filter.access');
        if ($filter_by_access) {
            $query->where("a.access = '" . $filter_by_access . "'");
        }


        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering');
        $orderDirn = $this->state->get('list.direction');
        if ($orderCol && $orderDirn) {
            $query->order($db->escape($orderCol . ' ' . $orderDirn));
        }
        return $query;
    }
    
}
