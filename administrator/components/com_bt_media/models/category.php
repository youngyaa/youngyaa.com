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

jimport('joomla.application.component.modeladmin');

/**
 * Bt_media model.
 */
class Bt_mediaModelcategory extends JModelAdmin {

    /**
     * @var		string	The prefix to use with controller messages.
     * @since	1.6
     */
    protected $text_prefix = 'COM_BT_MEDIA';

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param	type	The table type to instantiate
     * @param	string	A prefix for the table class name. Optional.
     * @param	array	Configuration array for model. Optional.
     * @return	JTable	A database object
     * @since	1.6
     */
    public function getTable($type = 'Category', $prefix = 'Bt_mediaTable', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param	array	$data		An optional array of data for the form to interogate.
     * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
     * @return	JForm	A JForm object on success, false on failure
     * @since	1.6
     */
    public function getForm($data = array(), $loadData = true) {
        // Initialise variables.
        $app = JFactory::getApplication();

        // Get the form.
        $form = $this->loadForm('com_bt_media.category', 'category', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        $jinput = JFactory::getApplication()->input;

        // The front end calls this model and uses a_id to avoid id clashes so we need to check for that first.
        $id = $jinput->get('id', 0);
        // Determine correct permissions to check.
        if ($this->getState('category.id')) {
            $id = $this->getState('category.id');
        }

        $user = JFactory::getUser();

        if ($id != 0 && (!$user->authorise('core.edit.state', 'com_bt_media.category.' . (int) $id)) || ($id == 0 && !$user->authorise('core.edit.state', 'com_bt_media'))
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
    
    
    protected function canDelete($record) {
        $user = JFactory::getUser();
        $canDelete = FALSE;
        // Check against the category.
        if (!empty($record->id)) {
            if ($user->authorise('core.delete', 'com_bt_media.category.' . (int) $record->id)) {
                $canDelete = TRUE;
            } else {
                if ($user->authorise('media.delete.own', 'com_bt_media.category.' . (int) $record->id) && $user->id == (int)$record->created_by) {
                    $canDelete = TRUE;
                }
            }
            return $canDelete;
        }
        // Default to component settings if category not known.
        else {
            return parent::canDelete($record);
        }
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return	mixed	The data for the form.
     * @since	1.6
     */
    protected function loadFormData() {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_bt_media.edit.category.data', array());

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * Method to get a single record.
     *
     * @param	integer	The id of the primary key.
     *
     * @return	mixed	Object on success, false on failure.
     * @since	1.6
     */
    public function getItem($pk = null) {
        if ($item = parent::getItem($pk)) {

            //Do any procesing on fields here if needed
        }

        return $item;
    }

    /**
     * Prepare and sanitise the table prior to saving.
     *
     * @since	1.6
     */
//    protected function prepareTable($table) {
//        jimport('joomla.filter.output');
//
//        if (empty($table->id)) {
//
//            // Set ordering to the last item if not set
//            if (@$table->ordering === '') {
//                $db = JFactory::getDbo();
//                $db->setQuery('SELECT MAX(ordering) FROM #__bt_media_categories');
//                $max = $db->loadResult();
//                $table->ordering = $max + 1;
//            }
//        }
//    }

    public function save($data) {
        $task = JFactory::getApplication()->input->get('task');
        if ($data['parent_id']) {
            $data['level'] = Bt_mediaLegacyHelper::getCategoryLevel($data['parent_id']) + 1;
        } else {
            $data['level'] = 1;
        }

        if ($task == 'save2copy') {
            $data['alias'] = Bt_mediaLegacyHelper::createAlias($data['alias'], 'category', $data['id'], TRUE);
            $data['name'] = JString::increment($data['name']);
        }
        if ($task == 'apply' || $task == 'save' || $task == 'save2new') {
            if (empty($data['alias'])) {
                $data['alias'] = Bt_mediaLegacyHelper::createAlias($data['name'], 'category', $data['id']);
            } else {
                $data['alias'] = Bt_mediaLegacyHelper::createAlias($data['alias'], 'category', $data['id']);
            }
            if (empty($data['created_date'])) {
                $data['created_date'] = JFactory::getDate()->calendar('Y-m-d H:i:s', true, true);
            }
        }
        parent::save($data);
        return true;
    }

    public function saveorder($idArray = null, $lft_array = null) {
        $query = $this->_db->getQuery(true);

        // Validate arguments
        if (is_array($idArray) && is_array($lft_array) && count($idArray) == count($lft_array)) {
            for ($i = 0, $count = count($idArray); $i < $count; $i++) {
                // Do an update to change the lft values in the table for each id
                $query->clear()
                        ->update('#__bt_media_categories')
                        ->where('id = ' . (int) $idArray[$i])
                        ->set('ordering = ' . (int) $lft_array[$i]);

                $this->_db->setQuery($query)->execute();
            }
            return true;
        } else {
            return false;
        }
    }
    
}