<?php

/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

/**
 * Finder Content Plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	Content.finder
 * @since   2.5
 */
$com_path = '/components/com_bt_media/';
jimport('joomla.application.component.model');
JLoader::register('Bt_mediaHelper', JPATH_SITE . $com_path . '/helpers/bt_media.php');
JLoader::register('Bt_mediaLegacyHelper', JPATH_ADMINISTRATOR . $com_path . '/helpers/legacy.php');
JFactory::getLanguage()->load('com_bt_media');

class plgContentBt_Media_Display extends JPlugin {

    protected $shareScript = '';

    public function onContentPrepare($context, &$row, &$params, $page = 0) {
        $regux_string = '/\{mediagallery id=\[(\d{1,}(,)*)*\]\}/';
        $matches = array();
        preg_match_all($regux_string, $row->text, $matches);
        $maths = $matches[0];
        if (count($maths) > 0) {
            if (class_exists('Bt_mediaHelper')) {
                //Load admin language file
                $lang = JFactory::getLanguage();
                $lang->load('com_bt_media', JPATH_ADMINISTRATOR);
                $document = JFactory::getDocument();
                $cparams = JFactory::getApplication()->getParams('com_bt_media');
                if (!JFactory::getApplication()->isAdmin()) {
                    Bt_mediaHelper::addSiteScript($cparams);
                }

                $html = array();
                $script = Bt_mediaHelper::getScripts($cparams, 'plg');


                $document->addScriptDeclaration($script);
                $document->addStyleDeclaration(".item-detail, .item-view-title{max-width:" . $cparams->get('media_show_width', 700) . "px;}  .image-information .items_list div.item{width: " . $cparams->get('thumb_image_width', 200) . "px;}");
                $identify = 0;
                foreach ($maths as $value) {
                    $gallery_html = array();
                    $gallery_html[] = '<div class="image-information">
									<!--<ul class="items_list">-->
									<div id="container" class="items_list">';
                    $str_ids1 = ltrim($value, '{mediagallery id=[');
                    $str_ids = rtrim($str_ids1, ']}');
                    $ids = explode(',', $str_ids);
                    foreach ($ids as $id) {
                        $item = Bt_mediaHelper::getMediaById($id);
                        if ($item) {
                            $item->category_name = Bt_mediaHelper::getCategorybyId($item->cate_id)->name;
                            $item->created_by = JFactory::getUser($item->created_by)->name;
                            $gallery_html[] = $this->makeHTML($item, $identify, $cparams);
                        }
                    }
                    $gallery_html[] = '</div></div>';
                    $gallery_html[] = '<div style="clear:both"></div>';
                    $html[] = implode($gallery_html);
                    $identify++;
                }
                $row->text = str_replace($maths, $html, $row->text);
                $row->text = $row->text . $this->shareScript;
            }
        }
    }

    public function makeHTML($item, $indentify, $params) {
        $html = array();
        $registry = new JRegistry();
        $registry->loadString($item->params);
        $params->merge($registry);
        $item->tags = Bt_mediaLegacyHelper::getAllTagsItem($item->id);
        $thumbImage = Bt_mediaHelper::getPathImage('thumb', $item->image_path, $item->cate_id);

        if ($params->get('theme', 'default') == 'default') {
            $html_class = 'img-thumb';
            $itemMark = '';
            $title = $item->name;
            if (str_word_count($item->name) == 1 && strlen($item->name) >= 20) {
                $title = substr($title, 0, 20) . '...';
            } else {
                $title = $item->name;
            }

            $title = '<div class="item-title"><a href="' . JRoute::_('index.php?option=com_bt_media&view=detail&id=' . (int) $item->id . ':' . $item->alias.'&cat_rel='.$item->cate_id) . '">' . $title . '</a></div>';
        }
        if ($params->get('theme', 'default') == 'modern') {
            $html_class = 'img-thumb item-image';
            $itemMark = Bt_mediaHelper::getItemMark($item, $params);
            $title = '';
        }

        if ($params->get('allow_comment', 1) == '1' || $params->get('allow_social_share', 1) == '1') {
            $row = new stdClass();
            $row->title = $item->name;
            $row->link = JFactory::getURI()->toString(array('scheme', 'host', 'port')) . JRoute::_('index.php?option=com_bt_media&view=detail&id=' . (int) $item->id . ':' . $item->alias.'&cat_rel='.$item->cate_id);
            $row->image = JUri::root() . $params->get('file_save', 'images/bt_media') . '/images/thumbnail/' . $item->image_path;
            $row->description = $item->description;
            JPluginHelper::importPlugin('content');
            $sharePlugin = array('buttons' => '', 'comment' => '', 'script' => '');
            if (class_exists('plgContentBt_socialshare')) {
                $sharePlugin = plgContentBt_socialshare::socialButtons($row, false);
            }
            $this->shareScript = $sharePlugin['script'];
        }
        $html[] = '<div class="item" style="position: relative;">';
        if ($item->media_type == 'image') {
            $html[] = '<a rel="media-item-plg" id="' . (int) $item->id . '"  onclick="return false;" class="plg-view-box" href="' . JRoute::_('index.php?option=com_bt_media&ajax=1&view=detail&id=' . $item->id . ':' . $item->alias.'&cat_rel='.$item->cate_id) . '" >
                        <div class="' . $html_class . '">' . $itemMark . '
                            <img class="image" name="thumnail" title="' . $item->name . '" src="' . $thumbImage . '" />
                        </div>
                       </a>';
        }
        if ($item->media_type == 'video') {
            $html[] = '<a rel="media-item-plg" id="' . (int) $item->id . '" onclick="return false;" class="plg-view-box" href="' . JRoute::_('index.php?option=com_bt_media&ajax=1&view=detail&id=' . $item->id . ':' . $item->alias.'&cat_rel='.$item->cate_id) . '">
                        <div class="' . $html_class . '">' . $itemMark . '
                            <img title="' . $item->name . '" src="' . $thumbImage . '" />
                            <img class="img-preview" title="' . JText::_('Preview') . '" src="' . JURI::base() . 'administrator/components/com_bt_media/assets/images/view_video.png" />
                        </div>
                      </a>';
        }

        $html[] = $title;

        $html[] = '</div>';

        return implode($html);
    }

}
