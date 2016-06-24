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
class Bt_mediaModelTags extends JModelList {

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

        // List state information
        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
        $this->setState('list.limit', $limit);


        $limitstart = JFactory::getApplication()->input->getInt('limitstart', 0);
        $this->setState('list.start', $limitstart);
        //Filtering media_type
        $this->setState('filter.ordering', $app->getUserStateFromRequest($this->context . '.filter.ordering', 'filter_ordering', '', 'string'));
        //Filtering categories
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
        $db = $this->getDbo();
        $user = JFactory::getUser();
        $groups = implode(',', $user->getAuthorisedViewLevels());
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
                $this->getState(
                        'list.select', 'a.*, (SELECT COUNT(it.id) FROM #__bt_media_tags_xref as it INNER JOIN #__bt_media_items as i ON i.id = it.item_id where it.tag_id = a.id) as item_count'
                )
        );

        $query->from('#__bt_media_tags AS a');


        // Join over the users for the checked out user.
        $query->select('uc.name AS editor');
        $query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

        // Join over the created by field 'created_by'
        $query->select('created_by.name AS created_by');
        $query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');

        //access view level
        $query->where('a.access IN (' . $groups . ')');


        $filter_ordering = $this->state->get("list.ordering");
        if ($filter_ordering && $filter_ordering != '') {
			if($filter_ordering == 'count'){
				$query->order("item_count DESC");
			}else
            if ($filter_ordering == 'alphabet') {
                $query->order("a.name ASC");
            } elseif ($filter_ordering == 'random') {
                $query->order("RAND()");
            } else {
                $query->order("a." . $filter_ordering . ' DESC');
            }
        }


        // Filter by published state
        $published = $this->getState('filter.state');
        if (is_numeric($published)) {
            $query->where('a.state = ' . (int) $published);
        } else {
            $query->where('(a.state = 1)');
        }

        $query->where('a.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
        return $query;
    }
}