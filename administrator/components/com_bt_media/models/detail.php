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
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * Bt_media model.
 */
class Bt_mediaModeldetail extends JModelAdmin {

    /**
     * @var		string	The prefix to use with controller messages.
     * @since	1.6
     */
    protected $text_prefix = 'COM_BT_MEDIA';
    private $result = array('success' => false, 'message' => '', 'data' => null);

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param	type	The table type to instantiate
     * @param	string	A prefix for the table class name. Optional.
     * @param	array	Configuration array for model. Optional.
     * @return	JTable	A database object
     * @since	1.6
     */
    public function getTable($type = 'Detail', $prefix = 'Bt_mediaTable', $config = array()) {
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

    protected function canEditState($record) {
        $user = JFactory::getUser();

        // Check against the category.
        if (!empty($record->cate_id)) {
            return $user->authorise('core.edit.state', 'com_bt_media.category.' . (int) $record->cate_id);
        }
        // Default to component settings if category not known.
        else {
            return parent::canEditState($record);
        }
    }

    protected function canDelete($record) {
        $user = JFactory::getUser();
        $canDelete = FALSE;
        // Check against the category.
        if (!empty($record->id)) {
            if ($user->authorise('core.delete', 'com_bt_media.media.' . (int) $record->id)) {
                $canDelete = TRUE;
            } else {
                if ($user->authorise('media.delete.own', 'com_bt_media.media.' . (int) $record->id) && $user->id == (int) $record->created_by) {
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
        $data = JFactory::getApplication()->getUserState('com_bt_media.edit.detail.data', array());

        if (empty($data)) {
            $data = $this->getItem();

            //Support for 'multiple' field
            $data->cate_id = json_decode($data->cate_id);
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

    public function save($data) {
        $sessions = JFactory::getSession();
        $task = JFactory::getApplication()->input->get('task');
        $layout = JFactory::getApplication()->input->get('layout');
        $params = JComponentHelper::getParams('com_bt_media');
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/images.php';

        $saveDir = JPATH_SITE . DIRECTORY_SEPARATOR . $params->get('file_save', 'images/bt_media');

        $videoDir = $saveDir . '/videos';
        $original = $saveDir . '/images/original';
        $large = $saveDir . '/images/large';
        $thumbnail = $saveDir . '/images/thumbnail';
        $imageTemp = $saveDir . '/temp/images';
        $videoOriginal = $saveDir . '/videos';
//        if (isset($_FILES['images'])) {
//            die('abc');
//            $imageUploads = $_FILES['images'];
//        }
//        if (isset($_FILES['videos'])) {
//            die('video');
//            $videoUploads = $_FILES['videos'];
//        }
        if (isset($_FILES['fileuploads'])) {
            $fileUploads = $_FILES['fileuploads'];
        }

        if ($layout == 'add') {
            if ($task == 'apply' || $task == 'save' || $task == 'save2new') {
                $data['created_date'] = JFactory::getDate()->calendar('Y-m-d H:i:s');
                //multi add process
                if ($sessions->get('files_upload') || $sessions->get('images_jfolder') || $sessions->get('images_flickr') || $sessions->get('images_picasa') || $sessions->get('video_youtube') || $sessions->get('video_vimeo') || $sessions->get('videos_jfolder')) {
                    if ($sessions->get('images_jfolder') && $sessions->get('images_jfolder') != NULL) {
                        $images = $sessions->get('images_jfolder');
                        foreach ($images as $image) {
                            $data['id'] = 0;
                            $data['name'] = $image->name;
                            $data['image_path'] = $image->image_path;
                            $data['source_of_media'] = $image->source_of_media;
                            $data['media_type'] = $image->media_type;
                            $data['alias'] = Bt_mediaLegacyHelper::createAlias($data['name'], 'media', 0);
                            $this->setState($this->getName() . '.id', 0);
                            parent::save($data);
                        }
                        $sessions->clear('images_jfolder');
                    }
                    if ($sessions->get('images_flickr') && $sessions->get('images_flickr') != NULL) {
                        $images = $sessions->get('images_flickr');
                        foreach ($images as $image) {
                            $data['id'] = 0;
                            $data['name'] = $image->name;
                            $data['image_path'] = $image->image_path;
                            if ($image->video_path) {
                                $data['video_path'] = $image->video_path;
                            }
                            $data['source_of_media'] = $image->source_of_media;
                            $data['description'] = $image->description;
                            $data['media_type'] = $image->media_type;
                            $data['alias'] = Bt_mediaLegacyHelper::createAlias($data['name'], 'media', 0);
                            $this->setState($this->getName() . '.id', 0);
                            parent::save($data);
                        }
                        $sessions->clear('images_flickr');
                    }
                    if ($sessions->get('images_picasa') && $sessions->get('images_picasa') != NULL) {
                        $images = $sessions->get('images_picasa');
                        foreach ($images as $image) {
                            $data['id'] = 0;
                            $data['name'] = $image->name;
                            $data['image_path'] = $image->image_path;
                            $data['source_of_media'] = $image->source_of_media;
                            $data['media_type'] = $image->media_type;
                            $data['alias'] = Bt_mediaLegacyHelper::createAlias($data['name'], 'media', 0);
                            $this->setState($this->getName() . '.id', 0);
                            parent::save($data);
                        }
                        $sessions->clear('images_picasa');
                    }

                    //save video
                    if ($sessions->get('videos_jfolder') && $sessions->get('videos_jfolder') != NULL) {
                        $videos = $sessions->get('videos_jfolder');
                        foreach ($videos as $video) {
                            $data['id'] = 0;
                            $data['name'] = $video->name;
                            $data['image_path'] = $video->image_path;
                            $data['video_path'] = $video->video_path;
                            $data['source_of_media'] = $video->source_of_media;
                            $data['media_type'] = $video->media_type;
                            $data['alias'] = Bt_mediaLegacyHelper::createAlias($data['name'], 'media', 0);
                            $this->setState($this->getName() . '.id', 0);
                            parent::save($data);
                        }
                        $sessions->clear('videos_jfolder');
                    }
                    if ($sessions->get('files_upload') && $sessions->get('files_upload') != NULL) {
                        $videos = $sessions->get('files_upload');
                        foreach ($videos as $video) {
                            $data['id'] = 0;
                            $data['name'] = $video->name;
                            $data['image_path'] = $video->image_path;
                            $data['video_path'] = $video->video_path;
                            $data['source_of_media'] = $video->source_of_media;
                            $data['media_type'] = $video->media_type;
                            $data['alias'] = Bt_mediaLegacyHelper::createAlias($data['name'], 'media', 0);
                            $this->setState($this->getName() . '.id', 0);
                            parent::save($data);
                        }
                        $sessions->clear('files_upload');
                    }
                    if ($sessions->get('video_youtube') && $sessions->get('video_youtube') != NULL) {
                        $videos = $sessions->get('video_youtube');
                        foreach ($videos as $video) {
                            $data['id'] = 0;
                            $data['name'] = $video->name;
                            $data['image_path'] = $video->image_path;
                            $data['video_path'] = $video->video_path;
                            $data['source_of_media'] = $video->source_of_media;
                            $data['media_type'] = $video->media_type;
                            $data['description'] = $video->description;
                            $data['tags'] = $video->tags;
                            $data['alias'] = Bt_mediaLegacyHelper::createAlias($data['name'], 'media', 0);
                            parent::save($data);
                            $ins_item_id = $this->getState($this->getName() . '.id', 0);
                            Bt_mediaLegacyHelper::saveItemTags($ins_item_id, $data['tags']);
                            $this->setState($this->getName() . '.id', 0);
                        }
                        $sessions->clear('video_youtube');
                    }
                    if ($sessions->get('video_vimeo') && $sessions->get('video_vimeo') != NULL) {
                        $videos = $sessions->get('video_vimeo');
                        foreach ($videos as $video) {
                            $data['id'] = 0;
                            $data['name'] = $video->name;
                            $data['image_path'] = $video->image_path;
                            $data['video_path'] = $video->video_path;
                            $data['source_of_media'] = $video->source_of_media;
                            $data['media_type'] = $video->media_type;
                            $data['description'] = $video->description;
                            $data['tags'] = $video->tags;
                            $data['alias'] = Bt_mediaLegacyHelper::createAlias($data['name'], 'media', 0);
                            parent::save($data);
                            $ins_item_id = $this->getState($this->getName() . '.id', 0);
                            Bt_mediaLegacyHelper::saveItemTags($ins_item_id, $data['tags']);
                            $this->setState($this->getName() . '.id', 0);
                        }
                        $sessions->clear('video_vimeo');
                    }
                }
                if ($params->get('file_upload_type', 'flash') == 'basic') {
                    for ($i = 0; $i < sizeof($fileUploads["name"]); $i++) {
                        $allowedExtensions = array();
                        if (JFactory::getUser()->authorise('media.upload.image', 'com_bt_media')) {
                            $allowedExtensions[] = 'jpg';
                            $allowedExtensions[] = 'jpeg';
                            $allowedExtensions[] = 'png';
                            $allowedExtensions[] = 'gif';
                        }
                        if (JFactory::getUser()->authorise('media.upload.video', 'com_bt_media')) {
                            $allowedExtensions[] = 'flv';
                            $allowedExtensions[] = 'mp4';
                        }

                        if (empty($allowedExtensions)) {
                            $this->setError(JText::_('COM_BT_MEDIA_NOT_PERMISSION_FILE_UPLOAD'));
                            return false;
                        }
                        $file = $fileUploads['tmp_name'][$i];
                        if ($fileUploads["name"][$i] != "" && $fileUploads["error"][$i] == 0) {
                            $extension = Bt_mediaLegacyHelper::getFileExtension($fileUploads['name'][$i]);
                            if (in_array($extension, $allowedExtensions)) {
                                $basename = substr($fileUploads['name'][$i], 0, strrpos($fileUploads['name'][$i], '.'));
                                $hashedName = md5('upload-' . $fileUploads['name'][$i]);
                                if (Bt_mediaLegacyHelper::checkOS('mac')) {
                                    $filename = $hashedName . '-' . uniqid() . '-' . $i . '.' . $extension;
                                } else {
                                    $filename = $hashedName . '.' . $extension;
                                }
                                if ($extension != 'mp4' && $extension != 'flv') {
                                    if (!JFile::exists($original . '/' . $filename)) {
                                        if (!move_uploaded_file($file, "{$original}/{$filename}")) {
                                            $this->setError(JText::_('COM_BT_MEDIA_ERROR_COULD_NOT_SAVE'));
                                            return false;
                                        } else {
                                            Bt_mediaLegacyHelper::processImage($original . '/' . $filename, $large . '/' . $filename, $params->get('image_width', 700), $params->get('image_height', 450), $params->get('large_image_process_type', 'crop'), $params->get('image_quality', 100));
                                            Bt_mediaLegacyHelper::processImage($original . '/' . $filename, $thumbnail . '/' . $filename, $params->get('thumb_image_width', 200), $params->get('thumb_image_height', 130), $params->get('thumb_image_process_type', 'crop'), $params->get('image_quality', 100));
                                            $data['id'] = 0;
                                            $data['name'] = $basename;
                                            $data['image_path'] = $filename;
                                            $data['source_of_media'] = 'Upload from local';
                                            $data['alias'] = Bt_mediaLegacyHelper::createAlias($data['name'], 'media', 0);
                                            $data['media_type'] = 'image';
                                            $this->setState($this->getName() . '.id', 0);
                                            parent::save($data);
                                        }
                                    } else {
                                        if (!Bt_mediaLegacyHelper::checkFileOnDB($filename)) {
                                            if (!JFile::exists($large . '/' . $filename)) {
                                                Bt_mediaLegacyHelper::processImage($original . '/' . $filename, $large . '/' . $filename, $params->get('image_width', 700), $params->get('image_height', 450), $params->get('large_image_process_type', 'crop'), $params->get('image_quality', 100));
                                            }
                                            if (!JFile::exists($thumbnail . '/' . $filename)) {
                                                Bt_mediaLegacyHelper::processImage($original . '/' . $filename, $thumbnail . '/' . $filename, $params->get('thumb_image_width', 200), $params->get('thumb_image_height', 130), $params->get('thumb_image_process_type', 'crop'), $params->get('image_quality', 100));
                                            }

                                            $data['id'] = 0;
                                            $data['name'] = $basename;
                                            $data['image_path'] = $filename;
                                            $data['source_of_media'] = 'Upload from local';
                                            $data['alias'] = Bt_mediaLegacyHelper::createAlias($data['name'], 'media', 0);
                                            $data['media_type'] = 'image';
                                            $this->setState($this->getName() . '.id', 0);
                                            parent::save($data);
                                        }
                                    }
                                } else {
                                    $max_upload = (int) (ini_get('upload_max_filesize'));
                                    $max_post = (int) (ini_get('post_max_size'));
                                    $memory_limit = (int) (ini_get('memory_limit'));
                                    $upload_mb = min($max_upload, $max_post, $memory_limit);
                                    $size = $fileUploads['size'][$i] / (1024 * 1024);
                                    if ($size > $upload_mb) {
                                        $this->setError(JText::_('COM_BT_MEDIA_ERROR_FILE_MAXIMUM'));
                                        return FALSE;
                                    } else {
                                        if (!JFile::exists($videoOriginal . '/' . $filename)) {
                                            if (!move_uploaded_file($file, "{$videoOriginal}/{$filename}")) {
                                                $this->setError(JText::_('COM_BT_MEDIA_ERROR_COULD_NOT_SAVE'));
                                            } else {
                                                if (!JFile::exists($original . '/default_video_thumb.jpg')) {
                                                    JFile::copy(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'assets/images/default_video_thumb.jpg', $original . '/default_video_thumb.jpg');
                                                }
                                                Bt_mediaLegacyHelper::processImage($original . '/default_video_thumb.jpg', $thumbnail . "/" . $hashedName . '.jpg', $params->get('thumb_image_width', 200), $params->get('thumb_image_height', 130), $params->get('thumb_image_process_type', 'crop'), $params->get('image_quality', 100));
                                                $data['id'] = 0;
                                                $data['name'] = $basename;
                                                $data['video_path'] = $filename;
                                                $data['image_path'] = $hashedName . '.jpg';
                                                $data['source_of_media'] = 'Upload from local';
                                                $data['alias'] = Bt_mediaLegacyHelper::createAlias($data['name'], 'media', 0);
                                                $data['media_type'] = 'video';
                                                $this->setState($this->getName() . '.id', 0);
                                                parent::save($data);
                                            }
                                        } else {
                                            $file_check = $hashedName . '.jpg';
                                            if (!Bt_mediaLegacyHelper::checkFileOnDB($file_check)) {
                                                if (!JFile::exists($original . "/" . $hashedName . '.jpg')) {
                                                    if (!JFile::exists($original . '/default_video_thumb.jpg')) {
                                                        JFile::copy(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'assets/images/default_video_thumb.jpg', $original . '/default_video_thumb.jpg');
                                                    }
                                                    Bt_mediaLegacyHelper::processImage($original . '/default_video_thumb.jpg', $thumbnail . "/" . $hashedName . '.jpg', $params->get('thumb_image_width', 200), $params->get('thumb_image_height', 130), $params->get('thumb_image_process_type', 'crop'), $params->get('image_quality', 100));
                                                }
                                                $data['id'] = 0;
                                                $data['name'] = $basename;
                                                $data['video_path'] = $filename;
                                                $data['image_path'] = $hashedName . '.jpg';
                                                $data['source_of_media'] = 'Upload from local';
                                                $data['alias'] = Bt_mediaLegacyHelper::createAlias($data['name'], 'media', 0);
                                                $data['media_type'] = 'video';
                                                $this->setState($this->getName() . '.id', 0);
                                                parent::save($data);
                                            }
                                        }
                                    }
                                }
                            } else {
                                $this->setError(JText::_('COM_BT_MEDIA_FILE_EXTENSION_INVALID'));
                                return false;
                            }
                        }
                    }
                }
            }
        }

        //Process for single add and edit media item
        if ($layout == 'edit') {

            if ($params->get('file_upload_type', 'flash') == 'basic') {
                if ($_FILES['image']['name'] != '' && $_FILES['image']['error'] == 0) {
                    $oldFile = $data['image_path'];
                    $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif', 'flv', 'mp4');
                    $file = $_FILES['image']['tmp_name'];
                    $extension = Bt_mediaLegacyHelper::getFileExtension($_FILES['image']['name']);
                    if (in_array($extension, $allowedExtensions)) {
                        $basename = substr($_FILES['image']['name'], 0, strrpos($_FILES['image']['name'], '.'));
                        $hashedName = md5('upload-' . $_FILES['image']['name']);
                        $filename = "{$hashedName}.{$extension}";
                        if ($extension != 'flv' && $extension != 'mp4') {
                            if (!JFile::exists($imageTemp . '/' . $filename)) {
                                if (!move_uploaded_file($file, "{$imageTemp}/{$filename}")) {
                                    $this->setError(JText::_('COM_BT_MEDIA_ERROR_COULD_NOT_SAVE'));
                                    return FALSE;
                                } else {
                                    Bt_mediaLegacyHelper::processImage($imageTemp . '/' . $filename, $imageTemp . '/large_' . $filename, $params->get('image_width', 700), $params->get('image_height', 450), $params->get('large_image_process_type', 'crop'), $params->get('image_quality', 100));
                                    Bt_mediaLegacyHelper::processImage($imageTemp . '/' . $filename, $imageTemp . '/thumb_' . $filename, $params->get('thumb_image_width', 200), $params->get('thumb_image_height', 130), $params->get('thumb_image_process_type', 'crop'), $params->get('image_quality', 100));
                                    $data['image_path'] = $filename . '|' . $oldFile;
                                }
                            } else {
                                if (!JFile::exists($imageTemp . '/large_' . $filename)) {
                                    Bt_mediaLegacyHelper::processImage($imageTemp . '/' . $filename, $imageTemp . '/large_' . $filename, $params->get('image_width', 700), $params->get('image_height', 450), $params->get('large_image_process_type', 'crop'), $params->get('image_quality', 100));
                                }
                                if (!JFile::exists($imageTemp . '/thumb_' . $filename)) {
                                    Bt_mediaLegacyHelper::processImage($imageTemp . '/' . $filename, $imageTemp . '/thumb_' . $filename, $params->get('thumb_image_width', 200), $params->get('thumb_image_height', 130), $params->get('thumb_image_process_type', 'crop'), $params->get('image_quality', 100));
                                }
                                $data['image_path'] = $filename . '|' . $oldFile;
                            }
                        } else {
                            if (!JFile::exists($saveDir . '/temp/videos/' . $filename)) {
                                if (!move_uploaded_file($file, "{$saveDir}/temp/videos/{$filename}")) {
                                    $this->setError(JText::_('COM_BT_MEDIA_ERROR_COULD_NOT_SAVE'));
                                    return FALSE;
                                } else {
                                    if (!JFile::exists($imageTemp . '/default_video_thumb.jpg')) {
                                        JFile::copy(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'assets/images/default_video_thumb.jpg', $imageTemp . '/default_video_thumb.jpg');
                                    }
                                    list($width, $height) = getimagesize($imageTemp . '/default_video_thumb.jpg');
                                    Bt_mediaLegacyHelper::processImage($imageTemp . '/default_video_thumb.jpg', $imageTemp . '/' . $hashedName . '.jpg', $width, $height, $params->get('large_image_process_type', 'crop'), $params->get('image_quality', 100));
                                    Bt_mediaLegacyHelper::processImage($imageTemp . '/default_video_thumb.jpg', $imageTemp . '/large_' . $hashedName . '.jpg', $params->get('image_width', 700), $params->get('image_height', 450), $params->get('large_image_process_type', 'crop'), $params->get('image_quality', 100));
                                    Bt_mediaLegacyHelper::processImage($imageTemp . '/default_video_thumb.jpg', $imageTemp . '/thumb_' . $hashedName . '.jpg', $params->get('thumb_image_width', 200), $params->get('thumb_image_height', 130), $params->get('thumb_image_process_type', 'crop'), $params->get('image_quality', 100));
                                    $data['image_path'] = $filename . '|' . $oldFile;
                                }
                            } else {
                                if (!JFile::exists($imageTemp . '/default_video_thumb.jpg')) {
                                    JFile::copy(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'assets/images/default_video_thumb.jpg', $imageTemp . '/default_video_thumb.jpg');
                                }
                                if (!JFile::exists($imageTemp . '/large_' . $hashedName . '.jpg')) {
                                    Bt_mediaLegacyHelper::processImage($imageTemp . '/default_video_thumb.jpg', $imageTemp . '/large_' . $hashedName . '.jpg', $params->get('image_width', 700), $params->get('image_height', 450), $params->get('large_image_process_type', 'crop'), $params->get('image_quality', 100));
                                }
                                if (!JFile::exists($imageTemp . '/thumb_' . $hashedName . '.jpg')) {
                                    Bt_mediaLegacyHelper::processImage($imageTemp . '/default_video_thumb.jpg', $imageTemp . '/thumb_' . $hashedName . '.jpg', $params->get('thumb_image_width', 200), $params->get('thumb_image_height', 130), $params->get('thumb_image_process_type', 'crop'), $params->get('image_quality', 100));
                                }
                                $data['image_path'] = $filename . '|' . $oldFile;
                            }
                        }
                    } else {
                        $this->setError(JText::_('COM_BT_MEDIA_FILE_EXTENSION_INVALID'));
                        return FALSE;
                    }
                }
            }

            $filename = explode("|", $data['image_path']);
            if (isset($filename[1])) {
                $oldFile = $filename[1];
                $newFile = $filename[0];
                $extension = Bt_mediaLegacyHelper::getFileExtension($newFile);
                if ($oldFile != $newFile) {
                    if ($extension == "flv" || $extension == "mp4") {
                        if ($data['source_of_media'] == "Upload from local" || $data['source_of_media'] == "" || !$data['source_of_media']) {
                            if (JFile::exists($saveDir . '/temp/videos/' . $newFile)) {
                                if (JFile::move($saveDir . '/temp/videos/' . $newFile, $videoDir . '/' . $newFile)) {
                                    if (JFile::exists($videoDir . '/' . $oldFile)) {
                                        JFile::delete($videoDir . '/' . $oldFile);
                                    }
                                }
                            }
                            if (!$data['media_type'] || $data['media_type'] == '' || $data['media_type'] == 'image') {
                                $data['media_type'] = 'video';
                            }
                            $data['video_path'] = $newFile;
                        }
                        $imageFile = substr($newFile, 0, strpos($newFile, '.')) . '.jpg';
                    } else {
                        $imageFile = $newFile;
                    }
                    if (JFile::exists($imageTemp . '/' . $imageFile)) {
                        if (JFile::move($imageTemp . '/' . $imageFile, $original . '/' . $imageFile)) {
                            JFile::move($imageTemp . '/thumb_' . $imageFile, $thumbnail . '/' . $imageFile);
                            JFile::move($imageTemp . '/large_' . $imageFile, $large . '/' . $imageFile);
                            if (JFile::exists($original . '/' . $oldFile)) {
                                JFile::delete($original . '/' . $oldFile);
                            }
                            if (JFile::exists($thumbnail . '/' . $oldFile)) {
                                JFile::delete($thumbnail . '/' . $oldFile);
                            }
                            if (JFile::exists($large . '/' . $oldFile)) {
                                JFile::delete($large . '/' . $oldFile);
                            }
                        }
                    }
                    $data['image_path'] = $imageFile;
                } else {
                    unset($data['image_path']);
                }
            }


            if (empty($data['alias'])) {
                $data['alias'] = Bt_mediaLegacyHelper::createAlias($data['name'], 'media', $data['id']);
            } else {
                $data['alias'] = Bt_mediaLegacyHelper::createAlias($data['alias'], 'media', $data['id']);
            }
            if (empty($data['created_date'])) {
                $data['created_date'] = JFactory::getDate()->calendar('Y-m-d H:i:s', true, true);
            }
            if ($task == 'save2copy') {
                $data['alias'] = Bt_mediaLegacyHelper::createAlias($data['alias'], 'media', $data['id'], TRUE);
                $data['name'] = JString::increment($data['name']);
                $data['image_path'] = '';
            }
            if (!$data['media_type'] || $data['media_type'] == '') {
                $data['media_type'] = 'image';
            }
            if (!parent::save($data)) {
                return false;
            }
            if (isset($data['tags'])) {

                $ins_item_id = $this->getState('detail.id', 0);
                if ($ins_item_id === 0) {
                    $item_id = $data['id'];
                } else {
                    $item_id = $ins_item_id;
                }
                Bt_mediaLegacyHelper::saveItemTags($item_id, $data['tags']);
            }
        }
        return true;
    }

    function rebuild() {
        $params = JComponentHelper::getParams('com_bt_media');
        $building = JFactory::getApplication()->input->get('building');
        $session = JFactory::getSession();
        $db = JFactory::getDBO();
        if ($building) {
            $images = $session->get('rebuild-images');
            $output = $session->get('rebuild-output');
        } else {

            $db->setQuery('Select id, name, video_path,image_path, media_type, params from #__bt_media_items');
            $images = $db->loadObjectList();
            $output = '<h3>Rebuild thumbnail & large images of BT Media:</h3>';
            $output .= '<b>Total: ' . count($images) . ' images</b><hr />';
        }
        if (count($images)) {
            $image = array_pop($images);
            $session->set('rebuild-images', $images);
            $filename = $image->image_path;

            $path_image_thumb = JPATH_SITE . '/' . $params->get('file_save', 'images/bt_media') . '/images/thumbnail/';
            $path_image_large = JPATH_SITE . '/' . $params->get('file_save', 'images/bt_media') . '/images/large/';
            $path_image_orig = JPATH_SITE . '/' . $params->get('file_save', 'images/bt_media') . '/images/original/';
            if (!is_file($path_image_orig . $filename) && is_file($path_image_large . $filename)) {
                JFile::copy($path_image_large . $filename, $path_image_orig . $filename);
            }
            if (!is_file($path_image_orig . $filename)) {
                @unlink($path_image_large . $filename);
                @unlink($path_image_thumb . $filename);
                $query = 'DELETE FROM #__bt_media_items WHERE id =' . $image->id;
                $db->setQuery($query);
                $db->query();
                $output .= $image->name . ':<i> ' . $image->name . '</i><font style="color:red"> - Image not found! Removed!</font><br />';
            } else {
                Bt_mediaLegacyHelper::processImage($path_image_orig . $filename, $path_image_large . $filename, $params->get('image_width', 700), $params->get('image_height', 450), $params->get('large_image_process_type', 'crop'), $params->get('image_quality', 100));
                Bt_mediaLegacyHelper::processImage($path_image_orig . $filename, $path_image_thumb . $filename, $params->get('thumb_image_width', 200), $params->get('thumb_image_height', 130), $params->get('thumb_image_process_type', 'crop'), $params->get('image_quality', 100));

                $output .= '' . $image->name . ':<i> ' . $filename . '</i><br />';
            }
            echo '<html><head><title>Rebuilding images...</title><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/><meta HTTP-EQUIV="REFRESH" content="0; url=index.php?option=com_bt_media&task=list.rebuild&tmpl=component&building=true;"></head>';
            echo '<body>' . $output . '...</body></html>';
            $session->set('rebuild-output', $output);
            exit;
        } else {
            return $output;
        }
    }

    //Delete items selected, delete image of earch item, delete tags of item on tag_xref table
    public function delete(&$pks) {
        $params = JComponentHelper::getParams('com_bt_media');
        foreach ($pks as $pk) {
            $item = $this->getItem($pk);
            Bt_mediaLegacyHelper::clearTagsItem($pk);
            $this->deleteFile(JPATH_SITE . DIRECTORY_SEPARATOR . $params->get('file_save', 'images/bt_media') . '/images/large/' . $item->image_path);
            $this->deleteFile(JPATH_SITE . DIRECTORY_SEPARATOR . $params->get('file_save', 'images/bt_media') . '/images/thumbnail/' . $item->image_path);
            $this->deleteFile(JPATH_SITE . DIRECTORY_SEPARATOR . $params->get('file_save', 'images/bt_media') . '/images/original/' . $item->image_path);
            if ($item->media_type == 'video' && $item->source_of_media == 'Upload from local') {
                $this->deleteFile(JPATH_SITE . DIRECTORY_SEPARATOR . $params->get('file_save', 'images/bt_media') . '/videos/' . $item->video_path);
            }
        }
        if (parent::delete($pks))
            return TRUE;
        else
            return FALSE;
    }

    private function deleteFile($file_location) {
        if (file_exists($file_location)) {
            if (!unlink($file_location))
                return FALSE;
        }
        return TRUE;
    }

    public function setFeatured($item, $value) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->update('#__bt_media_items');
        $query->set('featured=' . (int) $value);
        $query->where('id=' . (int) $item);
        $db->setQuery($query);
        $rs = $db->execute();
        if ($rs) {
            $this->result['success'] = true;
            $this->result['message'] = JText::_('Success');
        }
        return $this->result;
    }

    public function deleteImage($file, $session, $params) {
        if ($this->deleteFileInSession($session, $file, $params)) {
            $this->result['success'] = TRUE;
            $this->result['message'] = JText::_('File delete');
        } else {
            $this->result['message'] = JText::_('File not delete');
        }
        return $this->result;
    }

    public function changeName($session_name, $file, $newName) {
        if ($this->changeFileName($session_name, $file, $newName)) {
            $this->result['success'] = TRUE;
            $this->result['message'] = JText::_('File name change success');
        } else {
            $this->result['message'] = JText::_('File name not change');
            $this->result['data'] = array($session_name, $file, $newName);
        }
        return$this->result;
    }

    public function html5Upload($objPic, $params) {
        $demo_mode = false;
        $allowedExtensions = array();
        if (JFactory::getUser()->authorise('media.upload.image', 'com_bt_media')) {
            $allowedExtensions[] = 'jpg';
            $allowedExtensions[] = 'jpeg';
            $allowedExtensions[] = 'png';
            $allowedExtensions[] = 'gif';
        }
        if (JFactory::getUser()->authorise('media.upload.video', 'com_bt_media')) {
            $allowedExtensions[] = 'flv';
            $allowedExtensions[] = 'mp4';
        }

        if (empty($allowedExtensions)) {
            $this->result['message'] = JText::_('COM_BT_MEDIA_NOT_PERMISSION_FILE_UPLOAD');
            return $this->result;
        }

        $objFile = new stdClass();
        $extension_p = explode('.', $objPic['name']);
        $extension = strtolower($extension_p[count($extension_p) - 1]);

        if (!in_array($extension, $allowedExtensions)) {
            $this->result['message'] = JText::_('Only ' . implode(',', $allowedExtensions) . ' files are allowed!');
        } elseif ($demo_mode) {

            $line = implode('		', array(date('r'), $_SERVER['REMOTE_ADDR'], $objPic['size'], $objPic['name']));
            file_put_contents('log.txt', $line . PHP_EOL, FILE_APPEND);

            $this->result['message'] = JText::_('Uploads are ignored in demo mode.');
        } else {
            $basename = substr($objPic['name'], 0, strrpos($objPic['name'], '.'));
            $hashedName = md5('upload-' . $objPic['name']);
            $filename = "{$hashedName}.{$extension}";

            if ($extension == 'flv' || $extension == 'mp4') {
                if (!JFile::exists($params->videoOriginalSaveDir . '/' . $filename)) {
                    if (!move_uploaded_file($objPic['tmp_name'], "{$params->videoOriginalSaveDir}/{$filename}")) {
                        $this->result['message'] = JText::_('COM_BT_MEDIA_ERROR_COULD_NOT_SAVE');
                    } else {
                        if (!JFile::exists($params->originalSaveDir . '/default_video_thumb.jpg')) {
                            JFile::copy(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'assets/images/default_video_thumb.jpg', $params->originalSaveDir . '/default_video_thumb.jpg');
                        }
                        list($width, $height) = getimagesize($params->originalSaveDir . '/default_video_thumb.jpg');
                        Bt_mediaLegacyHelper::processImage($params->originalSaveDir . '/default_video_thumb.jpg', $params->largeSaveDir . "/" . $hashedName . '.jpg', $width, $height, $params->params->get('large_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                        Bt_mediaLegacyHelper::processImage($params->originalSaveDir . '/default_video_thumb.jpg', $params->thumbSaveDir . "/" . $hashedName . '.jpg', $params->thumbWidth, $params->thumbHeight, $params->params->get('thumb_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                        $objFile->name = $basename;
                        $objFile->video_path = $filename;
                        $objFile->image_path = $hashedName . '.jpg';
                        $objFile->source_of_media = 'Upload from local';
                        $objFile->media_type = 'video';

                        $rs_html = Bt_mediaLegacyHelper::createHTML($hashedName, $objFile->image_path, $objFile->name, 'files_upload', $params->params);

                        $this->result["success"] = true;
                        $this->result["message"] = JText::_('File upload success!');
                        $this->result["data"] = $rs_html;
                        Bt_mediaLegacyHelper::sessionStore('files_upload', $objFile);
                    }
                } else {
                    $file_check = $hashedName . '.jpg';
                    if (Bt_mediaLegacyHelper::checkFileOnDB($file_check) || Bt_mediaLegacyHelper::checkFileInSession('files_upload', $file_check)) {
                        $this->result['message'] = JText::_('COM_BT_MEDIA_FILE_EXIST');
                    } else {
                        if (!JFile::exists($params->originalSaveDir . "/" . $hashedName . '.jpg')) {
                            if (!JFile::exists($params->originalSaveDir . '/default_video_thumb.jpg')) {
                                JFile::copy(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'assets/images/default_video_thumb.jpg', $params->originalSaveDir . '/default_video_thumb.jpg');
                            }
                            list($width, $height) = getimagesize($params->originalSaveDir . '/default_video_thumb.jpg');
                            Bt_mediaLegacyHelper::processImage($params->originalSaveDir . '/default_video_thumb.jpg', $params->largeSaveDir . "/" . $hashedName . '.jpg', $width, $height, $params->params->get('large_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                            Bt_mediaLegacyHelper::processImage($params->originalSaveDir . '/default_video_thumb.jpg', $params->thumbSaveDir . "/" . $hashedName . '.jpg', $params->thumbWidth, $params->thumbHeight, $params->params->get('thumb_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                        }
                        $objFile->name = $basename;
                        $objFile->video_path = $filename;
                        $objFile->image_path = $hashedName . '.jpg';
                        $objFile->source_of_media = 'Upload from local';
                        $objFile->media_type = 'video';

                        $rs_html = Bt_mediaLegacyHelper::createHTML($hashedName, $objFile->image_path, $objFile->name, 'files_upload', $params->params);

                        $this->result['success'] = TRUE;
                        $this->result['data'] = $rs_html;
                        Bt_mediaLegacyHelper::sessionStore('files_upload', $objFile);
                    }
                }
            } else {

                if (!JFile::exists($params->originalSaveDir . '/' . $filename)) {
                    if (!move_uploaded_file($objPic['tmp_name'], "{$params->originalSaveDir}/{$filename}")) {
                        $this->result['message'] = JText::_('COM_BT_MEDIA_ERROR_COULD_NOT_SAVE');
                    } else {
                        Bt_mediaLegacyHelper::processImage($params->originalSaveDir . '/' . $filename, $params->largeSaveDir . '/' . $filename, $params->params->get('image_width', 700), $params->params->get('image_height', 450), $params->params->get('large_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                        Bt_mediaLegacyHelper::processImage($params->originalSaveDir . '/' . $filename, $params->thumbSaveDir . '/' . $filename, $params->thumbWidth, $params->thumbHeight, $params->params->get('thumb_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                        $objFile->name = $basename;
                        $objFile->image_path = $filename;
                        $objFile->source_of_media = 'Upload from local';
                        $objFile->media_type = 'image';

                        $rs_html = Bt_mediaLegacyHelper::createHTML($hashedName, $objFile->image_path, $objFile->name, 'files_upload', $params->params);

                        $this->result['success'] = TRUE;
                        $this->result['message'] = JText::_('File was uploaded successfuly!');
                        $this->result['data'] = $rs_html;
                        Bt_mediaLegacyHelper::sessionStore('files_upload', $objFile);
                    }
                } else {
                    if (Bt_mediaLegacyHelper::checkFileOnDB($filename) || Bt_mediaLegacyHelper::checkFileInSession('files_upload', $filename)) {
                        $this->result['message'] = JText::_('COM_BT_MEDIA_FILE_EXIST');
                    } else {

                        if (!JFile::exists($params->largeSaveDir . '/' . $filename)) {
                            Bt_mediaLegacyHelper::processImage($params->originalSaveDir . '/' . $filename, $params->largeSaveDir . '/' . $filename, $params->params->get('image_width', 700), $params->params->get('image_height', 450), $params->params->get('large_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                        }
                        if (!JFile::exists($params->thumbSaveDir . '/' . $filename)) {
                            Bt_mediaLegacyHelper::processImage($params->originalSaveDir . '/' . $filename, $params->thumbSaveDir . '/' . $filename, $params->thumbWidth, $params->thumbHeight, $params->params->get('thumb_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                        }
                        $objFile->name = $basename;
                        $objFile->image_path = $filename;
                        $objFile->source_of_media = 'Upload from local';
                        $objFile->media_type = 'image';

                        $rs_html = Bt_mediaLegacyHelper::createHTML($hashedName, $objFile->image_path, $objFile->name, 'files_upload', $params->params);

                        $this->result['success'] = TRUE;
                        $this->result['data'] = $rs_html;
                        Bt_mediaLegacyHelper::sessionStore('files_upload', $objFile);
                    }
                }
            }
        }

        return $this->result;
    }


    public function html5UpdateUpload($picture, $params) {

        $demo_mode = false;
        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif', 'flv', 'mp4');

        $extension_p = explode('.', $picture['name']);
        $extension = strtolower($extension_p[count($extension_p) - 1]);

        if (!in_array($extension, $allowedExtensions)) {
            $this->result['message'] = JText::_('Only ' . implode(',', $allowedExtensions) . ' files are allowed!');
        } elseif ($demo_mode) {

            $line = implode('		', array(date('r'), $_SERVER['REMOTE_ADDR'], $picture['size'], $picture['name']));
            file_put_contents('log.txt', $line . PHP_EOL, FILE_APPEND);

            $this->result['message'] = JText::_('Uploads are ignored in demo mode.');
        } else {
            $oldFile = '';
            if (isset($_POST['filename'])) {
                $oldFile_data = $_POST['filename'];
                if ($oldFile_data) {
                    $oldFile_p = explode("/", $oldFile_data);
                    $oldFile = $oldFile_p[count($oldFile_p) - 1];
                }
            }

            $hashedName = md5('upload-' . $picture['name']);
            $filename = $hashedName . '.' . $extension;
            if ($extension != 'flv' && $extension != 'mp4') {
                if (!JFile::exists($params->imageTempSaveDir . '/' . $filename)) {
                    if (!move_uploaded_file($picture['tmp_name'], "{$params->imageTempSaveDir}/{$filename}")) {
                        $this->result['message'] = JText::_('COM_BT_MEDIA_ERROR_COULD_NOT_SAVE');
                    } else {
                        Bt_mediaLegacyHelper::processImage($params->imageTempSaveDir . '/' . $filename, $params->imageTempSaveDir . '/large_' . $filename, $params->params->get('image_width', 700), $params->params->get('image_height', 450), $params->params->get('large_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                        Bt_mediaLegacyHelper::processImage($params->imageTempSaveDir . '/' . $filename, $params->imageTempSaveDir . '/thumb_' . $filename, $params->thumbWidth, $params->thumbHeight, $params->params->get('thumb_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                        $file_path = array();
                        $file_path[] = $filename . '|' . $oldFile;
                        $file_path[] = '<img src="' . JUri::root() . $params->params->get('file_save', 'images/bt_media') . '/temp/images/large_' . $filename . '"/>';
                        $file_path[] = 'Upload from local';
                        $this->result['success'] = TRUE;
                        $this->result['data'] = $file_path;
                    }
                } else {
                    if (!JFile::exists($params->imageTempSaveDir . '/large_' . $filename)) {
                        Bt_mediaLegacyHelper::processImage($params->imageTempSaveDir . '/' . $filename, $params->imageTempSaveDir . '/large_' . $filename, $params->params->get('image_width', 700), $params->params->get('image_height', 450), $params->params->get('large_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                    }
                    if (!JFile::exists($params->imageTempSaveDir . '/thumb_' . $filename)) {
                        Bt_mediaLegacyHelper::processImage($params->imageTempSaveDir . '/' . $filename, $params->imageTempSaveDir . '/thumb_' . $filename, $params->thumbWidth, $params->thumbHeight, $params->params->get('thumb_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                    }
                    $file_path = array();
                    $file_path[] = $filename . '|' . $oldFile;
                    $file_path[] = '<img src="' . JUri::root() . $params->params->get('file_save', 'images/bt_media') . '/temp/images/large_' . $filename . '"/>';
                    $file_path[] = 'Upload from local';
                    $this->result['success'] = TRUE;
                    $this->result['data'] = $file_path;
                }
            } else {
                if (!JFile::exists($params->videoTempSaveDir . '/' . $filename)) {
                    if (!move_uploaded_file($picture['tmp_name'], "{$params->videoTempSaveDir}/{$filename}")) {
                        $this->result['message'] = JText::_('COM_BT_MEDIA_ERROR_COULD_NOT_SAVE');
                    } else {
                        if (!JFile::exists($params->imageTempSaveDir . '/default_video_thumb.jpg')) {
                            JFile::copy(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'assets/images/default_video_thumb.jpg', $params->imageTempSaveDir . '/default_video_thumb.jpg');
                        }
                        list($width, $height) = getimagesize($params->imageTempSaveDir . '/default_video_thumb.jpg');
                        Bt_mediaLegacyHelper::processImage($params->imageTempSaveDir . '/default_video_thumb.jpg', $params->imageTempSaveDir . '/' . $hashedName . '.jpg', $width, $height, $params->params->get('large_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                        Bt_mediaLegacyHelper::processImage($params->imageTempSaveDir . '/default_video_thumb.jpg', $params->imageTempSaveDir . '/large_' . $hashedName . '.jpg', $params->params->get('image_width', 700), $params->params->get('image_height', 450), $params->params->get('large_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                        Bt_mediaLegacyHelper::processImage($params->imageTempSaveDir . '/default_video_thumb.jpg', $params->imageTempSaveDir . '/thumb_' . $hashedName . '.jpg', $params->thumbWidth, $params->thumbHeight, $params->params->get('thumb_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                        $file_path = array();
                        $file_path[] = $filename . '|' . $oldFile;
                        $file_path[] = '<img src="' . JUri::root() . $params->params->get('file_save', 'images/bt_media') . '/temp/images/large_' . $hashedName . '.jpg"/>';
                        $file_path[] = 'Upload from local';
                        $this->result['success'] = TRUE;
                        $this->result['data'] = $file_path;
                    }
                } else {
                    if (!JFile::exists($params->imageTempSaveDir . '/default_video_thumb.jpg')) {
                        JFile::copy(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'assets/images/default_video_thumb.jpg', $params->imageTempSaveDir . '/default_video_thumb.jpg');
                    }
                    if (!JFile::exists($params->imageTempSaveDir . '/large_' . $hashedName . '.jpg')) {
                        Bt_mediaLegacyHelper::processImage($params->imageTempSaveDir . '/default_video_thumb.jpg', $params->imageTempSaveDir . '/large_' . $hashedName . '.jpg', $params->params->get('image_width', 700), $params->params->get('image_height', 450), $params->params->get('large_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                    }
                    if (!JFile::exists($params->imageTempSaveDir . '/thumb_' . $hashedName . '.jpg')) {
                        Bt_mediaLegacyHelper::processImage($params->imageTempSaveDir . '/default_video_thumb.jpg', $params->imageTempSaveDir . '/thumb_' . $hashedName . '.jpg', $params->thumbWidth, $params->thumbHeight, $params->params->get('thumb_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                    }
                    $file_path = array();
                    $file_path[] = $filename . '|' . $oldFile;
                    $file_path[] = '<img src="' . JUri::root() . $params->params->get('file_save', 'images/bt_media') . '/temp/images/large_' . $hashedName . '.jpg"/>';
                    $file_path[] = 'Upload from local';
                    $this->result['success'] = TRUE;
                    $this->result['data'] = $file_path;
                }
            }
        }
        return $this->result;
    }

    public function editImageUpload($objFile, $oldFile, $source_server, $params) {
        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif', 'flv', 'mp4');
        $extension = Bt_mediaLegacyHelper::getFileExtension($objFile['name']);
        if (in_array($extension, $allowedExtensions)) {
            $hashedName = md5('upload-' . $objFile['name']);
            $filename = $hashedName . '.' . $extension;
            if ($filename == $oldFile) {
                $this->result['message'] = '
                    <div class="alert alert-error">
                        <h4 class="alert-heading">Message</h4>
                        <p>' . JText::_('COM_BT_MEDIA_FILE_EXIST') . '</p>
                      </div>';

                return $this->result;
            }
            if ($extension != 'flv' && $extension != 'mp4') {

                if (!JFile::exists($params->imageTempSaveDir . '/' . $filename)) {
                    if (!move_uploaded_file($objFile['tmp_name'], "{$params->imageTempSaveDir}/{$filename}")) {
                        $this->result['message'] = JText::_('COM_BT_MEDIA_ERROR_COULD_NOT_SAVE');
                    } else {
                        Bt_mediaLegacyHelper::processImage($params->imageTempSaveDir . '/' . $filename, $params->imageTempSaveDir . '/large_' . $filename, $params->params->get('image_width', 700), $params->params->get('image_height', 450), $params->params->get('large_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                        Bt_mediaLegacyHelper::processImage($params->imageTempSaveDir . '/' . $filename, $params->imageTempSaveDir . '/thumb_' . $filename, $params->thumbWidth, $params->thumbHeight, $params->params->get('thumb_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                        $file_path = array();
                        $file_path[] = $filename . '|' . $oldFile;
                        $file_path[] = '<img src="' . JUri::root() . $params->params->get('file_save', 'images/bt_media') . '/temp/images/large_' . $filename . '"/>';
                        if ($source_server) {
                            $file_path[] = $source_server;
                        } else {
                            $file_path[] = 'Upload from local';
                        }
                        $this->result['success'] = TRUE;
                        $this->result['data'] = $file_path;
                    }
                } else {
                    if (!JFile::exists($params->imageTempSaveDir . '/large_' . $filename)) {
                        Bt_mediaLegacyHelper::processImage($params->imageTempSaveDir . '/' . $filename, $params->imageTempSaveDir . '/large_' . $filename, $params->params->get('image_width', 700), $params->params->get('image_height', 450), $params->params->get('large_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                    }
                    if (!JFile::exists($params->imageTempSaveDir . '/thumb_' . $filename)) {
                        Bt_mediaLegacyHelper::processImage($params->imageTempSaveDir . '/' . $filename, $params->imageTempSaveDir . '/thumb_' . $filename, $params->thumbWidth, $params->thumbHeight, $params->params->get('thumb_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                    }
                    $file_path = array();
                    $file_path[] = $filename . '|' . $oldFile;
                    $file_path[] = '<img src="' . JUri::root() . $params->params->get('file_save', 'images/bt_media') . '/temp/images/large_' . $filename . '"/>';
                    if ($source_server) {
                        $file_path[] = $source_server;
                    } else {
                        $file_path[] = 'Upload from local';
                    }
                    $this->result['success'] = TRUE;
                    $this->result['data'] = $file_path;
                }
            } else {
                if (!JFile::exists($params->videoTempSaveDir . '/' . $filename)) {
                    if (!move_uploaded_file($objFile['tmp_name'], "{$params->videoTempSaveDir}/{$filename}")) {
                        $this->result['message'] = JText::_('COM_BT_MEDIA_ERROR_COULD_NOT_SAVE');
                    } else {
                        if (!JFile::exists($params->imageTempSaveDir . '/default_video_thumb.jpg')) {
                            JFile::copy(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'assets/images/default_video_thumb.jpg', $params->imageTempSaveDir . '/default_video_thumb.jpg');
                        }
                        list($width, $height) = getimagesize($params->imageTempSaveDir . '/default_video_thumb.jpg');
                        Bt_mediaLegacyHelper::processImage($params->imageTempSaveDir . '/default_video_thumb.jpg', $params->imageTempSaveDir . '/' . $hashedName . '.jpg', $width, $height, $params->params->get('large_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                        Bt_mediaLegacyHelper::processImage($params->imageTempSaveDir . '/default_video_thumb.jpg', $params->imageTempSaveDir . '/large_' . $hashedName . '.jpg', $params->params->get('image_width', 700), $params->params->get('image_height', 450), $params->params->get('large_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                        Bt_mediaLegacyHelper::processImage($params->imageTempSaveDir . '/default_video_thumb.jpg', $params->imageTempSaveDir . '/thumb_' . $hashedName . '.jpg', $params->thumbWidth, $params->thumbHeight, $params->params->get('thumb_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                        $file_path = array();
                        $file_path[] = $filename . '|' . $oldFile;
                        $file_path[] = '<img src="' . JUri::root() . $params->params->get('file_save', 'images/bt_media') . '/temp/images/large_' . $hashedName . '.jpg"/>';
                        $file_path[] = 'Upload from local';
                        $this->result['success'] = TRUE;
                        $this->result['data'] = $file_path;
                    }
                } else {
                    if (!JFile::exists($params->imageTempSaveDir . '/default_video_thumb.jpg')) {
                        JFile::copy(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'assets/images/default_video_thumb.jpg', $params->imageTempSaveDir . '/default_video_thumb.jpg');
                    }
                    if (!JFile::exists($params->imageTempSaveDir . '/large_' . $hashedName . '.jpg')) {
                        Bt_mediaLegacyHelper::processImage($params->imageTempSaveDir . '/default_video_thumb.jpg', $params->imageTempSaveDir . '/large_' . $hashedName . '.jpg', $params->params->get('image_width', 700), $params->params->get('image_height', 450), $params->params->get('large_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                    }
                    if (!JFile::exists($params->imageTempSaveDir . '/thumb_' . $hashedName . '.jpg')) {
                        Bt_mediaLegacyHelper::processImage($params->imageTempSaveDir . '/default_video_thumb.jpg', $params->imageTempSaveDir . '/thumb_' . $hashedName . '.jpg', $params->thumbWidth, $params->thumbHeight, $params->params->get('thumb_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                    }
                    $file_path = array();
                    $file_path[] = $filename . '|' . $oldFile;
                    $file_path[] = '<img src="' . JUri::root() . $params->params->get('file_save', 'images/bt_media') . '/temp/images/large_' . $hashedName . '.jpg"/>';
                    $file_path[] = 'Upload from local';
                    $this->result['success'] = TRUE;
                    $this->result['data'] = $file_path;
                }
            }
        } else {
            $this->result['message'] = JText::_('COM_BT_MEDIA_FILE_EXTENSION_INVALID');
        }
        return $this->result;
    }

    public function uploadify($objFileUpload, $params) {
        $file = $objFileUpload['tmp_name'];
        $extension = Bt_mediaLegacyHelper::getFileExtension($objFileUpload['name']);

        $allowedExtensions = array();
        if (JFactory::getUser()->authorise('media.upload.image', 'com_bt_media')) {
            $allowedExtensions[] = 'jpg';
            $allowedExtensions[] = 'jpeg';
            $allowedExtensions[] = 'png';
            $allowedExtensions[] = 'gif';
        }
        if (JFactory::getUser()->authorise('media.upload.video', 'com_bt_media')) {
            $allowedExtensions[] = 'flv';
            $allowedExtensions[] = 'mp4';
        }

        if (empty($allowedExtensions)) {
            $this->result['message'] = JText::_('COM_BT_MEDIA_NOT_PERMISSION_FILE_UPLOAD');
            return $this->result;
        }

        $objFile = new stdClass();
        if (in_array($extension, $allowedExtensions)) {

            $basename = substr($objFileUpload['name'], 0, strrpos($objFileUpload['name'], '.'));
            $hashedName = md5('upload-' . $objFileUpload['name']);
            $filename = $hashedName . '.' . $extension;
            if ($extension == 'flv' || $extension == 'mp4') {
                $max_upload = (int) (ini_get('upload_max_filesize'));
                $max_post = (int) (ini_get('post_max_size'));
                $memory_limit = (int) (ini_get('memory_limit'));
                $upload_mb = min($max_upload, $max_post, $memory_limit);
                $size = $objFileUpload['size'] / (1024 * 1024);
                if ($size > $upload_mb) {
                    $this->result['message'] = JText::_('COM_BT_MEDIA_ERROR_FILE_MAXIMUM');
                } else {
                    if (!JFile::exists($params->videoOriginalSaveDir . '/' . $filename)) {
                        if (!move_uploaded_file($file, "{$params->videoOriginalSaveDir}/{$filename}")) {
                            $this->result['message'] = JText::_('COM_BT_MEDIA_ERROR_COULD_NOT_SAVE');
                        } else {
                            if (!JFile::exists($params->originalSaveDir . '/default_video_thumb.jpg')) {
                                JFile::copy(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'assets/images/default_video_thumb.jpg', $params->originalSaveDir . '/default_video_thumb.jpg');
                            }
                            list($width, $height) = getimagesize($params->originalSaveDir . '/default_video_thumb.jpg');
                            Bt_mediaLegacyHelper::processImage($params->originalSaveDir . '/default_video_thumb.jpg', $params->largeSaveDir . "/" . $hashedName . '.jpg', $width, $height, $params->params->get('large_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                            Bt_mediaLegacyHelper::processImage($params->originalSaveDir . '/default_video_thumb.jpg', $params->thumbSaveDir . "/" . $hashedName . '.jpg', $params->thumbWidth, $params->thumbHeight, $params->params->get('thumb_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                            $objFile->name = $basename;
                            $objFile->video_path = $filename;
                            $objFile->image_path = $hashedName . '.jpg';
                            $objFile->source_of_media = 'Upload from local';
                            $objFile->media_type = 'video';

                            $rs_html = Bt_mediaLegacyHelper::createHTML($hashedName, $objFile->image_path, $objFile->name, 'videos_upload', $params->params);

                            $this->result["success"] = true;
                            $this->result["message"] = JText::_('File upload success!');
                            $this->result["data"] = $rs_html;
                            Bt_mediaLegacyHelper::sessionStore('files_upload', $objFile);
                        }
                    } else {
                        $file_check = $hashedName . '.jpg';
                        if (Bt_mediaLegacyHelper::checkFileOnDB($file_check) || Bt_mediaLegacyHelper::checkFileInSession('videos_upload', $file_check)) {
                            $this->result['message'] = JText::_('COM_BT_MEDIA_FILE_EXIST');
                        } else {
                            if (!JFile::exists($params->originalSaveDir . "/" . $hashedName . '.jpg')) {
                                if (!JFile::exists($params->originalSaveDir . '/default_video_thumb.jpg')) {
                                    JFile::copy(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'assets/images/default_video_thumb.jpg', $params->originalSaveDir . '/default_video_thumb.jpg');
                                }
                                list($width, $height) = getimagesize($params->originalSaveDir . '/default_video_thumb.jpg');
                                Bt_mediaLegacyHelper::processImage($params->originalSaveDir . '/default_video_thumb.jpg', $params->largeSaveDir . "/" . $hashedName . '.jpg', $width, $height, $params->params->get('large_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                                Bt_mediaLegacyHelper::processImage($params->originalSaveDir . '/default_video_thumb.jpg', $params->thumbSaveDir . "/" . $hashedName . '.jpg', $params->thumbWidth, $params->thumbHeight, $params->params->get('thumb_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                            }
                            $objFile->name = $basename;
                            $objFile->video_path = $filename;
                            $objFile->image_path = $hashedName . '.jpg';
                            $objFile->source_of_media = 'Upload from local';
                            $objFile->media_type = 'video';

                            $rs_html = Bt_mediaLegacyHelper::createHTML($hashedName, $objFile->image_path, $objFile->name, 'videos_upload', $params->params);

                            $this->result['success'] = TRUE;
                            $this->result['data'] = $rs_html;
                            Bt_mediaLegacyHelper::sessionStore('files_upload', $objFile);
                        }
                    }
                }
            } else {
                if (!JFile::exists($params->originalSaveDir . '/' . $filename)) {
                    if (!move_uploaded_file($file, "{$params->originalSaveDir}/{$filename}")) {
                        $this->result['message'] = JText::_('COM_BT_MEDIA_ERROR_COULD_NOT_SAVE');
                    } else {
                        Bt_mediaLegacyHelper::processImage($params->originalSaveDir . '/' . $filename, $params->largeSaveDir . '/' . $filename, $params->params->get('image_width', 700), $params->params->get('image_height', 450), $params->params->get('large_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                        Bt_mediaLegacyHelper::processImage($params->originalSaveDir . '/' . $filename, $params->thumbSaveDir . '/' . $filename, $params->thumbWidth, $params->thumbHeight, $params->params->get('thumb_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                        $objFile->name = $basename;
                        $objFile->image_path = $filename;
                        $objFile->source_of_media = 'Upload from local';
                        $objFile->media_type = 'image';

                        $rs_html = Bt_mediaLegacyHelper::createHTML($hashedName, $objFile->image_path, $objFile->name, 'files_upload', $params->params);

                        $this->result['success'] = TRUE;
                        $this->result['message'] = JText::_('File upload success');
                        $this->result['data'] = $rs_html;
                        Bt_mediaLegacyHelper::sessionStore('files_upload', $objFile);
                    }
                } else {
                    if (Bt_mediaLegacyHelper::checkFileOnDB($filename) || Bt_mediaLegacyHelper::checkFileInSession('files_upload', $filename)) {
                        $this->result['message'] = JText::_('COM_BT_MEDIA_FILE_EXIST');
                    } else {

                        if (!JFile::exists($params->largeSaveDir . '/' . $filename)) {
                            Bt_mediaLegacyHelper::processImage($params->originalSaveDir . '/' . $filename, $params->largeSaveDir . '/' . $filename, $params->params->get('image_width', 700), $params->params->get('image_height', 450), $params->params->get('large_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                        }
                        if (!JFile::exists($params->thumbSaveDir . '/' . $filename)) {
                            Bt_mediaLegacyHelper::processImage($params->originalSaveDir . '/' . $filename, $params->thumbSaveDir . '/' . $filename, $params->thumbWidth, $params->thumbHeight, $params->params->get('thumb_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                        }
                        $objFile->name = $basename;
                        $objFile->image_path = $filename;
                        $objFile->source_of_media = 'Upload from local';
                        $objFile->media_type = 'image';

                        $rs_html = Bt_mediaLegacyHelper::createHTML($hashedName, $objFile->image_path, $objFile->name, 'files_upload', $params->params);

                        $this->result['success'] = TRUE;
                        $this->result['data'] = $rs_html;
                        Bt_mediaLegacyHelper::sessionStore('files_upload', $objFile);
                    }
                }
            }
        } else {
            $this->result['message'] = JText::_('COM_BT_MEDIA_FILE_EXTENSION_INVALID');
        }
        return $this->result;
    }

    public function jFolderGetFiles($data) {
        $image_folder = JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
        $folders = explode(',', $data);
        $files = array();
        foreach ($folders as $folder) {
            if ($folder != '0' && $folder != '1') {
                $files = Bt_mediaLegacyHelper::getFileInFolder($image_folder . $folder . DIRECTORY_SEPARATOR, $files, end($folders));
            }
        }

        $this->result['success'] = true;
        $this->result['message'] = JText::_('All file had been get');
        $this->result['data'] = $files;

        return $this->result;
    }

    public function jFolderGetFile($data, $params) {
        $fileInfo = pathinfo($data);
        $hashedName = md5('jfolder-' . $fileInfo['filename']);
        $filename = $hashedName . '.' . $fileInfo['extension'];
        if (in_array($fileInfo['extension'], array('png', 'jpg', 'jpeg', 'gif'))) {
            if (!JFile::exists($params->originalSaveDir . '/' . $filename)) {
                if (JFile::copy($data, $params->originalSaveDir . '/' . $filename)) {
                    if (file_exists($params->originalSaveDir . '/' . $filename)) {
                        Bt_mediaLegacyHelper::processImage($params->originalSaveDir . '/' . $filename, $params->largeSaveDir . '/' . $filename, $params->params->get('image_width', 700), $params->params->get('image_height', 450), $params->params->get('large_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                        Bt_mediaLegacyHelper::processImage($params->originalSaveDir . '/' . $filename, $params->thumbSaveDir . '/' . $filename, $params->thumbWidth, $params->thumbHeight, $params->params->get('thumb_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                        $objFile = new stdClass();
                        $objFile->name = $fileInfo['filename'];
                        $objFile->image_path = $filename;
                        $objFile->source_of_media = 'JFolder';
                        $objFile->media_type = 'image';

                        $rs_html = Bt_mediaLegacyHelper::createHTML($hashedName, $objFile->image_path, $objFile->name, 'images_jfolder', $params->params);

                        Bt_mediaLegacyHelper::sessionStore('images_jfolder', $objFile);
                        $this->result['success'] = TRUE;
                        $this->result['message'] = JText::_("File get success");
                        $this->result['data'] = $rs_html;
                    }
                } else {
                    $this->result['message'] = JText::_("File get fail");
                }
            } else {
                if (Bt_mediaLegacyHelper::checkFileOnDB($filename) || Bt_mediaLegacyHelper::checkFileInSession('images_jfolder', $filename)) {
                    $this->result['message'] = JText::_('COM_BT_MEDIA_FILE_EXIST');
                } else {
                    if (!JFile::exists($params->largeSaveDir . '/' . $filename)) {
                        Bt_mediaLegacyHelper::processImage($params->originalSaveDir . '/' . $filename, $params->largeSaveDir . '/' . $filename, $params->params->get('image_width', 700), $params->params->get('image_height', 450), $params->params->get('large_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                    }
                    if (!JFile::exists($params->thumbSaveDir . '/' . $filename)) {
                        Bt_mediaLegacyHelper::processImage($params->originalSaveDir . '/' . $filename, $params->thumbSaveDir . '/' . $filename, $params->thumbWidth, $params->thumbHeight, $params->params->get('thumb_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                    }
                    $objFile = new stdClass();
                    $objFile->name = $fileInfo['filename'];
                    $objFile->image_path = $filename;
                    $objFile->source_of_media = 'JFolder';
                    $objFile->media_type = 'image';

                    $rs_html = Bt_mediaLegacyHelper::createHTML($hashedName, $objFile->image_path, $objFile->name, 'images_jfolder', $params->params);

                    Bt_mediaLegacyHelper::sessionStore('images_jfolder', $objFile);
                    $this->result['success'] = TRUE;
                    $this->result['message'] = JText::_("File exist on server but not exist on database");
                    $this->result['data'] = $rs_html;
                }
            }
        }

        if (in_array($fileInfo['extension'], array('flv', 'mp4'))) {
            if (!JFile::exists($params->videoOriginalSaveDir . '/' . $filename)) {
                if (JFile::copy($data, $params->videoOriginalSaveDir . '/' . $filename)) {
                    if (file_exists($this->videoOriginalSaveDir . '/' . $filename)) {
                        if (!JFile::exists($params->originalSaveDir . '/default_video_thumb.jpg')) {
                            JFile::copy(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'assets/images/default_video_thumb.jpg', $params->originalSaveDir . '/default_video_thumb.jpg');
                        }
                        list($width, $height) = getimagesize($params->originalSaveDir . '/default_video_thumb.jpg');
                        Bt_mediaLegacyHelper::processImage($params->originalSaveDir . '/default_video_thumb.jpg', $params->originalSaveDir . '/' . $hashedName . '.jpg', $width, $height, $params->params->get('large_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                        Bt_mediaLegacyHelper::processImage($params->originalSaveDir . '/default_video_thumb.jpg', $params->largeSaveDir . '/' . $hashedName . '.jpg', $params->params->get('image_width', 700), $params->params->get('image_height', 450), $params->params->get('large_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                        Bt_mediaLegacyHelper::processImage($params->originalSaveDir . '/default_video_thumb.jpg', $params->thumbSaveDir . '/' . $hashedName . '.jpg', $params->thumbWidth, $params->thumbHeight, $params->params->get('thumb_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                        $objFile = new stdClass();
                        $objFile->name = $fileInfo['filename'];
                        $objFile->image_path = $hashedName . '.jpg';
                        $objFile->video_path = $filename;
                        $objFile->source_of_media = 'JFolder';
                        $objFile->media_type = 'video';

                        $rs_html = Bt_mediaLegacyHelper::createHTML($hashedName, $objFile->image_path, $objFile->name, 'videos_jfolder', $params->params);

                        Bt_mediaLegacyHelper::sessionStore('videos_jfolder', $objFile);
                        $this->result['success'] = TRUE;
                        $this->result['message'] = JText::_("File get success");
                        $this->result['data'] = $rs_html;
                    }
                } else {
                    $this->result['message'] = JText::_("File get fail");
                }
            } else {
                if (Bt_mediaLegacyHelper::checkFileOnDB($hashedName . '.jpg') || Bt_mediaLegacyHelper::checkFileInSession('videos_jfolder', $hashedName . '.jpg')) {
                    $this->result['message'] = JText::_('COM_BT_MEDIA_FILE_EXIST');
                } else {
                    if (!JFile::exists($params->originalSaveDir . '/default_video_thumb.jpg')) {
                        JFile::copy(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'assets/images/default_video_thumb.jpg', $params->originalSaveDir . '/default_video_thumb.jpg');
                    }
                    if (!JFile::exists($params->originalSaveDir . '/' . $hashedName . '.jpg')) {
                        list($width, $height) = getimagesize($params->originalSaveDir . '/default_video_thumb.jpg');
                        Bt_mediaLegacyHelper::processImage($params->originalSaveDir . '/default_video_thumb.jpg', $params->originalSaveDir . '/' . $hashedName . '.jpg', $width, $height, $params->params->get('large_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                    }
                    if (!JFile::exists($params->largeSaveDir . '/' . $hashedName . '.jpg')) {
                        Bt_mediaLegacyHelper::processImage($params->originalSaveDir . '/default_video_thumb.jpg', $params->largeSaveDir . '/' . $hashedName . '.jpg', $params->params->get('image_width', 700), $params->params->get('image_height', 450), $params->params->get('large_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                    }
                    if (!JFile::exists($params->thumbSaveDir . '/' . $hashedName . '.jpg')) {
                        Bt_mediaLegacyHelper::processImage($params->originalSaveDir . '/default_video_thumb.jpg', $params->thumbSaveDir . '/' . $hashedName . '.jpg', $params->thumbWidth, $params->thumbHeight, $params->params->get('thumb_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                    }
                    $objFile = new stdClass();
                    $objFile->name = $fileInfo['filename'];
                    $objFile->image_path = $hashedName . '.jpg';
                    $objFile->video_path = $filename;
                    $objFile->source_of_media = 'JFolder';
                    $objFile->media_type = 'video';

                    $rs_html = Bt_mediaLegacyHelper::createHTML($hashedName, $objFile->image_path, $objFile->name, 'videos_jfolder', $params->params);

                    Bt_mediaLegacyHelper::sessionStore('videos_jfolder', $objFile);
                    $this->result['success'] = TRUE;
                    $this->result['message'] = JText::_("File exist on server but not exist on database");
                    $this->result['data'] = $rs_html;
                }
            }
        }

        return $this->result;
    }

    public function imageGetAlbums($from, $author, $params = NULL) {
        if ($from == 'flickr') {
            include_once 'components/com_bt_media/lib/phpFlickr/phpFlickr.php';
            if ($params->params->get('flickr_api') == '') {
                $this->result['message'] = JText::_('COM_BT_MEDIA_FLICKR_API_KEY_IS_NULL');
                return $this->result;
            }

            $flickrObj = new phpFlickr($params->params->get('flickr_api'));
            if (filter_var($author, FILTER_VALIDATE_EMAIL)) {
                $auth_info = $flickrObj->people_findByEmail($author);
            } else {
                $auth_info = $flickrObj->people_findByUsername($author);
            }
            $list_album = $flickrObj->photosets_getList($auth_info['id']);
            if (count($list_album['photoset']) > 0) {
                $albums = array();
                $albums[] = '<option value="0">' . JText::_('Please select an album') . '</option>';
                foreach ($list_album['photoset'] as $album) {
                     $albums[] = '<option value="' . $album['id'] . '">' . (is_string($album['title']) ? $album['title'] : $album['title']['_content'])  . '</option>';
                }
                $data = implode($albums);
                $this->result['success'] = true;
                $this->result['message'] = JText::_('Albums had been get');
                $this->result['data'] = $data;
            } else {
                $this->result['success'] = true;
                $this->result['message'] = JText::_('No album find');
                $this->result['data'] = '<option value="0">' . JText::_('Please select an album') . '</option>';
            }
        }
        if ($from == 'picasa') {
            $feedURL = 'http://picasaweb.google.com/data/feed/api/user/' . $author . '?alt=rss&kind=album';
            @$list_album = simplexml_load_file($feedURL);
            if (isset($list_album) && $list_album) {
                $albums = array();
                $albums[] = '<option value="0">' . JText::_('Please select an album') . '</option>';
                foreach ($list_album->channel->item as $album) {
                    $guid = (string) $album->guid;
                    $albumID = substr($guid, strrpos($guid, '/') + 1, strrpos($guid, '?') - 1 - strrpos($guid, '/'));
                    $albums[] = '<option value="' . $albumID . '">' . $album->title . '</option>';
                }
                $data = implode($albums);
                $this->result['success'] = TRUE;
                $this->result['message'] = JText::_('All album had been get');
                $this->result['data'] = $data;
            } else {
                $this->result['success'] = TRUE;
                $this->result['message'] = JText::_('No album find');
                $this->result['data'] = '<option value="0">' . JText::_('Please select an album') . '</option>';
            }
        }
        return $this->result;
    }

    public function imageGetPhotos($from, $albumid, $username = null, $params = null) {
        if ($from == 'flickr') {
            include_once 'components/com_bt_media/lib/phpFlickr/phpFlickr.php';
            if ($params->params->get('flickr_api') == '') {
                $this->result['message'] = JText::_('COM_BT_MEDIA_FLICKR_API_KEY_IS_NULL');
                return $this->result;
            }
            $flickrObj = new phpFlickr($params->params->get('flickr_api'));

            $photos_in_album = $flickrObj->photosets_getPhotos($albumid);
            if ($photos_in_album['stat'] == 'ok') {
                $photos = $photos_in_album['photoset']['photo'];
                if (!empty($photos)) {
                    $images = array();
                    foreach ($photos as $photo) {
                        $images[] = $photo['id'];
                    }
                    $this->result['success'] = TRUE;
                    $this->result['message'] = JText::_('All file had been get');
                    $this->result['data'] = $images;
                } else {
                    $this->result['message'] = JText::_('No images found');
                }
            } else {
                $this->result['message'] = JText::_('Can\'t get file in album');
            }
        }
        if ($from == 'picasa') {
            $feedURL = 'http://picasaweb.google.com/data/feed/base/user/' . $username . '/albumid/' . $albumid . '?alt=rss';
            $photos = @simplexml_load_file($feedURL);
            if (count($photos->channel->item) > 0) {
                $images = array();
                $i = 0;
                foreach ($photos->channel->item as $photo) {
                    $image_url = (string) $photo->enclosure->attributes()->url;
                    $images[$i]['url'] = $image_url;
                    $images[$i]['title'] = (string) $photo->title;
                    $i++;
                }
                $this->result['success'] = TRUE;
                $this->result['message'] = JText::_('All items get');
                $this->result['data'] = $images;
            } else {
                $this->result['message'] = JText::_('No item found');
            }
        }

        return $this->result;
    }

    public function imageGetPhoto($from, $photoid, $params) {
        if ($from == 'flickr') {
            include_once 'components/com_bt_media/lib/phpFlickr/phpFlickr.php';
            if ($params->params->get('flickr_api') == '') {
                $this->result['message'] = JText::_('COM_BT_MEDIA_FLICKR_API_KEY_IS_NULL');
                return $this->result;
            }
            $flickrObj = new phpFlickr($params->params->get('flickr_api'));

            $photo_info = $flickrObj->photos_getInfo($photoid);
            $photo = $photo_info['photo'];
            $media_type = $photo['media'];
            if ($media_type == 'photo') {
                $b_image = 'http://farm' . $photo['farm'] . '.staticflickr.com/' . $photo['server'] . '/' . $photo['id'] . '_' . $photo['secret'] . '_b.jpg';
                $z_image = 'http://farm' . $photo['farm'] . '.staticflickr.com/' . $photo['server'] . '/' . $photo['id'] . '_' . $photo['secret'] . '_z.jpg';
                $fileInfo = pathinfo($b_image);
                $hashedName = md5('flickr-' . $photo['id'] . '_' . $photo['secret']);
                $filename = $hashedName . '.' . $fileInfo['extension'];
                if (!JFile::exists($params->originalSaveDir . '/' . $filename)) {
                    $file_data_fail = file_get_contents('http://l.yimg.com/g/images/photo_unavailable.gif');
                    $b_data = file_get_contents($b_image);
                    $z_data = file_get_contents($z_image);
                    if ($file_data_fail == $b_data && $file_data_fail == $z_data) {
                        $this->result['message'] = JText::_("File not found");
                    } else {
                        if ($file_data_fail == $z_data) {
                            $flickr_image = $b_image;
                        } else {
                            $flickr_image = $z_image;
                        }
                        if (file_put_contents($params->originalSaveDir . '/' . $filename, file_get_contents($flickr_image))) {
                            if (file_exists($params->originalSaveDir . '/' . $filename)) {
                                Bt_mediaLegacyHelper::processImage($params->originalSaveDir . '/' . $filename, $params->largeSaveDir . '/' . $filename, $params->params->get('image_width', 700), $params->params->get('image_height', 450), $params->params->get('large_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                                Bt_mediaLegacyHelper::processImage($params->originalSaveDir . '/' . $filename, $params->thumbSaveDir . '/' . $filename, $params->thumbWidth, $params->thumbHeight, $params->params->get('thumb_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                                $objFile = new stdClass();
                                $objFile->name = $photo['title']['_content'];
                                $objFile->image_path = $filename;
                                $objFile->description = $photo['description']['_content'];
                                $objFile->source_of_media = 'Flickr Server';
                                $objFile->media_type = 'image';
                                $rs_html = Bt_mediaLegacyHelper::createHTML($hashedName, $objFile->image_path, $objFile->name, 'images_flickr', $params->params);
                                Bt_mediaLegacyHelper::sessionStore('images_flickr', $objFile);
                                $this->result['success'] = TRUE;
                                $this->result['message'] = JText::_("File get success");
                                $this->result['data'] = $rs_html;
                            }
                        } else {
                            $this->result['message'] = JText::_("File get fail");
                        }
                    }
                } else {
                    if (Bt_mediaLegacyHelper::checkFileOnDB($filename) || Bt_mediaLegacyHelper::checkFileInSession('images_flickr', $filename)) {
                        $this->result['message'] = JText::_('COM_BT_MEDIA_FILE_EXIST');
                    } else {
                        if (!JFile::exists($params->largeSaveDir . '/' . $filename)) {
                            Bt_mediaLegacyHelper::processImage($params->originalSaveDir . '/' . $filename, $params->largeSaveDir . '/' . $filename, $params->params->get('image_width', 700), $params->params->get('image_height', 450), $params->params->get('large_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                        }
                        if (!JFile::exists($params->thumbSaveDir . '/' . $filename)) {
                            Bt_mediaLegacyHelper::processImage($params->originalSaveDir . '/' . $filename, $params->thumbSaveDir . '/' . $filename, $params->thumbWidth, $params->thumbHeight, $params->params->get('thumb_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                        }
                        $objFile = new stdClass();
                        $objFile->name = $photo['title']['_content'];
                        $objFile->image_path = $filename;
                        $objFile->description = $photo['description']['_content'];
                        $objFile->source_of_media = 'Flickr Server';
                        $objFile->media_type = 'image';
                        $rs_html = Bt_mediaLegacyHelper::createHTML($hashedName, $objFile->image_path, $objFile->name, 'images_flickr', $params->params);
                        Bt_mediaLegacyHelper::sessionStore('images_flickr', $objFile);
                        $this->result['success'] = TRUE;
                        $this->result['message'] = JText::_("File exist on server but not exist on database");
                        $this->result['data'] = $rs_html;
                    }
                }
            } else if ($media_type == 'video') {
                return Bt_mediaLegacyHelper::flickrGetVideo($photo, $params->params);
            } else {
                $this->result['message'] = JText::_("Error: Not determine the type of media");
            }
        }

        if ($from == 'picasa') {
            $fileInfo = pathinfo($photoid[0]);
            $hashedName = md5('picasa-' . $fileInfo['filename']);
            $filename = $hashedName . '.' . $fileInfo['extension'];

            if (!JFile::exists($params->originalSaveDir . '/' . $filename)) {
                if (copy($photoid[0], $params->originalSaveDir . '/' . $filename)) {
                    if (file_exists($params->originalSaveDir . '/' . $filename)) {
                        Bt_mediaLegacyHelper::processImage($params->originalSaveDir . '/' . $filename, $params->largeSaveDir . '/' . $filename, $params->params->get('image_width', 700), $params->params->get('image_height', 450), $params->params->get('large_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                        Bt_mediaLegacyHelper::processImage($params->originalSaveDir . '/' . $filename, $params->thumbSaveDir . '/' . $filename, $params->thumbWidth, $params->thumbHeight, $params->params->get('thumb_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                        $objFile = new stdClass();
                        $objFile->name = $photoid[1];
                        $objFile->image_path = $filename;
                        $objFile->source_of_media = 'Picasa Server';
                        $objFile->media_type = 'image';

                        $rs_html = Bt_mediaLegacyHelper::createHTML($hashedName, $objFile->image_path, $objFile->name, 'images_picasa', $params->params);

                        Bt_mediaLegacyHelper::sessionStore('images_picasa', $objFile);
                        $this->result['success'] = TRUE;
                        $this->result['message'] = JText::_("File get success");
                        $this->result['data'] = $rs_html;
                    }
                } else {
                    $this->result['message'] = JText::_('File get fail');
                }
            } else {
                if (Bt_mediaLegacyHelper::checkFileOnDB($filename) || Bt_mediaLegacyHelper::checkFileInSession('images_picasa', $filename)) {
                    $this->result['message'] = JText::_('COM_BT_MEDIA_FILE_EXIST');
                } else {
                    if (!JFile::exists($params->largeSaveDir . '/' . $filename)) {
                        Bt_mediaLegacyHelper::processImage($params->originalSaveDir . '/' . $filename, $params->largeSaveDir . '/' . $filename, $params->params->get('image_width', 700), $params->params->get('image_height', 450), $params->params->get('large_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                    }
                    if (!JFile::exists($params->thumbSaveDir . '/' . $filename)) {
                        Bt_mediaLegacyHelper::processImage($params->originalSaveDir . '/' . $filename, $params->thumbSaveDir . '/' . $filename, $params->thumbWidth, $params->thumbHeight, $params->params->get('thumb_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                    }
                    $objFile = new stdClass();
                    $objFile->name = $photoid[1];
                    $objFile->image_path = $filename;
                    $objFile->source_of_media = 'Picasa Server';
                    $objFile->media_type = 'image';

                    $rs_html = Bt_mediaLegacyHelper::createHTML($hashedName, $objFile->image_path, $objFile->name, 'images_picasa', $params->params);

                    Bt_mediaLegacyHelper::sessionStore('images_picasa', $objFile);
                    $this->result['success'] = TRUE;
                    $this->result['message'] = JText::_("File exist on server but not exist on database");
                    $this->result['data'] = $rs_html;
                }
            }
        }
        return $this->result;
    }

    public function videoGetPlaylists($from, $username) {
        if ($from == 'youtube') {
            $channel = Bt_mediaLegacyHelper::getYoutubeChannelByUsername($username);

            $playlists = array();
            if(isset($channel->contentDetails->relatedPlaylists)) {
                if (isset($channel->contentDetails->relatedPlaylists->likes)) {
                    $playlists[$channel->contentDetails->relatedPlaylists->likes] = 'Likes' ;
                }
                if(isset($channel->contentDetails->relatedPlaylists->uploads)){
                    $playlists[$channel->contentDetails->relatedPlaylists->uploads] = 'Uploads';
                }
            }

            $playlists = array_merge($playlists, Bt_mediaLegacyHelper::getYoutubePlaylistByChannelId($channel->id));


            if (count($playlists)) {
                $options = array();
                $options[] = '<option value="">' . JText::_('Please select a playlist') . '</option>';
                foreach ($playlists as $id => $title) {
                    $options[] = '<option value="playlist|' . $id . '">' . $title . '</option>';
                }
                $data = implode($options);
                $this->result['success'] = TRUE;
                $this->result['message'] = JText::_('All playlist had been get');
                $this->result['data'] = $data;
            } else {
                $this->result['success'] = TRUE;
                $this->result['message'] = JText::_('No playlist find');
                $this->result['data'] = '<option value="">Please select a playlist</option>';
            }
        }
        if ($from == 'vimeo') {
            $dataURL = 'http://vimeo.com/api/v2/' . $username . '/info.xml';
            $info = @simplexml_load_file($dataURL);

            if($info){
                $userId = (string) $info->user->id;
                $dataURL = 'http://vimeo.com/api/v2/' . $username . '/albums.xml';
                $albums = @simplexml_load_file($dataURL);

                if ($albums) {
                    $options = array();
                    $options[] = '<option value="">' . JText::_('Please select a playlist') . '</option>';
                    foreach ($albums as $album) {
                        if ($album) {
                            $album_id = $album->id;
                            $album_title = $album->title;
                            if ($album_id && $album_title) {
                                $options[] = '<option value="album_'.  $userId . '_' . $album_id . '">' . $album_title . '</option>';
                            }
                        }
                    }
                    $data = implode($options);

                    $this->result['success'] = TRUE;
                    $this->result['message'] = JText::_('All album get');
                    $this->result['data'] = $data;
                }else{
                    $this->result['success'] = FALSE;
                    $this->result['message'] = JText::_('There is no album');
                    $this->result['data'] = '<option value="">Please select an album</option>';
                }

            } else {
                $this->result['success'] = FALSE;
                $this->result['message'] = JText::_('Can not get user information');
                $this->result['data'] = '<option value="">Please select an album</option>';
            }
        }
        return $this->result;
    }

    public function videoGetFromURL($from, $url) {
        if ($from == 'youtube') {
            if (filter_var($url, FILTER_VALIDATE_URL)) {
                $parse_url_1 = explode("?", $url);
                $parse_url = explode("&", $parse_url_1[1]);

                foreach ($parse_url as $value) {
                    $param = explode('=', $value);
                    if ($param[0] == 'v') {
                        $video_id = $param[1];
                        if (strpos($video_id, '#')) {
                            $video_id = substr($video_id, 0, strpos($video_id, '#'));
                        }
                        $this->result['success'] = TRUE;
                        $this->result['message'] = JText::_('Video had been get');
                        $this->result['data'] = 'video|' . $video_id;
                        break;
                    }
                    if ($param[0] == "list") {
                        $playlist_id = $param[1];

                        $this->result['success'] = TRUE;
                        $this->result['message'] = JText::_('Video in playlist had been get');
                        $this->result['data'] = 'playlist|' . $playlist_id;
                        break;
                    }
                }
            } else {
                $this->result['success'] = FALSE;
                $this->result['message'] = JText::_('Youtube url invalid');
            }
        }
        if ($from == 'vimeo') {
            if (filter_var($url, FILTER_VALIDATE_URL)) {
                $parse_url = explode("/", $url);

                $is_album = $parse_url[count($parse_url) - 2];
                if ($is_album == "album") {
                    $album_id = $parse_url[count($parse_url) - 1];
                    $this->result['success'] = TRUE;
                    $this->result['message'] = JText::_('All video had been get');
                    $this->result['data'] = "album_" . $album_id;
                } else {
                    $video_id = $parse_url[count($parse_url) - 1];
                    if (is_numeric($video_id)) {

                        $this->result['success'] = TRUE;
                        $this->result['message'] = JText::_('Video had been get');
                        $this->result['data'] = "video_" . $video_id;
                    } else {
                        $this->result['message'] = JText::_('COM_BT_MEDIA_VIDEO_VIMEO_ID_INVALID');
                    }
                }
            } else {
                $this->result['message'] = JText::_('Vimeo url invalid');
            }
        }
        return $this->result;
    }

    public function videoGetVideos($from, $data) {

        $params = JComponentHelper::getParams('com_bt_media');
        if ($from == 'youtube') {
            if(!$params->get('google_api')){
                $this->result['message'] = JText::_('Google API has not been set');
                return $this->result;
            }
            $parse_data = explode("|", $data);
            if ($parse_data[0] == "playlist") {
                $videos = Bt_mediaLegacyHelper::youtubeGetVideoFromPlaylist($parse_data[1], $params);
                if (count($videos) > 0) {
                    $this->result['success'] = TRUE;
                    $this->result['message'] = JText::_('All video had been get');
                    $this->result['data'] = $videos;
                } else {
                    $this->result['message'] = JText::_('Can\'t get video from playlist');
                }
            } else if ($parse_data[0] == "video") {
                $this->result['success'] = TRUE;
                $this->result['message'] = JText::_('Video had been get');
                $this->result['data'] = $parse_data[1];
            } else if ($parse_data[0] == "user") {
                $videos = Bt_mediaLegacyHelper::youtubeGetVideoByUser($parse_data[1], $params);
                if (count($videos) > 0) {
                    $this->result['success'] = TRUE;
                    $this->result['message'] = JText::_('All video had been get');
                    $this->result['data'] = $videos;
                } else {
                    $this->result['message'] = JText::_('Can\'t get video from playlist');
                }
            } else {
                $this->result['message'] = JText::_('Can\'t get video data');
            }
        }
        if ($from == 'vimeo') {
            $parse_data = explode('_', $data);
            if ($parse_data[0] == "album") {
                if(isset($parse_data[2])){
                    $userId = $parse_data[1];
                    $albumId = $parse_data[2];
                }else{
                    $userId = null;
                    $albumId = $parse_data[1];
                }
                $videoCount = Bt_mediaLegacyHelper::vimeoGetVideoFromAlbum($userId, $albumId);
                if ($videoCount) {
                    $this->result['success'] = TRUE;
                    $this->result['message'] = JText::_('All video had been get');
                    $this->result['data'] = $videoCount;
                } else {
                    $this->result['message'] = JText::_('Can\'t get video from album');
                }
            } else if ($parse_data[0] == "video") {
                $this->result['success'] = TRUE;
                $this->result['message'] = JText::_('Video had been get');
                $this->result['data'] = $parse_data[1];
            } else {
                $this->result['message'] = JText::_('Can\'t get video data');
            }
        }

        return $this->result;
    }

    public function getDataUrl($url, $oldFile, $source_server, $media_type, $params) {
        $urlinfo = parse_url($url);
        if ($urlinfo['host'] == 'youtube.com' || $urlinfo['host'] == 'www.youtube.com') {
            if (!JFactory::getUser()->authorise('media.get.video', 'com_bt_media')) {
                $this->result['message'] = '
                                <div class="alert alert-error">
                                    <h4 class="alert-heading">Message</h4>
                                    <p>' . JText::_('COM_BT_MEDIA_NOT_PERMISSION_GET_VIDEO_UPLOAD') . '</p>
                                  </div>';
                return $this->result;
            }
            if (filter_var($url, FILTER_VALIDATE_URL)) {
                $parse_url_1 = explode("?", $url);
                $parse_url = explode("&", $parse_url_1[1]);

                foreach ($parse_url as $value) {
                    $param = explode('=', $value);
                    if ($param[0] == 'v') {
                        $video_id = $param[1];
                        if (strpos($video_id, '#')) {
                            $video_id = substr($video_id, 0, strpos($video_id, '#'));
                        }
                        $hashedName = md5('youtube-' . $video_id);
                        $filename = $hashedName . '.jpg';
                        $videoObj = NULL;
                        if ($filename == $oldFile) {
                            $this->result['message'] = '
                                <div class="alert alert-error">
                                    <h4 class="alert-heading">Message</h4>
                                    <p>' . JText::_('COM_BT_MEDIA_FILE_EXIST') . '</p>
                                  </div>';
                            return $this->result;
                        }

                        if (!JFile::exists($params->imageTempSaveDir . '/' . $filename)) {


                            $urlFeed = 'https://www.googleapis.com/youtube/v3/videos?key=' . $params->params->get('google_api', '') . '&part=snippet&id=' . $video_id;
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_HEADER, 0);
                            curl_setopt($ch, CURLOPT_URL, $urlFeed);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            $rsp = curl_exec($ch);
                            curl_close($ch);
                            $data = json_decode($rsp);
                            if(isset($data->error)){
                                $this->result['message'] = $data->error->message;
                                return;
                            }
                            if(isset($data->items[0])) {
                                $video = $data->items[0];
                                $videoObj = new stdClass();
                                $tags = get_meta_tags('http://www.youtube.com/watch?v=' . $video_id);
                                if (copy($video->snippet->thumbnails->high->url, $params->imageTempSaveDir . '/' . $filename)) {
                                    if (file_exists($params->imageTempSaveDir . '/' . $filename)) {
                                        Bt_mediaLegacyHelper::processImage($params->imageTempSaveDir . '/' . $filename, $params->imageTempSaveDir . '/thumb_' . $filename, $params->params->get('thumb_image_width', 200), $params->params->get('thumb_image_height', 130), $params->params->get('thumb_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                                        Bt_mediaLegacyHelper::processImage($params->imageTempSaveDir . '/' . $filename, $params->imageTempSaveDir . '/large_' . $filename, $params->params->get('image_width', 700), $params->params->get('image_height', 450), $params->params->get('large_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                                        $videoObj->name = $video->snippet->title;;
                                        $videoObj->video_path = $video_id;
                                        $videoObj->image_path = $filename;
                                        $videoObj->description = $video->snippet->description;
                                        $videoObj->tags = $tags['keywords'];
                                        $videoObj->source_of_media = 'Youtube Server';
                                        $videoObj->media_type = 'video';

                                        $file_path = array();
                                        $file_path[] = $filename . '|' . $oldFile;
                                        $file_path[] = '<img src="' . JUri::root() . $params->params->get('file_save', 'images/bt_media') . '/temp/images/large_' . $filename . '"/>';
                                        $file_path[] = $videoObj;

                                        $this->result['success'] = TRUE;
                                        $this->result['message'] = JText::_('File get success');
                                        $this->result['data'] = $file_path;
                                    }
                                } else {
                                    $this->result['message'] = JText::_('File not found');
                                }
                            }else {
                                $this->result['message'] = '<div class="alert alert-note">
                                    <h4 class="alert-heading">Message</h4>
                                    <p>' . JText::_('This video has been removed by the user.') . '</p>
                                  </div>';
                            }

                        } else {

                            if (!JFile::exists($params->imageTempSaveDir . '/large_' . $filename)) {
                                Bt_mediaLegacyHelper::processImage($params->imageTempSaveDir . '/' . $filename, $params->imageTempSaveDir . '/large_' . $filename, $params->params->get('image_width', 700), $params->params->get('image_height', 450), $params->params->get('large_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                            }
                            if (!JFile::exists($params->imageTempSaveDir . '/thumb_' . $filename)) {
                                Bt_mediaLegacyHelper::processImage($params->imageTempSaveDir . '/' . $filename, $params->imageTempSaveDir . '/thumb_' . $filename, $params->thumbWidth, $params->thumbHeight, $params->params->get('thumb_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                            }

                            $urlFeed = 'https://www.googleapis.com/youtube/v3/videos?key=' . $params->params->get('google_api', '') . '&part=snippet&id=' . $video_id;
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_HEADER, 0);
                            curl_setopt($ch, CURLOPT_URL, $urlFeed);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            $rsp = curl_exec($ch);
                            curl_close($ch);
                            $data = json_decode($rsp);
                            if(isset($data->error)){
                                $this->result['message'] = $data->error->message;
                                return;
                            }
                            if(isset($data->items[0])) {
                                $video = $data->items[0];
                                $videoObj = new stdClass();
                                $tags = get_meta_tags('http://www.youtube.com/watch?v=' . $video_id);

                                $videoObj->name = $video->snippet->title;;
                                $videoObj->video_path = $video_id;
                                $videoObj->image_path = $filename;
                                $videoObj->description = $video->snippet->description;
                                $videoObj->tags = $tags['keywords'];
                                $videoObj->source_of_media = 'Youtube Server';
                                $videoObj->media_type = 'video';

                                $file_path = array();
                                $file_path[] = $filename . '|' . $oldFile;
                                $file_path[] = '<img src="' . JUri::root() . $params->params->get('file_save', 'images/bt_media') . '/temp/images/large_' . $filename . '"/>';
                                $file_path[] = $videoObj;

                                $this->result['success'] = TRUE;
                                $this->result['message'] = JText::_('File exist on server but not exist on database');
                                $this->result['data'] = $file_path;

                            }else {
                                $this->result['message'] = '<div class="alert alert-note">
                                    <h4 class="alert-heading">Message</h4>
                                    <p>' . JText::_('This video has been removed by the user.') . '</p>
                                  </div>';
                            }

                            $urlFeed = 'http://gdata.youtube.com/feeds/api/videos/' . $video_id;
                            $video = @simplexml_load_file($urlFeed);
                            if ($video) {
                                $videoObj = new stdClass();
                                $tags = get_meta_tags('http://www.youtube.com/watch?v=' . $video_id);
                                $videoObj->name = (string) $video->title;
                                $videoObj->video_path = $video_id;
                                $videoObj->image_path = $filename;
                                $videoObj->description = (string) $video->content;
                                $videoObj->tags = $tags['keywords'];
                                $videoObj->source_of_media = 'Youtube Server';
                                $videoObj->media_type = 'video';

                                $file_path = array();
                                $file_path[] = $filename . '|' . $oldFile;
                                $file_path[] = '<img src="' . JUri::root() . $params->params->get('file_save', 'images/bt_media') . '/temp/images/large_' . $filename . '"/>';
                                $file_path[] = $videoObj;

                                $this->result['success'] = TRUE;
                                $this->result['message'] = JText::_('File exist on server but not exist on database');
                                $this->result['data'] = $file_path;
                            } else {
                                $this->result['message'] = '<div class="alert alert-note">
                                    <h4 class="alert-heading">Message</h4>
                                    <p>' . JText::_('This video has been removed by the user.') . '</p>
                                  </div>';
                            }
                        }
                        break;
                    }
                    if ($param[0] == "list") {
                        $this->result['message'] = '<div class="alert alert-note">
                                    <h4 class="alert-heading">Message</h4>
                                    <p>' . JText::_('This is a youtube playlist url, please input a video url tu get it.') . '</p>
                                  </div>';
                        break;
                    }
                }
            } else {
                $this->result['message'] = JText::_('Youtube url invalid');
            }
        } else if ($urlinfo['host'] == 'vimeo.com' || $urlinfo['host'] == 'www.vimeo.com') {
            if (!JFactory::getUser()->authorise('media.get.video', 'com_bt_media')) {
                $this->result['message'] = '
                                <div class="alert alert-error">
                                    <h4 class="alert-heading">Message</h4>
                                    <p>' . JText::_('COM_BT_MEDIA_NOT_PERMISSION_GET_VIDEO_UPLOAD') . '</p>
                                  </div>';
                return $this->result;
            }
            if (filter_var($url, FILTER_VALIDATE_URL)) {
                $parse_url = explode("/", $url);
                $is_album = $parse_url[count($parse_url) - 2];
                if ($is_album == "album") {
                    $this->result['message'] = '<div class="alert alert-note">
                                    <h4 class="alert-heading">Message</h4>
                                    <p>' . JText::_('This is a vimeo album url, please input a video url tu get it.') . '</p>
                                  </div>';
                } else {
                    $video_id = $parse_url[count($parse_url) - 1];
                    if (is_numeric($video_id)) {
                        $hashedName = md5('vimeo-' . $video_id);
                        $filename = $hashedName . '.jpg';
                        $videoObj = NULL;

                        if ($filename == $oldFile) {
                            $this->result['message'] = '
                                <div class="alert alert-error">
                                    <h4 class="alert-heading">Message</h4>
                                    <p>' . JText::_('COM_BT_MEDIA_FILE_EXIST') . '</p>
                                  </div>';
                            return $this->result;
                        }

                        if (!JFile::exists($params->imageTempSaveDir . '/' . $hashedName . '.jpg')) {
                            $urlFeed = 'http://vimeo.com/api/v2/video/' . $video_id . '.xml';
                            $video = @simplexml_load_file($urlFeed);
                            if ($video) {
                                $video_image = (string) $video->video->thumbnail_large;
                                $videoObj = new stdClass();
                                $content = file_get_contents($video_image);
                                file_put_contents($params->imageTempSaveDir . '/' . $filename, $content);
                                if (file_exists($params->imageTempSaveDir . '/' . $filename)) {
                                    Bt_mediaLegacyHelper::processImage($params->imageTempSaveDir . '/' . $filename, $params->imageTempSaveDir . '/thumb_' . $filename, $params->params->get('thumb_image_width', 200), $params->params->get('thumb_image_height', 130), $params->params->get('thumb_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                                    Bt_mediaLegacyHelper::processImage($params->imageTempSaveDir . '/' . $filename, $params->imageTempSaveDir . '/large_' . $filename, $params->params->get('image_width', 700), $params->params->get('image_height', 450), $params->params->get('large_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                                    $videoObj->name = (string) $video->video->title;
                                    $videoObj->video_path = $video_id;
                                    $videoObj->image_path = $filename;
                                    $videoObj->description = (string) $video->video->description;
                                    $videoObj->tags = (string) $video->video->tags;
                                    $videoObj->source_of_media = 'Vimeo Server';
                                    $videoObj->media_type = 'video';

                                    $file_path = array();
                                    $file_path[] = $filename . '|' . $oldFile;
                                    $file_path[] = '<img src="' . JUri::root() . $params->params->get('file_save', 'images/bt_media') . '/temp/images/large_' . $filename . '"/>';
                                    $file_path[] = $videoObj;

                                    $this->result['success'] = TRUE;
                                    $this->result['message'] = JText::_('Video had been get');
                                    $this->result['data'] = $file_path;
                                }
                            }
                        } else {
                            if (!JFile::exists($params->imageTempSaveDir . '/large_' . $filename)) {
                                Bt_mediaLegacyHelper::processImage($params->imageTempSaveDir . '/' . $filename, $params->imageTempSaveDir . '/large_' . $filename, $params->params->get('image_width', 700), $params->params->get('image_height', 450), $params->params->get('large_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                            }
                            if (!JFile::exists($params->imageTempSaveDir . '/thumb_' . $filename)) {
                                Bt_mediaLegacyHelper::processImage($params->imageTempSaveDir . '/' . $filename, $params->imageTempSaveDir . '/thumb_' . $filename, $params->thumbWidth, $params->thumbHeight, $params->params->get('thumb_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                            }
                            $urlFeed = 'http://vimeo.com/api/v2/video/' . $video_id . '.xml';
                            $video = simplexml_load_file($urlFeed);
                            $videoObj = new stdClass();
                            $videoObj->name = (string) $video->video->title;
                            $videoObj->video_path = $video_id;
                            $videoObj->image_path = $filename;
                            $videoObj->description = (string) $video->video->description;
                            $videoObj->tags = (string) $video->video->tags;
                            $videoObj->source_of_media = 'Vimeo Server';
                            $videoObj->media_type = 'video';

                            $file_path = array();
                            $file_path[] = $filename . '|' . $oldFile;
                            $file_path[] = '<img src="' . JUri::root() . $params->params->get('file_save', 'images/bt_media') . '/temp/images/large_' . $filename . '"/>';
                            $file_path[] = $videoObj;

                            $this->result['success'] = TRUE;
                            $this->result['message'] = JText::_('File exist on server but not exist on database');
                            $this->result['data'] = $file_path;
                        }
                    } else {
                        $this->result['message'] = '<div class="alert alert-note">
                                    <h4 class="alert-heading">Message</h4>
                                    <p>' . JText::_('COM_BT_MEDIA_VIDEO_VIMEO_ID_INVALID') . '</p>
                                  </div>';
                    }
                }
            } else {
                $this->result['message'] = '<div class="alert alert-note">
                                    <h4 class="alert-heading">Message</h4>
                                    <p>' . JText::_('Vimeo url invalid') . '</p>
                                  </div>';
            }
        } else {
            if (!JFactory::getUser()->authorise('media.get.image', 'com_bt_media')) {
                $this->result['message'] = '
                                <div class="alert alert-error">
                                    <h4 class="alert-heading">Message</h4>
                                    <p>' . JText::_('COM_BT_MEDIA_NOT_PERMISSION_GET_IMAGE_UPLOAD') . '</p>
                                  </div>';
                return $this->result;
            }
            $fileInfo = pathinfo($url);
            $allowImageExt = array('jpg', 'png', 'gif', 'jpeg');
            if (in_array(strtolower($fileInfo['extension']), $allowImageExt)) {
                $hashedName = md5('unknown-' . $fileInfo['filename']);
                $filename = $hashedName . '.' . $fileInfo['extension'];

                if ($filename == $oldFile) {
                    $this->result['message'] = '
                                <div class="alert alert-error">
                                    <h4 class="alert-heading">Message</h4>
                                    <p>' . JText::_('COM_BT_MEDIA_FILE_EXIST') . '</p>
                                  </div>';
                    return $this->result;
                }

                if (!JFile::exists($params->imageTempSaveDir . '/' . $filename)) {
                    $content = file_get_contents($url);
                    file_put_contents($params->imageTempSaveDir . '/' . $filename, $content);
                    if (file_exists($params->imageTempSaveDir . '/' . $filename)) {
                        Bt_mediaLegacyHelper::processImage($params->imageTempSaveDir . '/' . $filename, $params->imageTempSaveDir . '/large_' . $filename, $params->params->get('image_width', 700), $params->params->get('image_height', 450), $params->params->get('large_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                        Bt_mediaLegacyHelper::processImage($params->imageTempSaveDir . '/' . $filename, $params->imageTempSaveDir . '/thumb_' . $filename, $params->thumbWidth, $params->thumbHeight, $params->params->get('thumb_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                        $objFile = new stdClass();
                        $objFile->name = $fileInfo['filename'];
                        $objFile->image_path = $filename;
                        $objFile->description = '';
                        if ($source_server && $media_type == "video") {
                            $objFile->source_of_media = $source_server;
                        } else {
                            $objFile->source_of_media = $urlinfo['host'];
                        }
                        $objFile->tags = '';
                        if ($media_type) {
                            $objFile->media_type = $media_type;
                        } else {
                            $objFile->media_type = 'image';
                        }

                        $file_path = array();
                        $file_path[] = $filename . '|' . $oldFile;
                        $file_path[] = '<img src="' . JUri::root() . $params->params->get('file_save', 'images/bt_media') . '/temp/images/large_' . $filename . '"/>';
                        $file_path[] = $objFile;

                        $this->result['success'] = TRUE;
                        $this->result['message'] = JText::_("File get success");
                        $this->result['data'] = $file_path;
                    } else {
                        $this->result['message'] = '<div class="alert alert-note">
                                    <h4 class="alert-heading">Message</h4>
                                    <p>' . JText::_("File get fail") . '</p>
                                  </div>';
                    }
                } else {
                    if (!JFile::exists($params->imageTempSaveDir . '/large_' . $filename)) {
                        Bt_mediaLegacyHelper::processImage($params->imageTempSaveDir . '/' . $filename, $params->imageTempSaveDir . '/large_' . $filename, $params->params->get('image_width', 700), $params->params->get('image_height', 450), $params->params->get('large_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                    }
                    if (!JFile::exists($params->imageTempSaveDir . '/thumb_' . $filename)) {
                        Bt_mediaLegacyHelper::processImage($params->imageTempSaveDir . '/' . $filename, $params->imageTempSaveDir . '/thumb_' . $filename, $params->thumbWidth, $params->thumbHeight, $params->params->get('thumb_image_process_type', 'crop'), $params->params->get('image_quality', 100));
                    }
                    $objFile = new stdClass();
                    $objFile->name = $fileInfo['filename'];
                    $objFile->image_path = $filename;
                    $objFile->description = '';
                    if ($source_server && $media_type == "video") {
                        $objFile->source_of_media = $source_server;
                    } else {
                        $objFile->source_of_media = $urlinfo['host'];
                    }
                    $objFile->tags = '';
                    if ($media_type) {
                        $objFile->media_type = $media_type;
                    } else {
                        $objFile->media_type = 'image';
                    }

                    $file_path = array();
                    $file_path[] = $filename . '|' . $oldFile;
                    $file_path[] = '<img src="' . JUri::root() . $params->params->get('file_save', 'images/bt_media') . '/temp/images/large_' . $filename . '"/>';
                    $file_path[] = $objFile;

                    $this->result['success'] = TRUE;
                    $this->result['message'] = JText::_("File get success");
                    $this->result['data'] = $file_path;
                }
            } else {
                $this->result['message'] = '
                                <div class="alert alert-note">
                                    <h4 class="alert-heading">Message</h4>
                                    <p>' . JText::_('Please input a direct image url.') . '</p>
                                  </div>';
            }
        }
        return $this->result;
    }

    private function changeFileName($session_name, $file, $name) {
        $change = false;
        $sessions = JFactory::getSession();
        $objFiles = $sessions->get($session_name);
        if ($objFiles != null) {
            $arrFile = array();
            foreach ($objFiles as $objFile) {
                if ((string) $objFile->image_path == $file) {
                    $objFile->name = $name;
                    $change = true;
                }
                $arrFile[] = $objFile;
            }
            $sessions->set($session_name, $arrFile);
        }
        return $change;
    }

    private function deleteFileInSession($session_name, $filename, $params) {
        $delete = false;
        $objFiles = $params->sessions->get($session_name);
        if ($objFiles != null) {
            foreach ($objFiles as $objFile) {
                if ((string) $objFile->image_path == $filename) {
                    $item_type = $objFile->media_type;
                    $orignFile = $params->originalSaveDir . '/' . $filename;
                    $thumbFile = $params->thumbSaveDir . '/' . $filename;
                    $largeFile = $params->largeSaveDir . '/' . $filename;

                    if ($item_type == "video") {
                        $videoFileName = $objFile->video_path;
                        $videoFIlePath = $params->videoOriginalSaveDir . '/' . $videoFileName;
                        if (JFile::exists($videoFIlePath))
                            unlink($videoFIlePath);
                    }
                    $index = array_search($objFile, $objFiles);
                    unset($objFiles[$index]);
                    if (JFile::exists($largeFile))
                        unlink($largeFile);
                    if (JFile::exists($thumbFile))
                        unlink($thumbFile);
                    if (JFile::exists($orignFile))
                        unlink($orignFile);
                    $delete = true;
                    break;
                }
            }
            $params->sessions->set($session_name, $objFiles);
        }
        return $delete;
    }

    public function obEndClear() {
        $obLevel = ob_get_level();
        while ($obLevel > 0) {
            ob_end_clean();
            $obLevel--;
        }
    }

}
