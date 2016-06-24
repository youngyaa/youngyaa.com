<?php

/**
 * @package     com_bt_media - BT Media
 * @version	1.1.0
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

jimport('joomla.application.component.controllerform');
require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/images.php';

/**
 * Detail controller class.
 */
class Bt_mediaControllerDetail extends JControllerForm {

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

    public function multiAdd() {
        $this->setRedirect(JRoute::_('index.php?option=com_bt_media&view=detail&layout=add', false));
        return true;
    }

    public function setFeatured() {
        $item = $_POST['item'];
        $value = $_POST['value'];
        $model = $this->getModel();
        $this->result = $model->setFeatured($item, $value);
        $model->obEndClear();
        echo json_encode($this->result);
        exit;
    }

    public function deleteImage() {
        $model = $this->getModel();
        $file = $_POST['file_name'];
        $session_name = $_POST['session_name'];
        $this->result = $model->deleteImage($file, $session_name, $this->objParams);
        $model->obEndClear();
        echo json_encode($this->result);
        exit;
    }

    public function changeName() {
        $newName = $_POST['name'];
        $file = $_POST['file'];
        $session_name = $_POST['session_name'];
        $model = $this->getModel();
        $this->result = $model->changeName($session_name, $file, $newName);
        $model->obEndClear();
        echo json_encode($this->result);
        exit;
    }

    public function html5Upload() {
        $model = $this->getModel();
        if (strtolower($_SERVER['REQUEST_METHOD']) != 'post') {
            $this->result['message'] = JText::_('Error! Wrong HTTP method!');
        } else {
            if (array_key_exists('pic', $_FILES) && $_FILES['pic']['error'] == 0) {
                $objPic = $_FILES['pic'];
                $this->result = $model->html5Upload($objPic, $this->objParams);
            } else {
                $this->result['message'] = JText::_('Something went wrong with your upload!');
            }
        }
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

    public function editImageUpload() {
        $oldFile = '';
        $source_server = '';
        $model = $this->getModel();
        if (isset($_POST['filename'])) {
            $oldFile_data = $_POST['filename'];
            if ($oldFile_data) {
                $oldFile_p = explode("|", $oldFile_data);
                $oldFile = $oldFile_p[count($oldFile_p) - 1];
            }
        }
        if (isset($_POST['source_server'])) {
            $source_server = $_POST['source_server'];
        }
        $file = $_FILES['Filedata'];
        $this->result = $model->editImageUpload($file, $oldFile, $source_server, $this->objParams);
        $model->obEndClear();
        echo json_encode($this->result);
        exit;
    }

    public function uploadify() {
        $model = $this->getModel();
        if (!empty($_FILES)) {
            $objFileUpload = $_FILES['Filedata'];
            $this->result = $model->uploadify($objFileUpload, $this->objParams);
        } else {
            $this->result['message'] = JText::_('COM_BT_MEDIA_FILE_NOT_FILES_SELECT');
        }
        $model->obEndClear();
        echo json_encode($this->result);
        exit();
    }

    public function getImage() {
        $model = $this->getModel();
        $from = $_POST['from'];
        if (isset($_POST['act'])) {
            $action = $_POST['act'];
        }
        if ($from == 'jfolder') {
            if (isset($_POST['data'])) {
                $foldersData = $_POST['data'];
                $this->result = $model->jFolderGetFiles($foldersData);
            }

//get image and coppy to server
            if (isset($_POST['photoid'])) {
                $photoid = $_POST['photoid'];
                $this->result = $model->jFolderGetFile($photoid, $this->objParams);
            }
        }

        //Getimage from flickr
        if ($from == 'flickr') {
//Get all photo album
            if ($action == "getalbums") {
                $author = $_POST['username'];
                $this->result = $model->imageGetAlbums($from, $author, $this->objParams);
            }


//Get image in album
            if ($action == "getphotos") {
                $albumid = $_POST['album'];
                $this->result = $model->imageGetPhotos($from, $albumid, null, $this->objParams);
            }

//get image and coppy to server
            if ($action == "getphoto") {
                $photoid = $_REQUEST['photoid'];
                $this->result = $model->imageGetPhoto($from, $photoid, $this->objParams);
            }
        }

        /**
         * Ajax process data from Picasa webalbum
         */
        if ($from == 'picasa') {
            if ($action == "getalbums") {
                $author = $_POST['username'];
                $this->result = $model->imageGetAlbums($from, $author);
            }



//Get image in album
            if ($action == "getphotos") {
                $user_id = $_POST['username'];
                $album_id = $_POST['album'];
                $this->result = $model->imageGetPhotos($from, $album_id, $user_id);
            }


//get image and coppy to server
            if ($action == "getphoto") {
                $photoid = $_REQUEST['photoid'];
                $this->result = $model->imageGetPhoto($from, $photoid, $this->objParams);
//return $photo_url;
            }
        }

        $model->obEndClear();
        echo json_encode($this->result);
        exit;
    }

    public function getVideo() {
        $model = $this->getModel();
        $from = $_POST['from'];
        //get video from jFolder
        if ($from == 'jfolder') {
            $action = $_POST['act'];
            if ($action == "getvideos") {
                $foldersData = $_POST['data'];
                $this->result = $model->jFolderGetFiles($action, $foldersData);
            }

//get image and coppy to server
            if ($action == "getvideo") {
                $videoid = $_REQUEST['videoid'];
                $this->result = $model->jFolderGetFile($action, $videoid, $this->objParams);
            }
        }

        if ($from == 'youtube') {
            $method = $_POST['method'];
            if ($method == "getplaylists") {
                $username = $_POST['username'];
                $this->result = $model->videoGetPlaylists($from, $username);
            }

            //Getvideo from playlist
            if ($method == "playlist_video") {
                $url_decode = urldecode($_POST['data']);
                $url = rtrim($url_decode, "/");
                $this->result = $model->videoGetFromURL($from, $url);
            }
            if ($method == "getvideos") {
                $data = $_POST['data'];
                $this->result = $model->videoGetVideos($from, $data);
            }

            if ($method == 'getvideo') {
                $video_id = $_POST['videoid'];
                $this->result = Bt_mediaLegacyHelper::youtubeGetVideo($video_id, $this->objParams->params);
            }
        }


        //Get video from Vimeo
        if ($from == 'vimeo') {
            $method = $_POST['method'];
            if ($method == "getplaylists") {
                $username = $_POST['username'];
                $this->result = $model->videoGetPlaylists($from, $username);
            }

            //Getvideo from Album
            if ($method == "playlist_video") {
                $url = rtrim($_POST['data'], "/");
                $this->result = $model->videoGetFromURL($from, $url);
            }

            if ($method == "getvideos") {
                $data = $_POST['data'];

                $this->result = $model->videoGetVideos($from, $data);
            }

            if ($method == 'getvideo') {
                $video_id = $_POST['videoid'];
                $this->result = Bt_mediaLegacyHelper::vimeoGetVideo($video_id, $this->objParams->params);
            }
        }

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
        if (isset($_POST['source_server'])) {
            $source_server = $_POST['source_server'];
        }
        if (isset($_POST['media_type'])) {
            $media_type = $_POST['media_type'];
        }
        $model = $this->getModel();
        $this->result = $model->getDataUrl($url, $oldFile, $source_server, $media_type, $this->objParams);
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

    public function loadSubFolder() {
        jimport('joomla.filesystem.folder');
        $direct = urldecode($_POST['folder']);
        $image_folder = JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
        $model = $this->getModel();
        if ($direct) {
            $html = array();
            $files = JFolder::folders($image_folder . $direct);
            $html[] = '<li class="fview back-parent" onclick="loadParentFolder(this, \'' . urlencode($direct) . '\')"><img src="' . JURI::base() . 'components/com_bt_media/assets/images/folder.gif"/><br/><span style="display:none;" class="ajax-loading"><img src="' . JUri::root() . 'administrator/components/com_bt_media/assets/images/ajax-loader_2.gif"/></span><br/>' . JText::_('<- Back') . '<input type="hidden" value=""/></li>';
            if ($files) {
                foreach ($files as $file) {
                    $html[] = '<li class="fview" ondblclick="loadSubFolder(this, \'' . urlencode($direct . DIRECTORY_SEPARATOR . $file) . '\')" onclick="select(this, \'' . urlencode($direct . DIRECTORY_SEPARATOR . $file) . '\')"><img src="' . JURI::base() . 'components/com_bt_media/assets/images/folder.gif"/><br/><span style="display:none;" class="ajax-loading"><img src="' . JUri::root() . 'administrator/components/com_bt_media/assets/images/ajax-loader_2.gif"/></span><br/>' . $file . '<input type="hidden" value=""/></li>';
                }
            }
            $this->result['success'] = true;
            $this->result['message'] = JText::_('All folder had been get.');
            $this->result['data'] = implode($html);
        }
        $model->obEndClear();
        echo json_encode($this->result);
        exit;
    }

    public function loadParentFolder() {
        jimport('joomla.filesystem.folder');
        $image_folder = JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;

        $direct = dirname(urldecode($_POST['folder']));
        $model = $this->getModel();
        $html = array();
        if ($direct != '.') {
            $html[] = '<li class="fview back-parent" onclick="loadParentFolder(this, \'' . $direct . '\')"><img src="' . JURI::base() . 'components/com_bt_media/assets/images/folder.gif"/><br/><span style="display:none;" class="ajax-loading"><img src="' . JUri::root() . 'administrator/components/com_bt_media/assets/images/ajax-loader_2.gif"/></span><br/>' . JText::_('<- Back') . '</li>';
        }
        $files = JFolder::folders($image_folder . $direct);
        if ($files) {
            foreach ($files as $file) {
                if ($file != "bt_media") {
                    $html[] = '<li class="fview" ondblclick="loadSubFolder(this, \'' . urlencode($direct . DIRECTORY_SEPARATOR . $file) . '\')" onclick="select(this, \'' . urlencode($direct . DIRECTORY_SEPARATOR . $file) . '\')"><img src="' . JURI::base() . 'components/com_bt_media/assets/images/folder.gif"/><br/><span style="display:none;" class="ajax-loading"><img src="' . JUri::root() . 'administrator/components/com_bt_media/assets/images/ajax-loader_2.gif"/></span><br/>' . $file . '<input type="hidden" value=""/></li>';
                }
            }
        }
        $this->result['success'] = true;
        $this->result['message'] = JText::_('All folder had been get.');
        $this->result['data'] = implode($html);
        $model->obEndClear();
        echo json_encode($this->result);
        exit;
    }

}
