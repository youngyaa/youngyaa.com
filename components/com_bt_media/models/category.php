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
class Bt_mediaModelCategory extends JModelList {

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

        // Load state from the request userState on edit or from the passed variable on default
        if (JFactory::getApplication()->input->get('layout') == 'edit') {
            $id = JFactory::getApplication()->getUserState('com_bt_media.edit.category.id');
        } else {
            $id = JFactory::getApplication()->input->getInt('catid');
            JFactory::getApplication()->setUserState('com_bt_media.edit.category.id', $id);
        }
        $this->setState('category.id', $id);

        // Load the filter state.
        $search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        // List state information
        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
        $this->setState('list.limit', $limit);

        $limitstart = JFactory::getApplication()->input->getInt('limitstart', 0);
        $this->setState('list.start', $limitstart);
        if (empty($ordering)) {
            $ordering = 'a.ordering';
        }
        $user = JFactory::getUser();
        if ((!$user->authorise('core.edit.state', 'com_bt_media')) && (!$user->authorise('core.edit', 'com_bt_media'))) {
            $this->setState('filter.published', 1);
            $this->setState('filter.archived', 2);
        }

        // Load the parameters.
        $params = $app->getParams();
        $this->setState('params', $params);
        parent::populateState($ordering, $direction);
    }

    public function getListQuery() {
        $jinput = JFactory::getApplication()->input;
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('a.*');
        $query->from('#__bt_media_categories AS a');

        $query->where('a.state = 1');

        $search = JFactory::getApplication()->input->getString('filter.search');
        if (!empty($search) && $search != JText::_('COM_BT_MEDIA_ENTER_YOUR_KEYWORD')) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int) substr($search, 3));
            } else {
                $search = $db->Quote('%' . $db->escape($search, true) . '%');
                $query->where('(a.name LIKE ' . $search . ')');
            }
        }
        $id = $this->getState('category.id');
        $query->where('a.parent_id = ' . $id);

        $params = $this->getState('params');
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
            if ($orderby != 'rating' && $orderby != 'featured' && $orderby != 'media_type') {
                $query->order("a." . $orderby . ' ' . $orderD);
            }
        }
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
                $id = $this->getState('category.id');
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

    public function getTable($type = 'Category', $prefix = 'Bt_mediaTable', $config = array()) {
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
        $id = (!empty($id)) ? $id : (int) $this->getState('category.id');

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
        $id = (!empty($id)) ? $id : (int) $this->getState('category.id');

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
        $form = $this->loadForm('com_bt_media.category', 'category', array('control' => 'jform', 'load_data' => $loadData));
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
        $id = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('category.id');
        $user = JFactory::getUser();

        if ($id) {
            //Check the user can edit this item
            $authorised = $user->authorise('core.edit', 'category.' . $id);
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
            $pk = (!empty($pk)) ? $pk : (int) $this->getState('category.id');
            $db = $this->getDbo();

            $db->setQuery(
                    'UPDATE #__bt_media_categories' .
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