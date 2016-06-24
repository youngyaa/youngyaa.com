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
class Bt_mediaModeltag extends JModelAdmin {

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
    public function getTable($type = 'Tag', $prefix = 'Bt_mediaTable', $config = array()) {
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
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_bt_media.edit.tag.data', array());

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
    public function save($data) {
        $task = JFactory::getApplication()->input->get('task');

        if ($task == 'save2copy') {
            $data['alias'] = Bt_mediaLegacyHelper::createAlias($data['alias'], 'tag', $data['id'], TRUE);
            $data['name'] = JString::increment($data['name']);
        }
        if ($task == 'apply' || $task == 'save' || $task == 'save2new') {
            $data['name'] = strtolower($data['name']);
            $data['name'] = str_replace(array('\\', '/', ','), '', $data['name']);

            if (empty($data['alias'])) {
                $data['alias'] = Bt_mediaLegacyHelper::createAlias($data['name'], 'tag', $data['id']);
            } else {
                $data['alias'] = Bt_mediaLegacyHelper::createAlias($data['alias'], 'tag', $data['id']);
            }
            if (empty($data['created_date'])) {
                $data['created_date'] = JFactory::getDate()->calendar('Y-m-d H:i:s', true, true);
            }
        }
        parent::save($data);
        return true;
    }

}