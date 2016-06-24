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
// No direct access
defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/controller.php';
require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/images.php';

/**
 * Detail controller class.
 */
class Bt_mediaControllerDetail extends Bt_mediaController {

    private $result = array('success' => false, 'message' => '', 'data' => null);
    private $objParams;

    function __construct() {
        $this->view_list = 'list';

        $this->objParams = new stdClass();
        $this->objParams->sessions = JFactory::getSession();
        $this->objParams->params = JComponentHelper::getParams('com_bt_media');
        $this->objParams->thumbWidth = $this->objParams->params->get('thumb_image_width', 200);
        $this->objParams->thumbHeight = $this->objParams->params->get('thumb_image_height', 130);
        $this->objParams->image = new BTMediaImageHelper();
        $this->objParams->jinput = JFactory::getApplication()->input;
        /**
         * Lấy các ảnh đã có 
         */
        $this->objParams->saveDir = JPATH_SITE . DIRECTORY_SEPARATOR . $this->objParams->params->get('file_save', 'images/bt_media');
        $this->createDir($this->objParams->saveDir);
        $this->createDir($this->objParams->saveDir . '/images');
        $this->createDir($this->objParams->saveDir . '/videos');

        $this->objParams->thumbSaveDir = $this->objParams->saveDir . '/images/thumbnail';
        $this->createDir($this->objParams->thumbSaveDir);
        $this->objParams->largeSaveDir = $this->objParams->saveDir . '/images/large';
        $this->createDir($this->objParams->largeSaveDir);
        $this->objParams->originalSaveDir = $this->objParams->saveDir . '/images/original';
        $this->createDir($this->objParams->originalSaveDir);

        $this->objParams->videoOriginalSaveDir = $this->objParams->saveDir . '/videos';
        $this->createDir($this->objParams->videoOriginalSaveDir);

//Image edit upload temp
        $this->createDir($this->objParams->saveDir . '/temp');
        $this->objParams->imageTempSaveDir = $this->objParams->saveDir . '/temp/images';
        $this->createDir($this->objParams->imageTempSaveDir);
        $this->objParams->videoTempSaveDir = $this->objParams->saveDir . '/temp/videos';
        $this->createDir($this->objParams->videoTempSaveDir);
        parent::__construct();
    }

    /**
     * Method to check out an item for editing and redirect to the edit form.
     *
     * @since	1.6
     */
    public function edit() {
        $app = JFactory::getApplication();

        // Get the previous edit id (if any) and the current edit id.
        $previousId = (int) $app->getUserState('com_bt_media.edit.detail.id');
        $editId = JFactory::getApplication()->input->getInt('id', null, 'array');

        // Set the user id for the user to edit in the session.
        $app->setUserState('com_bt_media.edit.detail.id', $editId);

        // Get the model.
        $model = $this->getModel('Detail', 'DetailModel');

        // Check out the item
        if ($editId) {
            $model->checkout($editId);
        }

        // Check in the previous user.
        if ($previousId) {
            $model->checkin($previousId);
        }

        // Redirect to the edit screen.
        $this->setRedirect(JRoute::_('index.php?option=com_bt_media&view=detail&layout=edit', false));
    }

    /**
     * Method to save a user's profile data.
     *
     * @return	void
     * @since	1.6
     */
    public function save() {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Initialise variables.
        $app = JFactory::getApplication();
        $model = $this->getModel();

        // Get the user data.
        $data = JFactory::getApplication()->input->get('jform', array(), 'array');

        // Validate the posted data.
        $form = $model->getForm();
        if (!$form) {
            JError::raiseError(500, $model->getError());
            return false;
        }

        // Validate the posted data.
        $data = $model->validate($form, $data);


        // Check for errors.
        if ($data === false) {
            // Get the validation messages.
            $errors = $model->getErrors();

            // Push up to three validation messages out to the user.
            for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
                if ($errors[$i] instanceof Exception) {
                    $app->enqueueMessage($errors[$i]->getMessage(), 'warning');
                } else {
                    $app->enqueueMessage($errors[$i], 'warning');
                }
            }

            // Save the data in the session.
            $app->setUserState('com_bt_media.edit.detail.data', $data);

            // Redirect back to the edit screen.
            $id = (int) $app->getUserState('com_bt_media.edit.detail.id');
            $this->setRedirect(JRoute::_('index.php?option=com_bt_media&view=detail&layout=edit&id=' . $id, false));
            return false;
        }

        if (!JFactory::getApplication()->input->getString('layout')) {
            JFactory::getApplication()->input->set('layout', 'edit');
        }
        // Attempt to save the data.
        $return = $model->save($data);
//        var_dump($return);
//        die;
        // Check for errors.
        if ($return === false) {
            // Save the data in the session.
            $app->setUserState('com_bt_media.edit.detail.data', $data);

            // Redirect back to the edit screen.
            $id = (int) $app->getUserState('com_bt_media.edit.detail.id');
            $this->setMessage(JText::sprintf('Save failed', $model->getError()), 'warning');
            $this->setRedirect(JRoute::_('index.php?option=com_bt_media&view=detail&layout=edit&id=' . $id, false));
            return false;
        }


        // Check in the profile.
        if ($return) {
            $model->checkin($return);
        }

        // Clear the profile id from the session.
        $app->setUserState('com_bt_media.edit.detail.id', null);

        // Redirect to the list screen.
        $this->setMessage(JText::_('Item saved successfully'));
        $menu = & JSite::getMenu();
        $item = $menu->getActive();
        $this->setRedirect(JRoute::_($item->link, false));

        // Flush the data from the session.
        $app->setUserState('com_bt_media.edit.detail.data', null);
    }

    function cancel() {
        $this->setRedirect($this->getReturnPage());
    }

    public function remove() {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Initialise variables.
        $app = JFactory::getApplication();
        $model = $this->getModel('Detail', 'Bt_mediaModel');

        // Get the user data.
        $data = JFactory::getApplication()->input->get('jform', array(), 'array');

        // Validate the posted data.
        $form = $model->getForm();
        if (!$form) {
            JError::raiseError(500, $model->getError());
            return false;
        }
        $pks = array($data['id']);

        // Attempt to save the data.
        $return = $model->delete($pks);

        // Check for errors.
        if ($return === false) {
            // Save the data in the session.
            $app->setUserState('com_bt_media.edit.detail.data', $data);

            // Redirect back to the edit screen.
            $id = (int) $app->getUserState('com_bt_media.edit.detail.id');
            $this->setMessage(JText::sprintf('Delete failed', $model->getError()), 'warning');
            $this->setRedirect(JRoute::_('index.php?option=com_bt_media&view=detail&layout=edit&id=' . $id, false));
            return false;
        }


        // Check in the profile.
        if ($return) {
            $model->checkin($return);
        }

        // Clear the profile id from the session.
        $app->setUserState('com_bt_media.edit.detail.id', null);

        // Redirect to the list screen.
        $this->setMessage(JText::_('Item deleted successfully'));
        $menu = & JSite::getMenu();
        $item = $menu->getActive();
        $this->setRedirect(JRoute::_($item->link, false));

        // Flush the data from the session.
        $app->setUserState('com_bt_media.edit.detail.data', null);
    }

    public function getModel($name = 'Detail', $prefix = 'Bt_mediaModel', $config = array()) {
        $this->addModelPath(JPATH_COMPONENT_ADMINISTRATOR . '/models');
        return JModelLegacy::getInstance($name, $prefix, $config);
    }

    public function editImageUpload() {
        $oldFile = '';
        $source_server = '';
        $model = $this->getModel();
        if (isset($_POST['filename'])) {
            $oldFile_data = $_POST['filename'];
            if ($oldFile_data) {
                $oldFile_p = explode("/", $oldFile_data);
                $oldFile = $oldFile_p[count($oldFile_p) - 1];
            }
        }
        if(isset($_POST['source_server'])){
           $source_server = $_POST['source_server'];
        }
        $file = $_FILES['Filedata'];
        $this->result = $model->editImageUpload($file, $oldFile, $source_server, $this->objParams);
        $model->obEndClear();
        echo json_encode($this->result);
        exit;
    }

    public function getDataUrl() {
        $url = rtrim(urldecode($_POST['data']), '/');
        $oldFile = '';
        $media_type = '';
        $source_server = '';
        if (isset($_POST['filename'])) {
            $oldFile_data = $_POST['filename'];
            if ($oldFile_data) {
                $oldFile_p = explode("|", $oldFile_data);
                $oldFile = $oldFile_p[count($oldFile_p) - 1];
            }
        }
        if(isset($_POST['source_server'])){
           $source_server = $_POST['source_server'];
        }
        if(isset($_POST['media_type'])){
           $media_type = $_POST['media_type'];
        }
        $model = $this->getModel();
        $this->result = $model->getDataUrl($url, $oldFile, $source_server, $media_type, $this->objParams);
        $model->obEndClear();
        echo json_encode($this->result);
        exit;
    }

    public function html5UpdateUpload() {
        $model = $this->getModel();
        if (strtolower($_SERVER['REQUEST_METHOD']) != 'post') {
            $this->result['message'] = JText::_('Error! Wrong HTTP method!');
        } else {
            if (array_key_exists('update_pic', $_FILES) && $_FILES['update_pic']['error'] == 0) {
                $picture = $_FILES['update_pic'];
                $this->result = $model->html5UpdateUpload($picture, $this->objParams);
            } else {
                $this->result['message'] = JText::_('Something went wrong with your upload!');
            }
        }
        $model->obEndClear();
        echo json_encode($this->result);
        exit;
    }

    private function createDir($dir_name) {
        if (!is_dir($dir_name)) {
            mkdir($dir_name, 0777);
            chmod($dir_name, 0777);
        }
    }

    /**
     * Get the return URL.
     *
     * If a "return" variable has been passed in the request
     *
     * @return  string	The return URL.
     *
     * @since   1.6
     */
    protected function getReturnPage() {

        $return = JFactory::getApplication()->input->getString('return');
        $return = str_replace('||', '&', $return);
        if ($return && $return != '') {
            return $return;
        } else {
            return JUri::base();
        }
    }

}