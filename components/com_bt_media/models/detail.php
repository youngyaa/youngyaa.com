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
// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');

/**
 * Student model.
 */
class Bt_mediaModelDetail extends JModelForm {

    var $_item = null;

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since	1.6
     */
    protected function populateState() {
        $app = JFactory::getApplication('com_bt_media');

        // Load state from the request userState on edit or from the passed variable on default
        if (JFactory::getApplication()->input->get('layout') == 'edit') {
            $id = JFactory::getApplication()->getUserState('com_bt_media.edit.detail.id');
        } else {
            $id = JFactory::getApplication()->input->getInt('id');
            JFactory::getApplication()->setUserState('com_bt_media.edit.detail.id', $id);
        }
        $this->setState('detail.id', $id);

        // Load the parameters.
        $params = $app->getParams();
        $params_array = $params->toArray();
        if (isset($params_array['item_id'])) {
            $this->setState('detail.id', $params_array['item_id']);
        }
        $user = JFactory::getUser();
        if ((!$user->authorise('core.edit.state', 'com_bt_media')) && (!$user->authorise('core.edit', 'com_bt_media'))) {
            $this->setState('filter.published', 1);
            $this->setState('filter.archived', 2);
        }
        $this->setState('params', $params);
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

            $layout = JFactory::getApplication()->input->get('layout');
            if (!$layout || $layout == 'default') {
                if (empty($id)) {
                    $id = $this->getState('detail.id');
                }
            }
            if($layout == 'edit'){
                $id = JFactory::getApplication()->input->get('id');
            }

            // Get a level row instance.
            $table = $this->getTable();

            // Attempt to load the row.
            if ($table->load($id)) {

                if ($published = $this->getState('filter.published')) {
                    if ($table->state != $published) {

                        return $this->_item;
                    }
                }

                // Convert the JTable to a clean JObject.
                //get array public properties;
                $properties = $table->getProperties(1);


                $this->_item = JArrayHelper::toObject($properties, 'JObject');
            } elseif ($error = $table->getError()) {
                $this->setError($error);
            }
        }

        return $this->_item;
    }

    public function getTable($type = 'Detail', $prefix = 'Bt_mediaTable', $config = array()) {
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
        $id = (!empty($id)) ? $id : (int) $this->getState('detail.id');

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
        $id = (!empty($id)) ? $id : (int) $this->getState('detail.id');

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
        $form = $this->loadForm('com_bt_media.detail', 'detail', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }

        $jinput = JFactory::getApplication()->input;

        // The front end calls this model and uses a_id to avoid id clashes so we need to check for that first.
        $id = $jinput->get('id', 0);
        // Determine correct permissions to check.
        if ($this->getState('detail.id')) {
            $id = $this->getState('detail.id');
        }

        $user = JFactory::getUser();

        if ($id != 0 && (!$user->authorise('core.edit.state', 'com_bt_media.media.' . (int) $id)) || ($id == 0 && !$user->authorise('core.edit.state', 'com_bt_media'))
        ) {
            // Disable fields for display.
            $form->setFieldAttribute('featured', 'disabled', 'true');
            $form->setFieldAttribute('ordering', 'disabled', 'true');
            $form->setFieldAttribute('state', 'disabled', 'true');

            // Disable fields while saving.
            // The controller has already verified this is an article you can edit.
            $form->setFieldAttribute('featured', 'filter', 'unset');
            $form->setFieldAttribute('ordering', 'filter', 'unset');
            $form->setFieldAttribute('state', 'filter', 'unset');
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
        $data = JFactory::getApplication()->getUserState('com_bt_media.edit.detail.data', array());
        if (empty($data)) {
            $data = $this->getData();
        }

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
        $id = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('detail.id');
        $state = (!empty($data['state'])) ? 1 : 0;
        $user = JFactory::getUser();

        if ($id) {
            //Check the user can edit this item
            $authorised = $user->authorise('core.edit', 'com_bt_media.detail.' . $id) || $authorised = $user->authorise('core.edit.own', 'com_bt_media.detail.' . $id);
            if ($user->authorise('core.edit.state', 'com_bt_media.detail.' . $id) !== true && $state == 1) { //The user cannot edit the state of the item.
                $data['state'] = 0;
            }
        } else {
            //Check the user can create new items in this section
            $authorised = $user->authorise('core.create', 'com_bt_media');
            if ($user->authorise('core.edit.state', 'com_bt_media.detail.' . $id) !== true && $state == 1) { //The user cannot edit the state of the item.
                $data['state'] = 0;
            }
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

    function delete($data) {
        $id = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('detail.id');
        if (JFactory::getUser()->authorise('core.delete', 'com_bt_media.detail.' . $id) !== true) {
            JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
            return false;
        }
        $table = $this->getTable();
        if ($table->delete($data['id']) === true) {
            return $id;
        } else {
            return false;
        }

        return true;
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
            $pk = (!empty($pk)) ? $pk : (int) $this->getState('detail.id');
            $db = $this->getDbo();

            $db->setQuery(
                    'UPDATE #__bt_media_items' .
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