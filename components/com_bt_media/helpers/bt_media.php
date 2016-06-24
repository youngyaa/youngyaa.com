<?php

/**
 * @package     com_bt_media - BT Media
 * @version	1.3
 * @created	Oct 2012
 * @author	BowThemes
 * @email	support@bowthems.com
 * @website	http://bowthemes.com
 * @support	Forum - http://bowthemes.com/forum/
 * @copyright   Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

abstract class Bt_mediaHelper {

    public static function getPathImage($imageType, $image_path, $catid) {
        $params = JComponentHelper::getParams("com_bt_media");
        $watermask = $params->get('wm-enabled', 0);

        if (!in_array('all', $params->get('wm-categories', array('all'))) && !in_array($catid, $params->get('wm-categories', array('all')))) {
            $watermask = 0;
        }
        if ($imageType == 'thumb' && $params->get('wm-thumb', 0) == 0) {
            $watermask = 0;
        }
        if (!$image_path) {
            if ($imageType == 'thumb') {
                return COM_BT_MEDIA_THEME_URL . 'images/no-image-thumb.jpg';
            }
            if ($imageType == 'large') {
                return COM_BT_MEDIA_THEME_URL . 'images/no-image.jpg';
            }
        }
        if ($watermask == 0) {
            if ($imageType == 'thumb') {
                if (!JFile::exists(JPATH_SITE . DIRECTORY_SEPARATOR . $params->get('file_save', 'images/bt_media') . '/images/thumbnail/' . $image_path)) {
                    $imagePath = COM_BT_MEDIA_THEME_URL . 'images/no-image-thumb.jpg';
                } else {
                    $imagePath = JURI::root() . $params->get('file_save', 'images/bt_media') . '/images/thumbnail/' . $image_path;
                }
            }
            if ($imageType == 'large') {
                if (!JFile::exists(JPATH_SITE . DIRECTORY_SEPARATOR . $params->get('file_save', 'images/bt_media') . '/images/large/' . $image_path)) {
                    $imagePath = COM_BT_MEDIA_THEME_URL . 'images/no-image.jpg';
                } else {
                    $imagePath = JURI::root() . $params->get('file_save', 'images/bt_media') . '/images/large/' . $image_path;
                }
            }
        } else {
            $imagePath = JRoute::_('index.php?option=com_bt_media&task=tag.viewimage&src=' . $image_path . '&imagetype=' . $imageType);
        }

        return $imagePath;
    }

    public static function getImageWatermark() {

        $params = JComponentHelper::getParams("com_bt_media");
        $file = JFactory::getApplication()->input->get('src');
        $imgType = JFactory::getApplication()->input->get('imagetype');
        $source = '';
        if ($imgType == 'large') {
            if (!JFile::exists(JPATH_SITE . DIRECTORY_SEPARATOR . $params->get('file_save', 'images/bt_media') . '/images/large/' . $file)) {
                $source = JPATH_SITE . '/components/com_bt_media/themes/' . $params->get('theme', 'default') . '/images/no-image.jpg';
            } else {
                $source = JPATH_SITE . '/' . $params->get('file_save', 'images/bt_media') . '/images/large/' . $file;
            }
        }
        if ($imgType == 'thumb') {
            if (!JFile::exists(JPATH_SITE . DIRECTORY_SEPARATOR . $params->get('file_save', 'images/bt_media') . '/images/thumbnail/' . $file)) {
                $source = JPATH_SITE . '/components/com_bt_media/themes/' . $params->get('theme', 'default') . '/images/no-image-thumb.jpg';
            } else {
                $source = JPATH_SITE . '/' . $params->get('file_save', 'images/bt_media') . '/images/thumbnail/' . $file;
            }
        }
        if ($params->get('wm-enabled')) {
            require_once JPATH_COMPONENT . '/helpers/watermask/watermask.php';
            $options = BtMediaWaterMask::getWaterMarkOptions();
            $options['padding'] = $params->get('wm-padding', $options['padding']);
            $options['font'] = $params->get('wm-font') ? JPATH_COMPONENT . '/helpers/watermask/fonts/' . $params->get('wm-font') . '.ttf' : $options['font'];
            $options['text'] = $params->get('wm-text', $options['text']);
            $options['image'] = $params->get('wm-image') ? JPATH_ROOT . '/' . $params->get('wm-image') : $options['image'];
            $options['type'] = $params->get('wm-type', $options['type']);
            $options['fcolor'] = $params->get('wm-fcolor', $options['fcolor']);
            $options['fsize'] = $params->get('wm-fsize', $options['fsize']);
            $options['bg'] = $params->get('wm-bg', $options['bg']);
            $options['bgcolor'] = $params->get('wm-bgcolor', $options['bgcolor']);
            $options['factor'] = $params->get('wm-factor', $options['factor']);
            $options['thumbnail_factor'] = $params->get('wm-thumbnail-factor', $options['thumbnail_factor']);
            $options['position'] = $params->get('wm-position', $options['position']);
            $options['opacity'] = $params->get('wm-opacity', $options['opacity']);
            $options['rotate'] = $params->get('wm-rotate', $options['rotate']);
            if($imgType == 'thumb'){
                $options['size'] = 'thumb';
            }
            BtMediaWaterMask::createWaterMark($source, $options);
        } else {
            $size = getimagesize($source);
            $imagetype = $size[2];
            switch ($imagetype) {
                case(1):
                    header('Content-type: image/gif');
                    $image = imagecreatefromgif($source);
                    imagegif($image);
                    break;

                case(2):
                    $image = imagecreatefromjpeg($source);
                    header('Content-type: image/jpeg');
                    imagejpeg($image);
                    break;

                case(3):
                    header('Content-type: image/png');
                    $image = imagecreatefrompng($source);
                    imagepng($image);
                    break;

                case(6):
                    header('Content-type: image/bmp');
                    $image = imagecreatefrombmp($source);
                    imagewbmp($image);
                    break;
            }
        }
        exit;
    }

    public static function addSiteScript($bt_params) {
        JHTML::_('behavior.framework');

        $app = JFactory::getApplication();
        $theme_media = $bt_params->get('theme', 'default');
        $theme_url = '';
        if (is_dir(JPATH_SITE . '/templates/' . $app->getTemplate() . '/html/com_bt_media/' . $theme_media)) {
            $theme_url = JURI::root() . 'templates/' . $app->getTemplate() . '/html/com_bt_media/' . $theme_media . '/';
        } elseif (is_dir(JPATH_SITE . '/components/com_bt_media/themes/' . $theme_media)) {
            $theme_url = JURI::root() . 'components/com_bt_media/themes/' . $theme_media . '/';
        }
        if ($theme_url == '') {
            return JError::raiseError(500, sprintf(JText::_('COM_BT_MEDIA_THEME_NOT_FOUND'), $theme_media));
        }
        if (!defined('COM_BT_MEDIA_THEME_URL')) {
            define('COM_BT_MEDIA_THEME_URL', $theme_url);
        }
        $document = JFactory::getDocument();
        $header = $document->getHeadData();
        $loadJquery = true;
        foreach ($header['scripts'] as $scriptName => $scriptData) {
            if (substr_count($scriptName, '/jquery')) {
                $loadJquery = false;
                break;
            }
        }
//bind JText to JS:
        JText::script('COM_BT_MEDIA_HITS');
        JText::script('COM_BT_MEDIA_ENTER_YOUR_KEYWORD');
        JText::script('COM_BT_MEDIA_DELETE_MESSAGE');

        if ($loadJquery) {
            $document->addScript(JURI::root() . 'components/com_bt_media/assets/js/jquery-1.8.2.min.js');
        }
        $document->addScriptDeclaration(
                'var btMediaCfg = {thumbWidth:' . $bt_params->get('thumb_image_width', 200) . ', siteURL: "' . JUri::root() . '"}'
        );

        $document->addStyleSheet(JURI::root() . 'components/com_bt_media/assets/lib/fancyBox/source/jquery.fancybox.css');
        $document->addStyleSheet(JURI::root() . 'components/com_bt_media/assets/css/jquery-ui.css');
        $document->addStyleSheet(JURI::root() . 'components/com_bt_media/assets/css/com_bt_media.css');
        $document->addStyleSheet(JURI::root() . 'components/com_bt_media/assets/lib/uploadify/uploadify.css');
        $document->addScript(JURI::root() . 'components/com_bt_media/assets/js/jquery-ui.js');
        $document->addScript(JURI::root() . 'components/com_bt_media/assets/js/jquery.filedrop.js');
        $document->addScript(JURI::root() . 'components/com_bt_media/assets/lib/fancyBox/source/jquery.fancybox.pack.js');
        $document->addScript(JURI::root() . 'components/com_bt_media/assets/lib/fancyBox/source/helpers/jquery.fancybox-media.js');

        $document->addScript(JURI::root() . 'components/com_bt_media/assets/lib/uploadify/jquery.uploadify.min.js');
        $document->addStyleSheet(COM_BT_MEDIA_THEME_URL . 'css/style.css');
        $document->addStyleSheet(JURI::root() . 'components/com_bt_media/assets/lib/video-js/video-js.min.css');
        $document->addScript(JURI::root() . 'components/com_bt_media/assets/lib/video-js/video.js');
        $document->addScriptDeclaration('videojs.options.flash.swf="' . JURI::root() . 'components/com_bt_media/assets/lib/video-js/video-js.swf"');
        $document->addScript('http://www.youtube.com/player_api');

//more js for model theme
        if ($theme_media == 'modern') {
            $document->addScript(COM_BT_MEDIA_THEME_URL . 'js/masonry.pkagd.min.js');
        }
        $document->addScript(JURI::root() . 'components/com_bt_media/assets/lib/jQueryAutoComplete/jquery.autocomplete.js');
        $document->addScript(JURI::root() . 'components/com_bt_media/assets/js/default.js');
        $document->addScript(COM_BT_MEDIA_THEME_URL . 'js/script.js');
    }

    public static function getRatingPanel($itemid, $rating_sum, $rating_count, $canRate = true, $showCount = true) {
        $width = 15;
        $height = 15;
        $numOfStar = 5;

        if ($rating_count == 0)
            $rating = 0;
        else
            $rating = ($rating_sum / $rating_count);

        $backgroundWidth = $numOfStar * $width;
        $currentWidth = round($rating * $width);

        $html = '<div class="btp-rating-container-' . $itemid . '"><div class="btp-rating-background" style="width: ' . $backgroundWidth . 'px"><div class="btp-rating-current" style="width: ' . $currentWidth . 'px"></div>';

        if ($canRate) {
            for ($i = $numOfStar; $i > 0; $i--) {
                $starWidth = $width * $i;
                $html .= '<a onclick="javascript:rate(' . $itemid . ', ' . $i . ')" href="javascript:void(0);" style="width:' . $starWidth . 'px"></a>';
            }
        }

        $html .= '</div>';
        if ($showCount) {
            $html .= '<div class="btp-rating-notice">' . sprintf(JText::_('COM_BT_MEDIA_RATING_TEXT'), $rating, $rating_count) . '</div>';
        }

        $html .= '</div>';

        return $html;
    }

    public static function allowComment() {
        $allowComment = false;
        $params = JComponentHelper::getParams('com_bt_media');
        if ($params->get('allow_comment', 1) == 1) {
            $allowComment = true;
        }
        return $allowComment;
    }

    public static function getCategorybyId($id) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(TRUE);
        $query->select('name, alias, parent_id');
        $query->from('#__bt_media_categories');
        $query->where('id=' . (int) $id);
        $db->setQuery($query);
        return $db->loadObject();
    }

    public static function getMediaById($id) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(TRUE);
        $query->select('*');
        $query->from('#__bt_media_items');
        $query->where('id=' . (int) $id);
        $query->where('state=1');
        $db->setQuery($query);
        return $db->loadObject();
    }

    public static function getScripts($params, $view) {
        $html = '
                    $BM(document).ready(function() {
                        $BM(".' . $view . '-view-box").fancybox({
							type:"ajax",
                            openEffect: \'' . $params->get('op_open_effect', 'fade') . '\',
                            closeEffect: \'' . $params->get('op_close_effect', 'fade') . '\',
                            nextEffect: \'' . $params->get('op_next_prev_effect', 'fade') . '\',
                            prevEffect: \'' . $params->get('op_next_prev_effect', 'fade') . '\',
                            aspectRatio: ' . (($params->get('op_aspect_ratio', 0) == 0) ? 'false' : 'true') . ',
                            autoPlay: ' . (($params->get('op_auto_play', 0) == 0) ? 'false' : 'true') . ',
                            playSpeed: ' . ($params->get('op_play_speed', 3) * 1000) . ',
                            closeBtn: ' . (($params->get('op_close_btn', 1) == 0) ? 'false' : 'true') . ',
                            padding: ' . $params->get('op_padding', 15) . ',
                            margin: ' . $params->get('op_margin', 50) . ',
                            autoSize: false,
                            mouseWheel:false,
                            wrapCSS: \'fancybox-bt-media\',
                            scrolling:false,
                            autoResize: true,
                            fitToView: false,
                            width: \'100%\',
                            maxWidth: '.($params->get('media_show_width', 700)).',
                            helpers: {
                                title: null,
                                media: {}
                            },
                            beforeShow: function() { ' .
                            ($params->get('op_auto_play', 0) ? ' $BM.fancybox.play(true); ' : '') . '
                                var parent_el = jQuery(this).attr("element").parent();
                                var id = jQuery(this).attr("element").attr("id");
                                var view_box = "#media-item-"+id;
                                var img = new Image();
                                if(typeof FB != "undefined"){
                                    FB.XFBML.parse(document.getElementById("' . $view . '-item-" + id));
                                }
                                if($BM(view_box).find(".bt-social-share").length>0){ 
                                        if(typeof IN != "undefined"){
                                                IN.parse(document.getElementById("' . $view . '-item-" + id));
                                        }
                                        if(typeof twttr != "undefined"){
                                                twttr.widgets.load();
                                        }
                                        if(typeof gapi != "undefined"){
                                                $BM(view_box).find(".bt-googleplus-button").attr("id","bt-googleplus-button-"+id);
                                                gapi.plus.go("bt-googleplus-button"+id);
                                        }
                                        if(typeof STMBLPN != "undefined"){
                                                STMBLPN.processWidgets();
                                        }
                                        if(typeof __DBW != "undefined"){
                                                __DBW.addButtons.call();
                                        }
                                }
                                $BM.getScript(btMediaCfg.siteURL+"components/com_bt_media/assets/lib/video-js/video.js");
                                if($BM(view_box).find("img").length){
                                        $BM(img).load(function(){
                                                $BM.fancybox.update();
                                        }).attr("src",$BM(view_box).find("img").attr("src"));
                                }else{
                                                $BM.fancybox.update();
                                }';
        if ($params->get('op_auto_play', 0)) {
            $html.='
								   // Find the iframe ID
								var frameid = $BM.fancybox.inner.find("iframe").attr("id");
								
								// Create video player object and add event listeners
								var player = new YT.Player(frameid, {
									events: {
										"onReady": onPlayerReady,
										"onStateChange": onPlayerStateChange
                                }
								});';
        }
        $html.='
                            },
							
                            tpl: {next:"",prev:"",closeBtn: \'<a title="Close" class="fancybox-item fancybox-close custom-f-close" href="javascript:$BM.fancybox.close();"></a>\'}
                        });
                    });';
        if ($params->get('op_auto_play', 0)) {
            $html.='
					function onPlayerReady(event) {
						//event.target.playVideo();
					}

					function onPlayerStateChange(event) {
						// Go to the next video after the current one is finished playing
						
						if (event.data === 0) {
							$BM.fancybox.next();
						}else if(event.data >=1) {
						
							$BM.fancybox.play(false);    
						}
					}';
        }
        return $html;
    }

    public static function getItemMark($item, $params) {
        $show_mark = FALSE;
        $item_header = '';
        $item_footer = '';
        if ($params->get('show_category', 1) && $params->get('show_category', 1) == 1) :
            $item_header .= '<div class="item-category">' . $item->category_name . '</div>';
            $show_mark = TRUE;
        endif;
        if ($params->get('show_name', 1) && $params->get('show_name', 1) == 1):
            $item_header .= '<div class="item-title">' . JFilterOutput::cleanText($item->name) . '</div>';
            $show_mark = TRUE;
        endif;
        if (self::allowComment() || ($params->get('allow_voting', 1) && $params->get('allow_voting', 1) == 1)) {
            $show_mark = TRUE;
            $item_footer .= '<div class="item-footer">';
            if (self::allowComment()) {
                $item_footer .= '<div class="item-comment-count"><span class="fb-cm-count"><fb:comments-count href=' . JFactory::getURI()->toString(array('scheme', 'host', 'port')) . JRoute::_('index.php?option=com_bt_media&view=detail&id=' . $item->id . ':' . $item->alias) . '></fb:comments-count></span> ' . JText::_('COM_BT_MEDIA_COMMENT_COUNT') . '</div>';
            }
            if ($params->get('allow_voting', 1) && $params->get('allow_voting', 1) == 1) {
                $item_footer .= '<div class="item-vote-count">' . self::getRatingPanel($item->id, $item->vote_sum, $item->vote_count, FALSE, FALSE) . '</div>';
            }
            $item_footer .= '</div>';
        }
        $html = '<div class = "item-mark">';
        if ($show_mark):
            $html .= '<div class="item-header"> ' . $item_header . ' </div>';
            $html .= $item_footer;
        endif;
        $html.='</div>';
        return $html;
    }

    public static function getFilterBar(&$params) {
        $app = JFactory::getApplication();
        $options = array();
        $options[] = JHtml::_('select.option', '', JText::_('COM_BT_MEDIA_MEDIA_TYPE'));
        $options[] = JHtml::_('select.option', 'image', JText::_('COM_BT_MEDIA_IMAGE'));
        $options[] = JHtml::_('select.option', 'video', JText::_('COM_BT_MEDIA_VIDEO'));
        $filterType = JHtml::_('select.genericlist', $options, 'filter_type', '', 'value', 'text', $app->input->getString('filter_type', $params->get('show_media_type')), '');

        $options = array();
        $options[] = JHtml::_('select.option', '', JText::_('COM_BT_MEDIA_ORDER_BY'));
        $options[] = JHtml::_('select.option', 'ordering', JText::_('COM_BT_MEDIA_ORDERING'));
        $options[] = JHtml::_('select.option', 'name', JText::_('COM_BT_MEDIA_ORDER_BY_NAME'));
        $options[] = JHtml::_('select.option', 'created_date', JText::_('COM_BT_MEDIA_ORDER_BY_CREATED_DATE'));
        $options[] = JHtml::_('select.option', 'hits', JText::_('COM_BT_MEDIA_ORDER_BY_HITS'));
        $options[] = JHtml::_('select.option', 'featured', JText::_('COM_BT_MEDIA_ORDER_BY_FEATURED'));
        $options[] = JHtml::_('select.option', 'item_type', JText::_('COM_BT_MEDIA_ORDER_BY_ITEM_TYPE'));
        $options[] = JHtml::_('select.option', 'rating', JText::_('COM_BT_MEDIA_ORDER_BY_RATING'));
        $filterOrdering = JHtml::_('select.genericlist', $options, 'filter_ordering', '', 'value', 'text', $app->input->getString('filter_ordering', $params->get('show_ordering')), '');

        $options = array();
        $options[] = JHtml::_('select.option', 'ASC', JText::_('COM_BT_MEDIA_ASC'));
        $options[] = JHtml::_('select.option', 'DESC', JText::_('COM_BT_MEDIA_DESC'));
        $filterDirection = JHtml::_('select.genericlist', $options, 'filter_direction', '', 'value', 'text', $app->input->getString('filter_direction', $params->get('order_type')), '');

        $html = '';
        $html .= '
		<div class="filter-bar">
            <div class="filter-search fltlft">
                <input placeholder="' . JText::_('COM_BT_MEDIA_ENTER_YOUR_KEYWORD') . '" type="text" name="filter_search" id="filter_search" value="' . $app->input->getString('filter_search') . '" title="' . JText::_('Search') . '" />
            </div>
           <div class="filter-select fltrt">' . $filterType . '</div>
           <div class="filter-select fltrt">' . $filterOrdering . '</div>
           <div class="filter-select fltrt">' . $filterDirection . '
		    <a class="btn" href="#" onclick="document.adminForm.submit();">' . JText::_('JSEARCH_FILTER_SUBMIT') . '</a>
		   </div>
			<div style="clear:both"></div>
        </div>
		';
        return $html;
    }

}

