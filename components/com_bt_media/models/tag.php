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
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');

/**
 * Bt_media model.
 */
class Bt_mediaModelTag extends JModelList {

    var $_item = null;

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since	1.6
     */
    protected function populateState($ordering = null, $direction = null) {
        $app = JFactory::getApplication('com_bt_media');
        $params = $app->getParams();

        // Load state from the request userState on edit or from the passed variable on default
        if (JFactory::getApplication()->input->get('layout') == 'edit') {
            $id = JFactory::getApplication()->getUserState('com_bt_media.edit.tag.id');
        } else {
            $id = JFactory::getApplication()->input->getInt('id');
            JFactory::getApplication()->setUserState('com_bt_media.edit.tag.id', $id);
        }
        if ($id) {
            $this->setState('tag.id', $id);
        } else {
            // Load the filter state.
            $tags = JFactory::getApplication()->input->getString('tags');
            $this->setState('filter.tags', $tags);
        }

        $user = JFactory::getUser();
        if ((!$user->authorise('core.edit.state', 'com_bt_media')) && (!$user->authorise('core.edit', 'com_bt_media'))) {
            $this->setState('filter.published', 1);
            $this->setState('filter.archived', 2);
        }

        // List state information
        if ($params->get('show_list_limit_item') && $params->get('show_list_limit_item') != 0) {
            $limit = (int) $params->get('show_list_limit_item');
        } else {
            $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
        }
        $this->setState('list.limit', $limit);

        $limitstart = JFactory::getApplication()->input->getInt('limitstart', 0);
        $this->setState('list.start', $limitstart);
        if (empty($ordering)) {
            $ordering = 'a.ordering';
        }

        // Load the parameters.
        $this->setState('params', $params);
        parent::populateState($ordering, $direction);
    }

    public function getListQuery() {
        $jinput = JFactory::getApplication()->input;
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('a.id as tag_id');
        $query->from('#__bt_media_tags AS a');


        $query->select('ai.id as aiid');
        $query->join('INNER', '#__bt_media_tags_xref as ai ON ai.tag_id = a.id');

        $query->select('i.*');
        $query->join('INNER', '#__bt_media_items as i ON ai.item_id = i.id');
        $query->where('i.state=1');

        // Join over the created by field 'created_by'
        $query->select('created_by.name AS created_by');
        $query->join('LEFT', '#__users AS created_by ON created_by.id = i.created_by');

        $query->select('c.id as cate_id,
                        c.name as category_name,
                        c.alias as cate_alias
                        ');
        $query->join('INNER', '#__bt_media_categories as c ON c.id = i.cate_id');
        $query->where('c.state=1');


        $query->where('a.state = 1');

        //Filter by tags
        $tags = $this->getState('filter.tags');
        if ($tags) {
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
        }



        $tag_id = $this->state->get("tag.id");
        if ($tag_id) {
            $query->where('a.id=' . $tag_id);
        }
        $params = $this->getState('params');



        $filter_media_type = $jinput->getString("filter_type");
        if ($filter_media_type && $filter_media_type != '') {
            $query->where("i.media_type = '" . $filter_media_type . "'");
        } elseif ($params->get('show_media_type') && $params->get('show_media_type') != '') {
            $query->where("i.media_type = '" . $params->get('show_media_type') . "'");
        }

        if ($params->get('order_type', 'ASC') && $params->get('order_type', 'ASC') != '') {
            $orderD = $params->get('order_type', 'ASC');
        } else {
            $orderD = $this->getState('list.direction');
        }

        $filter_ordering = $jinput->getString("filter_ordering");
        if ($filter_ordering && $filter_ordering != '') {
            $query->order("i." . $filter_ordering . ' ' . $orderD);
        } else {
            $query->order("i." . $params->get('show_ordering', 'ordering') . ' ' . $orderD);
        }

        $search = $jinput->getString('filter_search');
        if (!empty($search) && $search != JText::_('Enter your keywords')) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int) substr($search, 3));
            } else {
                $search = $db->Quote('%' . $db->escape($search, true) . '%');
                $query->where('(a.name LIKE ' . $search . ')');
            }
        }

        $query->group('i.id');
        $query->order('a.name ASC');
        return $query;
    }

    /**
     * Method to get an ojbect.
     *
     * @param	integer	The id of the object to get.
     *
     * @return	mixed	Object on success, false on failure.
     */
    public function &getData($id = null) {
        if ($this->_item === null) {
            $this->_item = false;

            if (empty($id)) {
                $id = $this->getState('tag.id');
            }

            // Get a level row instance.
            $table = $this->getTable();

            // Attempt to load the row.
            if ($table->load($id)) {
                // Check published state.
                if ($published = $this->getState('filter.published')) {
                    if ($table->state != $published) {
                        return $this->_item;
                    }
                }

                // Convert the JTable to a clean JObject.
                $properties = $table->getProperties(1);
                $this->_item = JArrayHelper::toObject($properties, 'JObject');
            } elseif ($error = $table->getError()) {
                $this->setError($error);
            }
        }

        return $this->_item;
    }

    public function getTable($type = 'Tag', $prefix = 'Bt_mediaTable', $config = array()) {
        $this->addTablePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to check in an item.
     *
     * @param	integer		The id of the row to check out.
     * @return	boolean		True on success, false on failure.
     * @since	1.6
     */
    public function checkin($id = null) {
        // Get the id.
        $id = (!empty($id)) ? $id : (int) $this->getState('tag.id');

        if ($id) {

            // Initialise the table
            $table = $this->getTable();

            // Attempt to check the row in.
            if (method_exists($table, 'checkin')) {
                if (!$table->checkin($id)) {
                    $this->setError($table->getError());
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Method to check out an item for editing.
     *
     * @param	integer		The id of the row to check out.
     * @return	boolean		True on success, false on failure.
     * @since	1.6
     */
    public function checkout($id = null) {
        // Get the user id.
        $id = (!empty($id)) ? $id : (int) $this->getState('tag.id');

        if ($id) {

            // Initialise the table
            $table = $this->getTable();

            // Get the current user object.
            $user = JFactory::getUser();

            // Attempt to check the row out.
            if (method_exists($table, 'checkout')) {
                if (!$table->checkout($user->get('id'), $id)) {
                    $this->setError($table->getError());
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Method to get the profile form.
     *
     * The base form is loaded from XML 
     * 
     * @param	array	$data		An optional array of data for the form to interogate.
     * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
     * @return	JForm	A JForm object on success, false on failure
     * @since	1.6
     */
    public function getForm($data = array(), $loadData = true) {
        // Get the form.
        $form = $this->loadForm('com_bt_media.tag', 'tag', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return	mixed	The data for the form.
     * @since	1.6
     */
    protected function loadFormData() {
        $data = $this->getData();

        //Support for 'multiple' field
        $data->cate_id = json_decode($data->cate_id);
        return $data;
    }

    /**
     * Method to save the form data.
     *
     * @param	array		The form data.
     * @return	mixed		The user id on success, false on failure.
     * @since	1.6
     */
    public function save($data) {
        $id = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('tag.id');
        $user = JFactory::getUser();

        if ($id) {
            //Check the user can edit this item
            $authorised = $user->authorise('core.edit', 'tag.' . $id);
        } else {
            //Check the user can create new items in this section
            $authorised = $user->authorise('core.create', 'com_bt_media');
        }

        if ($authorised !== true) {
            JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
            return false;
        }

        $table = $this->getTable();
        if ($table->save($data) === true) {
            return $id;
        } else {
            return false;
        }
    }

    /**
     * Increment the hit counter for the article.
     *
     * @param	int		Optional primary key of the article to increment.
     *
     * @return	boolean	True if successful; false otherwise and internal error set.
     */
    public function hit($pk = 0) {
        $hitcount = JFactory::getApplication()->input->getInt('hitcount', 1);

        if ($hitcount) {
            // Initialise variables.
            $pk = (!empty($pk)) ? $pk : (int) $this->getState('tag.id');
            $db = $this->getDbo();

            $db->setQuery(
                    'UPDATE #__bt_media_tags' .
                    ' SET hits = hits + 1' .
                    ' WHERE id = ' . (int) $pk
            );

            if (!$db->query()) {
                $this->setError($db->getErrorMsg());
                return false;
            }
        }

        return true;
    }

}