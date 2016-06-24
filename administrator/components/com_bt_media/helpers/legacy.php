<?php

/**
 * This class is used to check version of joomla and work something in different
 * @package             bt_portfolio - BT Portfolio Component
 * @version		2.0
 * @created		Feb 2012
 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright           Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// No direct access to this file
defined('_JEXEC') or die;
require_once JPATH_ADMINISTRATOR . '/components/com_bt_media/helpers/images.php';

class Bt_mediaLegacyHelper {

    /**
     * Load behavior mootool
     * 
     */
    public static function isLegacy() {
        if (version_compare(JVERSION, '3.0', 'ge')) {
            return false;
        } else {
            return true;
        }
    }

    public static function getController() {
        if (version_compare(JVERSION, '3.0', 'ge')) {

            return JControllerLegacy::getInstance('Bt_portfolio');
        } else {
            return JController::getInstance('Bt_portfolio');
        }
    }

    public static function getBrowser() {
        $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
        // you can add different browsers with the same way ..
        if (preg_match('/(chromium)[ \/]([\w.]+)/', $ua))
            $browser = 'chromium';
        elseif (preg_match('/(chrome)[ \/]([\w.]+)/', $ua))
            $browser = 'chrome';
        elseif (preg_match('/(safari)[ \/]([\w.]+)/', $ua))
            $browser = 'safari';
        elseif (preg_match('/(opera)[ \/]([\w.]+)/', $ua))
            $browser = 'opera';
        elseif (preg_match('/(msie)[ \/]([\w.]+)/', $ua))
            $browser = 'msie';
        elseif (preg_match('/(mozilla)[ \/]([\w.]+)/', $ua))
            $browser = 'mozilla';

        preg_match('/(' . $browser . ')[ \/]([\w]+)/', $ua, $version);

        return array('name' => $browser, 'version' => $version[2]);
    }

    public static function getCategoryOptions($filter = false) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('id as value,name as text,parent_id,ordering, created_by');
        $query->from('#__bt_media_categories');
        $query->where('state=1');
        $db->setQuery($query);
        $categories = $db->loadObjectList();
        return self::MakeTree($categories, $filter);
    }

    static function MakeTree($categories, $filter, $id = 0) {
        $tree = array();
        $tree = self::TreeTitle($categories, $tree, 0);
        $tree_array = array();
        $user = JFactory::getUser();
        if ($id > 0) {
            $tree_sub = array();
            $id_sub = '';
            $subcategories = self::SubTree($categories, $tree_sub, 0, $id_sub);
            foreach ($subcategories as $key0 => $value0) {
                $subcategories_array[$key0] = explode(',', $value0);
            }

            foreach ($tree as $key => $value) {

                foreach ($categories as $key2 => $value2) {
                    $syntax_check = 1;

                    if ($id == $key) {
                        $syntax_check = 0;
                    }

                    foreach ($subcategories_array as $key3 => $value3) {
                        foreach ($value3 as $key4 => $value4) {
                            if ($value4 == $id && $key == $key3) {
                                $syntax_check = 0;
                            }
                        }
                    }

                    if ($syntax_check == 1) {
                        if ($filter) {
                            if ($user->authorise('core.create', 'com_bt_media.category.' . $key) || $user->authorise('core.edit', 'com_bt_media.category.' . $key) || ($user->id == $value2->created_by && $user->authorise('core.edit.own', 'com_bt_media.category.' . $key)) || $user->authorise('core.delete', 'com_bt_media.category.' . $key) || ($user->id == $value2->created_by && $user->authorise('media.delete.own', 'com_bt_media.category.' . $key))) {
                                if ($key == $value2->value) {
                                    $tree_object = new JObject();
                                    $tree_object->text = $value;
                                    $tree_object->value = $key;
                                    $tree_array[] = $tree_object;
                                }
                            }
                        } else {
                            if ($key == $value2->value) {
                                $tree_object = new JObject();
                                $tree_object->text = $value;
                                $tree_object->value = $key;
                                $tree_array[] = $tree_object;
                            }
                        }
                    }
                }
            }
        } else {
            foreach ($tree as $key => $value) {
                foreach ($categories as $key2 => $value2) {
                    if ($key == $value2->value) {
                        if ($filter) {
                            if ($user->authorise('core.create', 'com_bt_media.category.' . $key) || $user->authorise('core.edit', 'com_bt_media.category.' . $key) || ($user->id == $value2->created_by && $user->authorise('core.edit.own', 'com_bt_media.category.' . $key)) || $user->authorise('core.delete', 'com_bt_media.category.' . $key) || ($user->id == $value2->created_by && $user->authorise('media.delete.own', 'com_bt_media.category.' . $key))) {
                                $tree_object = new JObject();
                                $tree_object->text = $value;
                                $tree_object->value = $key;
                                $tree_array[] = $tree_object;
                            }
                        } else {
                            $tree_object = new JObject();
                            $tree_object->text = $value;
                            $tree_object->value = $key;
                            $tree_array[] = $tree_object;
                        }
                    }
                }
            }
        }
        return $tree_array;
    }

    static function TreeTitle($data, $tree, $id = 0, $text = '') {

        foreach ($data as $key) {
            $show_text = $text . $key->text;
            if ($key->parent_id == $id) {
                $tree[$key->value] = $show_text;
                $tree = self::TreeTitle($data, $tree, $key->value, $text . " -- ");
                // &raquo;
            }
        }
        return ($tree);
    }

    static function SubTree($data, $tree, $id = 0, $id_sub = '') {
        foreach ($data as $key) {
            $show_id_sub = $id_sub . $key->value;
            if ($key->parent_id == $id) {
                $tree[$key->value] = $id_sub;
                $tree = self::SubTree($data, $tree, $key->value, $show_id_sub . ",");
            }
        }
        return ($tree);
    }

    public static function prepareTagsData() {
        $tags = array();
        $db = JFactory::getDbo();
        $query = $db->getQuery(TRUE);
        $query->select('id, name');
        $query->from('#__bt_media_tags');
        $query->where('state=1');
        $db->setQuery($query);
        $data = $db->loadObjectList();
        foreach ($data as $tag) {
            $tags[] = '{value: "' . $tag->name . '", data: "' . $tag->id . '"}';
        }
        return implode(",", $tags);
    }

    public static function getAllTagsItem($item_id) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(TRUE);
        $query->from('#__bt_media_tags_xref AS a ');

        $query->select('tag.id, tag.name, tag.alias');
        $query->join('INNER', '#__bt_media_tags AS tag ON tag.id = a.tag_id');
        $query->where('a.item_id=' . (int) $item_id);
        $query->where('tag.state = 1');
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public static function processImage($file, $new_file, $param_width, $param_height, $process_type, $quality = 100) {
        $imageObj = new BTMediaImageHelper();
        $params = JComponentHelper::getParams('com_bt_media');
        $imageObj->loadImage($file);
        if ($process_type == 'none') {
            copy($file, $new_file);
        }
        if ($process_type == 'crop') {
            $crop_position = $params->get('image_crop_position', 'crop_center');
            $imageObj->resize($new_file, $param_width, $param_height, $quality, TRUE, $crop_position);
        }
        if ($process_type == 'resizekeepratio') {
            list($iWidth, $iHeight) = getimagesize($file);
            if ($iWidth > $iHeight) {
                $imageObj->resizeToWidth($new_file, $param_width, $quality);
            } else {
                $imageObj->resizeToHeight($new_file, $param_height, $quality);
            }
        }
        if ($process_type == 'resize') {
            $imageObj->resize($new_file, $param_width, $param_height, $quality);
        }
        if ($process_type == 'fixedwidth') {
            $imageObj->resizeToWidth($new_file, $param_width, $quality);
        }
        if ($process_type == 'fixedheight') {
            $imageObj->resizeToHeight($new_file, $param_height, $quality);
        }
    }

    public static function createAlias($title, $forItem, $key = NULL, $isCoppy = FALSE) {
        require_once 'unicodetoascii.php';
        if (class_exists('unicodetoascii')) {
            $calias = new unicodetoascii();
            $alias = $calias->asciiAliasCreate($title);
        } else {
            $alias = JFilterOutput::stringURLSafe($title);
        }
        if ($isCoppy) {
            $newAlias = explode('-', $alias);
            if (count($newAlias) > 1 && is_numeric(end($newAlias))) {
                unset($newAlias[count($newAlias) - 1]);
            }
            $alias = implode('-', $newAlias);
        }
        if ($forItem == 'category') {
            $listAlias = self::getAllCatAlias($alias);
        }
        if ($forItem == 'media') {
            $listAlias = self::getAllMediaAlias($alias);
        }
        if ($forItem == 'tag') {
            $listAlias = self::getAllTagAlias($alias);
        }

        if ($key == 0) {
            if (count($listAlias) > 0) {
                $alias = self::generateAlias($alias, $listAlias);
            }
        } else {
            if (!$isCoppy) {
                if (count($listAlias) == 1 && $key != $listAlias[0]['id']) {
                    $alias = self::generateAlias($alias, $listAlias);
                } elseif (count($listAlias) > 1) {
                    $alias = self::generateAlias($alias, $listAlias);
                }
            } else {
                if (count($listAlias) > 0) {
                    $alias = self::generateAlias($alias, $listAlias);
                }
            }
        }
        return $alias;
    }

    static function generateAlias($alias, $listAlias) {
        $listEndAlias = array();
        foreach ($listAlias as $value) {
            $parseAlias = explode("-", $value['alias']);
            if (is_numeric(end($parseAlias))) {
                $listEndAlias[] = end($parseAlias);
            }
        }
        if (empty($listEndAlias)) {
            $alias = $alias . '-2';
        } else {
            $endmax = max($listEndAlias);
            $alias = $alias . '-' . ($endmax + 1);
        }
        return $alias;
    }

    static function getAllCatAlias($alias) {
        $db = JFactory::getDbo();
        $rs = array();
        $query = $db->getQuery(true);
        $query->select('id, alias');
        $query->from('#__bt_media_categories');
        $query->where("alias LIKE '" . $alias . "%'");
        $db->setQuery($query);
        $loadResult = $db->loadRowList();
        foreach ($loadResult as $value) {
            $rs[] = array('id' => $value[0], 'alias' => $value[1]);
        }
        return $rs;
    }

    static function getAllMediaAlias($alias) {
        $db = JFactory::getDbo();
        $rs = array();
        $query = $db->getQuery(true);
        $query->select('id, alias');
        $query->from('#__bt_media_items');
        $query->where("alias LIKE '" . $alias . "%'");
        $db->setQuery($query);
        $loadResult = $db->loadRowList();
        foreach ($loadResult as $value) {
            $rs[] = array('id' => $value[0], 'alias' => $value[1]);
        }
        return $rs;
    }

    static function getAllTagAlias($alias) {
        $db = JFactory::getDbo();
        $rs = array();
        $query = $db->getQuery(true);
        $query->select('id, alias');
        $query->from('#__bt_media_tags');
        $query->where("alias LIKE '" . $alias . "%'");
        $db->setQuery($query);
        $loadResult = $db->loadRowList();
        foreach ($loadResult as $value) {
            $rs[] = array('id' => $value[0], 'alias' => $value[1]);
        }
        return $rs;
    }

    public static function sessionStore($session_name, $value) {
        $sessions = JFactory::getSession();
        $sessionTemp = $sessions->get($session_name);
        $sessionTemp[count($sessionTemp)] = $value;
        $sessions->set($session_name, $sessionTemp);
    }

    public static function checkFileInSession($session_name, $file_path) {
        $is_exist = false;
        $sessions = JFactory::getSession();
        $objFiles = $sessions->get($session_name);
        if ($objFiles != null) {
            foreach ($objFiles as $objFile) {
                if ((string) $objFile->image_path == $file_path) {
                    $is_exist = TRUE;
                    break;
                }
            }
        }
        return $is_exist;
    }

    public static function checkFileOnDB($file_name) {
        $is_exist = FALSE;
        $db = JFactory::getDbo();
        $query = $db->getQuery(TRUE);
        $query->select('a.id, a.image_path, a.video_path');
        $query->from('#__bt_media_items as a');
        $query->where("a.image_path='" . $file_name . "'");
        $db->setQuery($query);
        $result = $db->loadObjectList();
        if ($result != NULL) {
            $is_exist = TRUE;
        }
        return $is_exist;
    }

    public static function checkTag($tag_name) {
        $is_exist = FALSE;
        $db = JFactory::getDbo();
        $query = $db->getQuery(TRUE);
        $query->select('a.id, a.name');
        $query->from('#__bt_media_tags as a');
        $query->where("a.name='" . $tag_name . "'");
        $db->setQuery($query);
        $result = $db->loadObjectList();
        if ($result != NULL) {
            $is_exist = TRUE;
        }
        return $is_exist;
    }

    public static function saveTagXref($tag_id, $item_id) {
        $data = new stdClass();
        $data->id = NULL;
        $data->tag_id = $tag_id;
        $data->item_id = $item_id;
        $db = JFactory::getDbo();
        $db->insertObject('#__bt_media_tags_xref', $data, $data->id);
    }

    public static function saveTag($tag_name) {
        $user = JFactory::getUser();
        $data = new stdClass();
        $data->id = NULL;
        $data->created_by = $user->id;
        $data->name = $tag_name;
        $data->state = '1';
        $data->language = '*';
        $data->access = '1';
        $data->created_date = JFactory::getDate()->calendar('Y-m-d H:i:s');
        $data->alias = self::createAlias($tag_name, 'tag');
        $data->description = '';
        $data->params = array('page_title' => '', 'metakey' => '', 'metadesc' => '', 'robots' => '');

        $db = JFactory::getDbo();
        $ins = $db->insertObject('#__bt_media_tags', $data, $data->id);
        if ($ins) {
            return $db->insertid();
        } else {
            return null;
        }
    }

    public static function getMediaItemsById($id) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(TRUE);
        $query->select('*');
        $query->from('#__bt_media_items');
        $query->where('id="' . $id . '"');
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public static function clearTagsItem($item_id) {
        $db = JFactory::getDbo();
        $db->setQuery(
                'DELETE FROM #__bt_media_tags_xref' .
                ' WHERE item_id = ' . (int) $item_id
        );
        if (!$db->query()) {
            self::setError($db->getErrorMsg());
            return false;
        }

        return true;
    }

    public static function getAllTagsMenu($item_id) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(TRUE);
        $query->select('link');
        $query->from('#__menu');
        $query->where('id="' . $item_id . '"');
        $db->setQuery($query);
        $link = $db->loadResult();
        $tagp = explode('=', $link);
        $tagnames = end($tagp);
        return $tagnames;
    }

    public static function getTagByName($name) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(TRUE);
        $query->select('id');
        $query->from('#__bt_media_tags');
        $query->where('name="' . $name . '"');
        $db->setQuery($query);
        return $db->loadResult();
    }

    public static function saveItemTags($itemid, $tags) {
        $tag_names = explode(",", $tags);
        self::clearTagsItem($itemid);
        foreach ($tag_names as $tname) {
            if ($tname != '') {
                $tag_id = self::getTagByName(trim(str_replace(array('\\', '/'), '', $tname)));
                if ($tag_id != NULL) {
                    self::saveTagXref($tag_id, $itemid);
                } else {
                    $tag_id = self::saveTag(trim(str_replace(array('\\', '/'), '', $tname)));
                    if ($tag_id != NULL) {
                        self::saveTagXref($tag_id, $itemid);
                    }
                }
            }
        }
    }

    public static function getCategoryLevel($catid) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(TRUE);
        $query->select('level');
        $query->from('#__bt_media_categories');
        $query->where('id=' . $catid);
        $db->setQuery($query);
        return $db->loadResult();
    }

    public static function createHTML($id, $m_path, $name, $session_name, $params) {
        $html = array();
        $html[] = '<li style="display:none;" class="image" id="' . $id . '">';
        $html[] = '<img src="' . JURI::root() . $params->get('file_save', 'images/bt_media') . '/images/thumbnail/' . $m_path . '" />';
        $html[] = '<img class="img-delete" onclick="removeImage(this)" src="' . JURI::base() . 'components/com_bt_media/assets/images/delete.png" />';
        $html[] = '<input type="hidden" name="image_filename" value="' . $m_path . '" />';
        $html[] = '<input type="hidden" name="old_name" value="' . htmlspecialchars($name) . '" />';
        $html[] = '<input type="hidden" name="session_name" value="' . $session_name . '" />';
        $html[] = '<div class="edit-title"><input class="input-title" type="text" name="image_title" value="' . htmlspecialchars($name) . '" onblur="changeName(this)"></div>';
        $html[] = '</li>';

        return implode($html);
    }

    public static function getFileInFolder($dir, &$files, $flag = 0, $ext = array('png', 'jpg', 'jpeg', 'gif', 'mp4', 'flv')) {
        $fileInDir = glob($dir . '*');
        if ($flag == 0) {
            foreach ($fileInDir as $file) {
                if (is_file($file)) {
                    $fileExtParse = explode('.', $file);
                    $fileExt = end($fileExtParse);
                    if (in_array(strtolower($fileExt), $ext)) {
                        array_push($files, $file);
                    }
                }
            }
        }
        if ($flag == 1) {
            foreach ($fileInDir as $file) {
                if (is_file($file)) {
                    $fileExtParse = explode('.', $file);
                    $fileExt = end($fileExtParse);
                    if (in_array(strtolower($fileExt), $ext)) {
                        array_push($files, $file);
                    }
                }
                if (is_dir($file)) {
                    $sdir = $file . DIRECTORY_SEPARATOR;
                    self::getFileInFolder($sdir, $files, 1, $ext);
                }
            }
        }
        return $files;
    }

    public static function youtubeGetVideoFromPlaylist($playlistId, $params) {
        $pageToken = '';
        $list_video = array();
        do {
            $url = 'https://www.googleapis.com/youtube/v3/playlistItems?key=' . $params->get('google_api') . '&part=snippet&playlistId=' . $playlistId . '&maxResults=50';
            if($pageToken){
                $url .= '&pageToken=' . $pageToken;
            }
            $rsp = self::callCURL($url);
            $data = json_decode($rsp);
            if (isset($data->error)) {
                return false;
            }
            if (isset($data->items) && count($data->items)) {
                foreach ($data->items as $item) {
                    $list_video[] = $item->snippet->resourceId->videoId;

                }
            }
            $pageToken = isset($data->nextPageToken) ? $data->nextPageToken : '';
        }while($pageToken);
        return $list_video;
    }


    public static function youtubeGetVideoByUser($username, $params) {

        $pl_info = @simplexml_load_file("http://gdata.youtube.com/feeds/api/users/" . $username . "/uploads");
        $list_video = array();
        if ($pl_info) {
            $videos = $pl_info->entry;
            foreach ($videos as $video) {
                $video_link = $video->id;
                $parser_link = explode("/", $video_link);
                $videoid = $parser_link[count($parser_link) - 1];
                if ($videoid != "") {
                    $list_video[] = $videoid;
                }
            }
        }

        return $list_video;
    }

    public static function youtubeGetVideo($video_id, $params) {
        $saveDir = JPATH_SITE . DIRECTORY_SEPARATOR . $params->get('file_save', 'images/bt_media');
        $result = array('success' => false, 'message' => '', 'data' => null);
        $hashedName = md5('youtube-' . $video_id);
        $filename = $hashedName . '.jpg';
        $videoObj = NULL;
        if (!JFile::exists($saveDir . '/images/original/' . $filename)) {

            $urlFeed = 'https://www.googleapis.com/youtube/v3/videos?key=' . $params->get('google_api', '') . '&part=snippet&id=' . $video_id;
            $rsp = self::callCURL($urlFeed);
            $data = json_decode($rsp);
            if(isset($data->error)){
                $result['message'] = $data->error->message;
                return;
            }
            if(isset($data->items[0])) {
                $video = $data->items[0];
                $videoObj = new stdClass();
                $tags = get_meta_tags('http://www.youtube.com/watch?v=' . $video_id);
                if (copy($video->snippet->thumbnails->high->url, $saveDir . '/images/original/' . $filename)) {
                    if (file_exists($saveDir . '/images/original/' . $filename)) {
                        self::processImage($saveDir . '/images/original/' . $filename, $saveDir . '/images/thumbnail/' . $filename, $params->get('thumb_image_width', 200), $params->get('thumb_image_height', 130), $params->get('thumb_image_process_type', 'crop'), $params->get('image_quality', 100));
                        self::processImage($saveDir . '/images/original/' . $filename, $saveDir . '/images/large/' . $filename, $params->get('image_width', 700), $params->get('image_height', 450), $params->get('large_image_process_type', 'crop'), $params->get('image_quality', 100));
                        $videoObj->name = $video->snippet->title;;
                        $videoObj->video_path = $video_id;
                        $videoObj->image_path = $filename;
                        $videoObj->description = $video->snippet->description;
                        $videoObj->tags = $tags['keywords'];
                        $videoObj->source_of_media = 'Youtube Server';
                        $videoObj->media_type = 'video';
                        self::sessionStore('video_youtube', $videoObj);
                        $rs_html = self::createHTML($hashedName, $videoObj->image_path, $videoObj->name, 'video_youtube', $params);

                        $result['success'] = TRUE;
                        $result['message'] = JText::_('File get success');
                        $result['data'] = $rs_html;
                    }
                } else {
                    $result['message'] = JText::_('File not found');
                }
            }else {
                $result['message'] = JText::_('This video has been removed by the user.');
            }
        } else {
            if (self::checkFileOnDB($filename) || self::checkFileInSession('video_youtube', $filename)) {
                $result['message'] = JText::_('File exist');
            } else {
                if (!JFile::exists($saveDir . '/images/large/' . $filename)) {
                    self::processImage($saveDir . '/images/original/' . $filename, $saveDir . '/images/large/' . $filename, $params->get('image_width', 700), $params->get('image_height', 450), $params->get('large_image_process_type', 'crop'), $params->get('image_quality', 100));
                }
                if (!JFile::exists($saveDir . '/images/thumbnail/' . $filename)) {
                    self::processImage($saveDir . '/images/original/' . $filename, $saveDir . '/images/thumbnail/' . $filename, $params->get('thumb_image_width', 200), $params->get('thumb_image_height', 130), $params->get('thumb_image_process_type', 'crop'), $params->get('image_quality', 100));
                }

                $urlFeed = 'https://www.googleapis.com/youtube/v3/videos?key=' . $params->get('google_api', '') . '&part=snippet&id=' . $video_id;
                $rsp = self::callCURL($urlFeed);
                $data = json_decode($rsp);
                if(isset($data->error)){
                    $result['message'] = $data->error->message;
                    return;
                }

                if(isset($data->items[0])) {
                    $video = $data->items[0];
                    $videoObj = new stdClass();
                    $tags = get_meta_tags('http://www.youtube.com/watch?v=' . $video_id);
                    $videoObj->name = $video->snippet->title;;
                    $videoObj->video_path = $video_id;
                    $videoObj->image_path = $filename;
                    $videoObj->tags = $tags['keywords'];
                    $videoObj->source_of_media = 'Youtube Server';
                    $videoObj->media_type = 'video';
                    self::sessionStore('video_youtube', $videoObj);
                    $rs_html = self::createHTML($hashedName, $videoObj->image_path, $videoObj->name, 'video_youtube', $params);
                    $result['success'] = TRUE;
                    $result['message'] = JText::_('File exist on server but not exist on database');
                    $result['data'] = $rs_html;
                } else {
                    $result['message'] = JText::_('This video has been removed by the user.');
                }
            }
        }
        return $result;
    }

    public static function flickrGetVideo($flickrObj, $params) {
        $saveDir = JPATH_SITE . DIRECTORY_SEPARATOR . $params->get('file_save', 'images/bt_media');
        $result = array('success' => false, 'message' => '', 'data' => null);
        $hashedName = md5('flickr-' . $flickrObj['id']);
        $filename = $hashedName . '.jpg';
        $videoObj = NULL;
        if (!JFile::exists($saveDir . '/images/original/' . $filename)) {
            $videoObj = new stdClass();
            $video_image = 'http://farm' . $flickrObj['farm'] . '.staticflickr.com/' . $flickrObj['server'] . '/' . $flickrObj['id'] . '_' . $flickrObj['secret'] . '_b.jpg';
            if (copy($video_image, $saveDir . '/images/original/' . $filename)) {
                if (file_exists($saveDir . '/images/original/' . $filename)) {
                    self::processImage($saveDir . '/images/original/' . $filename, $saveDir . '/images/thumbnail/' . $filename, $params->get('thumb_image_width', 200), $params->get('thumb_image_height', 130), $params->get('thumb_image_process_type', 'crop'), $params->get('image_quality', 100));
                    self::processImage($saveDir . '/images/original/' . $filename, $saveDir . '/images/large/' . $filename, $params->get('image_width', 700), $params->get('image_height', 450), $params->get('large_image_process_type', 'crop'), $params->get('image_quality', 100));
                    $videoObj->name = $flickrObj['title'];
                    $videoObj->video_path = $flickrObj['id'];
                    $videoObj->image_path = $filename;
                    $videoObj->description = $flickrObj['description'];
                    $videoObj->source_of_media = 'Flickr Server';
                    $videoObj->media_type = 'video';
                    self::sessionStore('images_flickr', $videoObj);

                    $rs_html = self::createHTML($hashedName, $videoObj->image_path, $videoObj->name, 'images_flickr', $params);

                    $result['success'] = TRUE;
                    $result['message'] = JText::_('File get success');
                    $result['data'] = $rs_html;
                }
            } else {
                $result['message'] = JText::_('File not found');
            }
        } else {
            if (self::checkFileOnDB($filename) || self::checkFileInSession('images_flickr', $filename)) {
                $result['message'] = JText::_('File exist');
            } else {
                if (!JFile::exists($saveDir . '/images/large/' . $filename)) {
                    self::processImage($saveDir . '/images/original/' . $filename, $saveDir . '/images/large/' . $filename, $params->get('image_width', 700), $params->get('image_height', 450), $params->get('large_image_process_type', 'crop'), $params->get('image_quality', 100));
                }
                if (!JFile::exists($saveDir . '/images/thumbnail/' . $filename)) {
                    self::processImage($saveDir . '/images/original/' . $filename, $saveDir . '/images/thumbnail/' . $filename, $params->get('thumb_image_width', 200), $params->get('thumb_image_height', 130), $params->get('thumb_image_process_type', 'crop'), $params->get('image_quality', 100));
                }
                $videoObj = new stdClass();
                $videoObj->name = $flickrObj['title'];
                $videoObj->video_path = $flickrObj['id'];
                $videoObj->image_path = $filename;
                $videoObj->description = $flickrObj['description'];
                $videoObj->source_of_media = 'Flickr Server';
                $videoObj->media_type = 'video';
                self::sessionStore('images_flickr', $videoObj);

                $rs_html = self::createHTML($hashedName, $videoObj->image_path, $videoObj->name, 'images_flickr', $params);

                $result['success'] = TRUE;
                $result['message'] = JText::_('File exist on server but not exist on database');
                $result['data'] = $rs_html;
            }
        }
        return $result;
    }

    /**
     *
     * Get videos from Vimeo Albums
     * Change to use new Vimeo API
     *
     * @param $userId Vimeo user id
     * @param $albumId Vimeo album id
     * @return array
     * @since 1.3
     */
    public static function vimeoGetVideoFromAlbum($userId, $albumId) {
        if(!isset($userId)){
            $urlFeed = 'http://vimeo.com/api/v2/album/' . $albumId . '/info.xml';
            $album = @simplexml_load_file($urlFeed);

            $userId = (string) $album->album->user_id;
        }

        $params = JComponentHelper::getParams('com_bt_media');
        require(JPATH_BASE . '/components/com_bt_media/lib/Vimeo/autoload.php');
        $vimeoLib = new \Vimeo\Vimeo($params->get('vimeo_client_id'), $params->get('vimeo_client_secret'));
        $token = $vimeoLib->clientCredentials(array('public'));
        $vimeoLib->setToken($token['body']['access_token']);
        $page = 0;
        $listVideos = array();
        do{
            $page++;
            $rsp = $vimeoLib->request('/users/' . $userId . '/albums/' . $albumId . '/videos', array('page' => $page, 'per_page' => 50), 'GET');
            $rsp = $rsp['body'];
            if($rsp && isset($rsp['data']) && count($rsp['data'])){
                foreach($rsp['data'] as $video){
                    $videoURI = $video['uri'];
                    $videoURI = str_replace('/videos/', '', $videoURI);
                    $listVideos[] = $videoURI;
                }
            }
        } while($rsp['paging']['next']);

        return $listVideos;
    }

    public static function vimeoGetVideo($video_id, $params) {
        $saveDir = JPATH_SITE . DIRECTORY_SEPARATOR . $params->get('file_save', 'images/bt_media');
        $result = array('success' => false, 'message' => '', 'data' => null);
        $hashedName = md5('vimeo-' . $video_id);
        $filename = $hashedName . '.jpg';
        $videoObj = NULL;
        if (!JFile::exists($saveDir . '/images/original/' . $hashedName . '.jpg')) {
            $urlFeed = 'http://vimeo.com/api/v2/video/' . $video_id . '.xml';
            $video = @simplexml_load_file($urlFeed);
            if ($video) {
                $video_image = (string) $video->video->thumbnail_large;
                $videoObj = new stdClass();
                $content = file_get_contents($video_image);
                file_put_contents($saveDir . '/images/original/' . $filename, $content);
                if (file_exists($saveDir . '/images/original/' . $filename)) {
                    self::processImage($saveDir . '/images/original/' . $filename, $saveDir . '/images/large/' . $filename, $params->get('image_width', 700), $params->get('image_height', 450), $params->get('large_image_process_type', 'crop'), $params->get('image_quality', 100));
                    self::processImage($saveDir . '/images/original/' . $filename, $saveDir . '/images/thumbnail/' . $filename, $params->get('thumb_image_width', 200), $params->get('thumb_image_height', 130), $params->get('thumb_image_process_type', 'crop'), $params->get('image_quality', 100));
                    $videoObj->name = (string) $video->video->title;
                    $videoObj->video_path = $video_id;
                    $videoObj->image_path = $filename;
                    $videoObj->description = (string) $video->video->description;
                    $videoObj->tags = (string) $video->video->tags;
                    $videoObj->source_of_media = 'Vimeo Server';
                    $videoObj->media_type = 'video';

                    self::sessionStore('video_vimeo', $videoObj);

                    $rs_html = self::createHTML($hashedName, $videoObj->image_path, $videoObj->name, 'video_vimeo', $params);

                    $result['success'] = TRUE;
                    $result['message'] = JText::_('Video had been get');
                    $result['data'] = $rs_html;
                }
            }
        } else {
            if (self::checkFileOnDB($filename) || self::checkFileInSession('video_vimeo', $filename)) {
                $result['message'] = JText::_('File exist');
            } else {
                if (!JFile::exists($saveDir . '/images/large/' . $hashedName . '.jpg')) {
                    self::processImage($saveDir . '/images/original/' . $filename, $saveDir . '/images/large/' . $filename, $params->get('image_width', 700), $params->get('image_height', 450), $params->get('large_image_process_type', 'crop'), $params->get('image_quality', 100));
                }
                if (!JFile::exists($saveDir . '/images/thumbnail/' . $hashedName . '.jpg')) {
                    self::processImage($saveDir . '/images/original/' . $filename, $saveDir . '/images/thumbnail/' . "/" . $filename, $params->get('thumb_image_width', 200), $params->get('thumb_image_height', 130), $params->get('thumb_image_process_type', 'crop'), $params->get('image_quality', 100));
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
                self::sessionStore('video_vimeo', $videoObj);

                $rs_html = self::createHTML($hashedName, $videoObj->image_path, $videoObj->name, 'video_vimeo', $params);

                $result['success'] = TRUE;
                $result['message'] = JText::_('File exist on server but not exist on database');
                $result['data'] = $rs_html;
            }
        }
        return $result;
    }

    public static function getFileExtension($file) {
        $extension = explode('.', $file);
        return strtolower($extension[count($extension) - 1]);
    }

    public static function getSharePlugin($item, $params) {
        $row = new stdClass();
        $row->title = $item->name;
        $row->link = JFactory::getURI()->toString(array('scheme', 'host', 'port')) . JRoute::_('index.php?option=com_bt_media&view=detail&id=' . $item->id . ':' . $item->alias . '&cat_rel=' . $item->cate_id);
        $row->image = JUri::root() . $params->get('file_save', 'images/bt_media') . '/images/thumbnail/' . $item->image_path;
        $row->description = $item->description;
        JPluginHelper::importPlugin('content');
        $sharePlugin = array('buttons' => '', 'comment' => '', 'script' => '');
        if (class_exists('plgContentBt_socialshare')) {
            $sharePlugin = plgContentBt_socialshare::socialButtons($row, false);
        }
        return $sharePlugin;
    }

    public static function checkOS($os) {
        $isOk = false;
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        if (strpos(strtolower($user_agent), $os))
            $isOk = true;
        return $isOk;
    }

    /**
     * Get response from an URL by cURL
     * @param $url
     * @return string
     * @since 1.3
     */
    public static function callCURL($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $rsp = curl_exec($ch);
        curl_close($ch);
        return $rsp;
    }

    /**
     * Get Youtube Channel information by using username
     * @return stdClass Channel object
     * @param $username Youtube user name
     * @since 1.3
     */
    public static function getYoutubeChannelByUsername($username){
        $params = JComponentHelper::getParams('com_bt_media');
        $url = 'https://www.googleapis.com/youtube/v3/channels?key=' . $params->get('google_api'). '&part=contentDetails&forUsername=' . $username;
        $rsp = self::callCURL($url);
        $rsp = json_decode($rsp);
        if(!$rsp || isset($rsp->error)){
            return false;
        }else{
            if($rsp->items && count($rsp->items) === 1){
                return $rsp->items[0];
            }else{
                return false;
            }
        }
    }

    /**
     * Get Youtube Playlist information by using Channel Id
     * @return array Youtube playlists
     * @param $channelId Youtube Channel Id
     * @since 1.3
     */
    public static function getYoutubePlaylistByChannelId($channelId){
        $params = JComponentHelper::getParams('com_bt_media');
        $playlists = array();
        $pageToken='';
        do{
            $url = 'https://www.googleapis.com/youtube/v3/playlists?key=' . $params->get('google_api') . '&part=snippet&channelId=' . $channelId . '&maxResults=50';
            if($pageToken){
                $url.='&pageToken='. $pageToken;
            }
            $rsp = self::callCURL($url);
            $rsp = json_decode($rsp);
            if(!$rsp || isset($rsp->error)){
                break;
            }else{
                if ($rsp->items && count($rsp->items)) {
                    foreach($rsp->items as $item){
                        $playlists[$item->id] = $item->snippet->title;
                    }
                } else {
                    break;
                }

            }
            $pageToken = $rsp->nextPageToken;
        }while(isset($pageToken));
        return $playlists;

    }

}