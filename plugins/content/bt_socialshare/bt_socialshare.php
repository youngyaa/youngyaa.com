<?php

/**
 * @package 	bt_socialshare - BT Social Share Plugin
 * @version		2.0
 * @created		Oct 2011

 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
if(file_exists(JPATH_ADMINISTRATOR.'/components/com_k2/lib/k2parameter.php')){
	JLoader::register('K2Plugin', JPATH_ADMINISTRATOR.'/components/com_k2/lib/k2plugin.php'); 
	class BtPlugin extends K2Plugin{}
}else{
	class BtPlugin extends JPlugin{}
}



class plgContentBt_socialshare extends BtPlugin {
	protected $html = '';
	protected $extra = '';
	protected $fbComment ='';
	protected $positions = array();
	protected $rowIds = array();
	function onRenderAdminForm(&$item, $type, $tab = ''){
		return;
	}
	public function buildHTML($context, &$row, &$params, $page = 0){	
		if(in_array($row->id,$this->rowIds)){
			return;
		}else{
			$this->rowIds[] = $row->id;
			$this->html = '';
			$this->extra = '';
			$this->positions = array();
			$this->fbComment = '';
		}

		$k2 = substr_count($context, 'com_k2');
		if ($context == 'com_content.featured' || $context == 'com_content.category' || $context == 'com_content.article' || $context == 'com_k2.item' || $context == 'com_k2.itemlist') {
		
            $view = JRequest::getString('view');
            if ($view == 'itemlist')
                $view = 'category';
            else if ($view == 'item')
                $view = 'article';
            $show_plugin_in = $this->params->get('show_plugin_in', array('article'));
            if (!is_array($show_plugin_in))
                $show_plugin_in = explode(',', $show_plugin_in);
            $document = JFactory::getDocument();
			$header = $document->getHeadData();
			$addOg = $this->params->get('og_meta',1);
			foreach($header['custom'] as $custom){
				if(substr_count($custom,'<meta property="og:title"'))
				{
					$addOg =false;
					break;
				}
			}
            $show = false;
			
			
            if ($show_plugin_in) {
                foreach ($show_plugin_in as $option) {
                    if (strtolower($view) == strtolower($option) || $option == "all" || ($option == 'featured' && $view=='article' && $row->featured)) {
                        $show = true;
                    }
                }
            }
            if (!$show || !$this->checkExcluding('', $row, $context)) {
                return;
            }

            //Run plugin
            $document->addStyleSheet(JURI::root() . '/plugins/content/bt_socialshare/assets/bt_socialshare.css');
            $uri = JURI::getInstance();
            if ($row->id) {
                if ($k2) {
                    require_once(JPATH_SITE . '/components/com_k2/helpers/route.php');
                    $link_article = JRoute::_(K2HelperRoute::getItemRoute($row->id . ':' . $row->alias, $row->catid . ':' . $row->category->alias));
                } else {
                    require_once(JPATH_SITE . '/components/com_content/helpers/route.php');
                    $link_article = JRoute::_(ContentHelperRoute::getArticleRoute($row->id, $row->catid));
                }
                $link_article = $uri->getScheme() . "://" . $uri->getHost() . $link_article;
            } else {
                $link_article = $uri->toString();
            }

			if($this->params->get('og_title', 0)){
				$title = $document->getTitle();
			}else{
				$title = $this->cleanText($row->title);
			}

            $lang = JFactory::getLanguage();
            $langTag = $lang->getTag();
            $langTagArr = explode('-', $langTag);

            //$this->html = '';
            $script = '';
			$ogImage = false;
			if($addOg){
				$description = substr($this->cleanText($row->introtext,true), 0, 300);
				$document->addCustomTag('<meta property="og:type" content="website" />');
				$document->addCustomTag('<meta property="og:title" content="' . $title . '" />');
				$document->addCustomTag('<meta property="og:url" content="' . $link_article . '" />');
				$document->addCustomTag('<meta property="og:description" content="' . $description . '" />');
				if($k2){
					if(isset($row->imageMedium) && $row->imageMedium){
						$ogImage = true;
						$document->addCustomTag('<meta property="og:image" content="' . $row->imageMedium . '" />');
					}
				}else{
					if(isset($row->images)){
						$articleImages = json_decode($row->images);
						
						if(isset($articleImages->image_intro) && $articleImages->image_intro){
							$ogImage = true;
							$document->addCustomTag('<meta property="og:image" content="' . JURI::root(). $articleImages->image_intro . '" />');
						}
					}
				}
			}
            $images = array();
            if (isset($row->text)) {
                $regex = "/\<img.+src\s*=\s*\"([^\"]*)\"[^\>]*\>/Us";
                preg_match($regex, $row->text, $matches);
                $images = (count($matches)) ? $matches : array();
                if (count($images) && $addOg && !$ogImage) {
					
                    $url_image = $images[1];
                    if (!substr_count($url_image, 'http://'))
                        $url_image = $uri->getScheme() . "://" . $uri->getHost() . JURI::root(true) . '/' . $url_image;
                    $document->addCustomTag('<meta property="og:image" content="' . $url_image . '" />');
                }
            }
            if (trim($this->params->get('facebook_api_id'))) {
                $fb_api = "&appId=" . trim($this->params->get('facebook_api_id'));
                $fb_admin_ids = trim($this->params->get('facebook_admins'));
                if($addOg){
					$document->addCustomTag('<meta property="fb:app_id" content="' . trim($this->params->get('facebook_api_id')) . '" />');
					if ($fb_admin_ids) {
						$document->addCustomTag('<meta property="fb:admins" content="' . $fb_admin_ids . '" />');
					}
				}
            } else {
                $fb_api = "";
            }

            $script .='<div id="fb-root"></div>
				<script>(function(d, s, id) {
				  var js, fjs = d.getElementsByTagName(s)[0];
				  if (d.getElementById(id)) {return;}
				  js = d.createElement(s); js.id = id;
				  js.src = "//connect.facebook.net/' . str_replace('-', '_', $langTag) . '/all.js#xfbml=1' . $fb_api . '";
				  fjs.parentNode.insertBefore(js, fjs);
				}(document, \'script\', \'facebook-jssdk\'));</script>';
            #share FB
            if ($this->params->get('facebook_share_button') == 1) {
                $this->html .= $this->getFacebookShareButton($link_article, $row->title);
            }
			
            # like FB 
            if ($this->params->get('facebook_like') == 1) {
                $this->html .= $this->getFacebookeLikeButton($link_article);
            }
            # FB commmet
			
			if ($this->params->get("facebook_comment") && $view == 'article' && $this->checkExcluding('fb_', $row, $context)) {
				$this->fbComment = $this->getFacebookCommentBox($link_article, $row->title);
			}
            if ($this->params->get("facebook_recommendation")) {
                $this->extra = $this->getFacebookRecommendation($link_article);
            }
            #for twitter
            if ($this->params->get('twitter') == 1) {
                $twitterArrg = $this->getTwitterButton($link_article, $langTagArr[0]);
                $this->html .= $twitterArrg[0];
                $script .= $twitterArrg[1];
            }

            #for linkedin
            if ($this->params->get('linkedin') == 1) {
                $linkedin = $this->getLinkedinButton($link_article);
                $this->html .= $linkedin[0];
                $script .= $linkedin[1];
            }

            #for google plus
            if ($this->params->get('google_plus') == 1) {
                $googlePlus = $this->getGooglePlusButton($link_article, $langTag);
                $this->html .= $googlePlus[0];
                $script .= $googlePlus[1];
            }

            #for stumble
            if ($this->params->get("stumble")) {
                //$this->html .= $this->getStumbleButton($link_article);
                $stumbleButton = $this->getStumbleButton($link_article);
                $this->html .= $stumbleButton[0];
                $script .= $stumbleButton[1];
            }

            #for digg
            if ($this->params->get("digg")) {
                $diggButton = $this->getDiggButton($link_article, $title);
                $this->html .= $diggButton[0];
                $script .= $diggButton[1];
            }
            $this->html .= '</div>';

			$positions = null;
			if($k2){
				$positions = $this->params->get('k2-positions', array('comment-block'));
			}else{
				$positions = $this->params->get('positions', array('bellow'));
			}
			
			foreach ($positions as $position) {
				$this->positions[] = $position;
				$addedhtml = '<div class="bt-social-share bt-social-share-' .$position .'">' . $this->getPretext(). $this->html;
				
				if ($position == 'below') {
					$row->introtext .= $addedhtml;
					if (isset($row->text))
						$row->text .= $addedhtml;
				}
				else if ($position == 'image') {
					if (count($images)) {
						if (isset($row->text)) {
							$row->text = str_replace($images[0], '<div style="display:inline-block;position:relative;">' . $images[0] . $addedhtml . '</div>', $row->text);
						}
					}
				} else if($position == 'after-title' || $position == 'comment-block'){
					continue;
				}
				else{
					$row->introtext = $addedhtml . $row->introtext;
					if (isset($row->text)) {
						$row->text = $addedhtml . $row->text;
					}
				}
			}
			
			if(!$k2){
				$this->extra .= $this->fbComment;
			}
            $this->extra .= $script;
            $row->introtext .=$this->extra;
            if (isset($row->text)) {
                $row->text .=$this->extra;
            }
        }
	}

    public function onContentBeforeDisplay($context, &$row, &$params, $page = 0) {
		if(JFactory::getApplication()->isAdmin()){
			return;
		}
		$this->buildHTML($context, $row, $params, $page);
        return;
    }

	public function onContentAfterTitle($context, &$row, &$params, $page = 0){
		if(JFactory::getApplication()->isAdmin()){
			return;
		}
		$this->buildHTML($context, $row, $params, $page);
		if(in_array('after-title', $this->positions)){
			$addedhtml = '<div class="bt-social-share bt-social-share-after-title">' .$this->getPretext(). $this->html;
			return $addedhtml;
		}
		return;
	}
	
	public function onK2CommentsBlock( & $item, &$params, $page){
		if(JFactory::getApplication()->isAdmin()){
			return;
		}
		$this->buildHTML('com_k2.item', $item, $params, $page);
		$addedhtml = '';
		if(in_array('comment-block', $this->positions)){
			$addedhtml = '<div class="bt-social-share bt-social-share-after-title">' .$this->getPretext(). $this->html;
		}
		if($params->get('comments')){
		   $addedhtml .= $this->fbComment;
		}
		return $addedhtml;
	}
    protected function checkExcluding($type, &$row, $context) {
        // Exclude Category
        $excludingCategories = $this->params->get($type . 'excluding_categories', 0);
        $excludingK2Categories = $this->params->get($type . 'excluding_k2_category', 0);
        if (!empty($excludingCategories)) {
            if ($context == 'com_content.article') {
                if (strlen(array_search($row->catid, $excludingCategories))) {
                    return false;
                }
            }
        }

        if (!empty($excludingK2Categories)) {
            if ($context == 'com_k2.item' || $context == 'com_k2.itemlist') {
                if (strlen(array_search($row->catid, $excludingK2Categories))) {
                    return false;
                }
            }
        }

        // Exclude Article
        $excludingArticles = $this->params->get($type . 'excluding_article', '');
        $excludingK2Articles = $this->params->get($type . 'excluding_k2_article', '');
        $excludingArticles = explode(",", $excludingArticles);
        $excludingK2Articles = explode(",", $excludingK2Articles);

        if (!empty($excludingArticles)) {
            if ($context == 'com_content.article' || $context == 'com_content.featured' || $context == 'com_content.category') {
                if (strlen(array_search($row->id, $excludingArticles))) {
                    return false;
                }
            }
        }
        if (!empty($excludingK2Articles)) {
            if ($context == 'com_k2.item' || $context == 'com_k2.itemlist') {
                if (strlen(array_search($row->id, $excludingK2Articles))) {
                    return false;
                }
            }
        }
        return true;
    }

    public function getFacebookShareButton($link_article, $title) {
        $html = array();
        $buttonType = $this->params->get('facebook_share_button_type');
        $html[] = '<div class="bt-social-share-button bt-facebook-share-button">';
        if ($buttonType) {
            $html[] = '<fb:share-button href="' . $link_article . '" type="' . $buttonType . '"></fb:share-button>';
        } else {
            $html[] = '<img class="fb-share" src="' . JURI::root() . '/plugins/content/bt_socialshare/assets/share.png" onClick="window.open(\'http://www.facebook.com/sharer.php?u=\'+encodeURIComponent(\'' . $link_article . '\')+\'&t=\'+encodeURIComponent(\'' . $title . '\'),\'sharer\',\'toolbar=0,status=0,left=\'+((screen.width/2)-300)+\',top=\'+((screen.height/2)-200)+\',width=600,height=360\');" href="javascript: void(0)" />';
        }
        $html[] = '</div>';

        return implode($html);
    }

    public function getFacebookeLikeButton($link_article) {
        $html = array();
        $html[] = '<div class="bt-social-share-button bt-facebook-like-button">';
        if ($this->params->get('facebook_html5') == 1) {
            $html[] = '<div class="fb-like" data-href="' . $link_article;
            $html[] = '" data-colorscheme="' . $this->params->get('facebook_like_color');
            $html[] = '" data-font="' . $this->params->get('facebook_like_font');
            $html[] = '" data-send="' . ($this->params->get('facebook_sendbutton') == 1 ? "true" : "false");
            $html[] = '" data-layout="' . $this->params->get('facebook_like_type');
            $html[] = '" data-width="' . $this->params->get('facebook_like_width');
            $html[] = '" data-show-faces="' . ($this->params->get('facebook_showface') == 1 ? "true" : "false");
            $html[] = '" data-action="' . $this->params->get('facebook_like_action') . '"></div>';
        } else {
            $html[] = '<fb:like send="' . ($this->params->get('facebook_sendbutton') == 1 ? "true" : "false");
            $html[] = '" colorscheme="' . $this->params->get('facebook_like_color');
            $html[] = '" font="' . $this->params->get('facebook_like_font');
            $html[] = '" href="' . $link_article;
            $html[] = '" layout="' . $this->params->get('facebook_like_type');
            $html[] = '" width="' . $this->params->get('facebook_like_width');
            $html[] = '" show_faces="' . ($this->params->get('facebook_showface') == 1 ? "true" : "false");
            $html[] = '" action="' . $this->params->get('facebook_like_action') . '"></fb:like>';
        }
        $html[] = '</div>';
        return implode($html);
    }

    public function getFacebookCommentBox($link_article,$title) {
		$document = JFactory::getDocument();
		static $fbAddedJs = false;
		if ($this->params->get("facebook_comment_width", "500") == 'auto') {
            //$document->addStyleDeclaration('.bt_facebook_comment .fb_iframe_widget iframe,.bt_facebook_comment .fb_iframe_widget,.bt_facebook_comment .fb_iframe_widget span { width:100% !important; }');
			$header = $document->getHeadData();
			$loadJquery = true;
			foreach ($header['scripts'] as $scriptName => $scriptData) {
				if (substr_count($scriptName, '/jquery')) {
					$loadJquery = false;
					break;
				}
			}
			if ($loadJquery) {
				$document->addScript(JURI::root() . 'plugins/content/bt_socialshare/assets/js/jquery-1.8.2.min.js');
			}
			$uniqid = uniqid();
			$extra = '<div id="'.$uniqid.'" class="bt_facebook_comment">'.$this->getPretext('comment');
			$extra .= "<script type=\"text/javascript\">
			(function($){
			var uniqid = '#".$uniqid."'
			$(document).ready(function(){
				var width = $(uniqid).width();
				var interval = setInterval(function(){
					var iframe = jQuery(uniqid).find('iframe');
					if(iframe.length){
						iframe.attr('src',iframe.attr('src').substring(0,iframe.attr('src').indexOf('width='))+'width='+width);
						clearInterval(interval);
					}
				},500);
				
			})
			var timeOut = 0;
			$(window).resize(function(){
				clearTimeout(timeOut);
				timeOut = setTimeout(function(){
				var width = $(uniqid).width();
				var iframe = $(uniqid).find('iframe');
				if(iframe.length){
					iframe.attr('src',iframe.attr('src').substring(0,iframe.attr('src').indexOf('width='))+'width='+width);
				}
				},500);
			})
			})(jQuery)
			</script>";
        }
        if ($this->params->get('facebook_html5') == 1) {
            $extra .= '<div class="fb-comments" data-colorscheme="' . $this->params->get("facebook_comment_color_schema", "light");
            $extra .= '" data-href="' . $link_article;
            $extra .= '" data-num-posts="' . $this->params->get("facebook_comment_numberpost", "2");
            $extra .= '" data-width="'.$this->params->get("facebook_comment_width", "500").'"></div>';
        } else {
            $extra .= '<fb:comments colorscheme="' . $this->params->get("facebook_comment_color_schema", "light");
            $extra .= '" href="' . $link_article;
            $extra .= '" num_posts="' . $this->params->get("facebook_comment_numberpost", "2");
            $extra .= '" width="'.$this->params->get("facebook_comment_width", "500").'"></fb:comments>';
        }
        $extra .= '</div>'; 
		if(trim($this->params->get('mail_to','')) && !$fbAddedJs){
			$fbAddedJs = true;
			$script = '';
			$script .="function bt_sendmail(response) {";
		 	$script .="var xmlhttp;";
		 	$script .="if (window.XMLHttpRequest) {xmlhttp=new XMLHttpRequest();} else {xmlhttp=new ActiveXObject('Microsoft.XMLHTTP');}";
			$script .="xmlhttp.open('GET','".JURI::base()."plugins/content/bt_socialshare/sendmail.php?title='+document.title+'&link='+encodeURIComponent(response.href),true);\n";
			$script .="xmlhttp.send();";
			$script .="};";
			$script .="window.fbAsyncInit = function() {";
			$script .="FB.Event.subscribe('comment.create', function (response) {bt_sendmail(response);});";
			$script .="};";		
			$document->addScriptDeclaration($script);
		}
        return $extra;
    }

    public function getTwitterButton($link_article, $langTagArr) {
        $html = array();
        $html[] = '<div class="bt-social-share-button bt-twitter-button" style="width:'.$this->params->get('twitter_width',80) .'px">';
        $html[] = '<a href="http://twitter.com/share" class="twitter-share-button" 
						  data-via="' . $this->params->get('twitter_name') . '" 
						  data-url="' . $link_article . '" 
						  data-size="' . $this->params->get('twitter_size') . '"
						  data-lang="' . $langTagArr . '"
						  data-count="' . $this->params->get('twitter_counter') . '" >Twitter</a>';
        $html[] = '</div>';
        $script = '<script src="http://platform.twitter.com/widgets.js" type="text/javascript"></script>';
        return array(implode($html), $script);
    }

    public function getLinkedinButton($link_article) {
        $html = array();
        $html[] = '<div class="bt-social-share-button bt-linkedin-button">';
        $html[] = '<script type="IN/share" data-url="' . $link_article . '"
						 data-showzero="' . ($this->params->get('linkedIn_showzero') == 1 ? "true" : "false") . '"
						 data-counter="' . $this->params->get('linkedIn_type') . '"></script>';
        $html[] = '</div>';
        $script = '<script type="text/javascript" src="http://platform.linkedin.com/in.js"></script>';
        return array(implode($html), $script);
    }

    public function getGooglePlusButton($link_article, $langTag) {
        $html = array();
        $script = array();
        # setting share button 
        $googleButton = '<g:plus' .
                ' action="share"' .
                ' href="' . $link_article . '"' .
                ' annotation="' . $this->params->get("google_plus_annotation", 1) . '" ' .
                ($this->params->get('google_plus_annotation') == 'vertical-bubble' ? 'height="60"' : ('height="' . $this->params->get('google_plus_type') . '" ')) .
                ($this->params->get("google_plus_annotation", 1) == 'inline' ? ('width="' . $this->params->get('google_plus_width', '120') . '" ') : '') .
                '></g:plus>';
        $html5Button = '<div class="g-plus" ' .
                'data-action="share" ' .
                ' data-href="' . $link_article . '"' .
                ' data-annotation="' . $this->params->get('google_plus_annotation') . '" ' .
                ($this->params->get('google_plus_annotation') == 'vertical-bubble' ? 'data-height="60"' : ('data-height="' . $this->params->get('google_plus_type') . '" ')) .
                ($this->params->get("google_plus_annotation", 1) == 'inline' ? ('data-width="' . $this->params->get('google_plus_width', '120') . '"') : '') .
                '></div>';

        $html[] = '<div class="bt-social-share-button bt-googleplus-button">';

        if ($this->params->get('google_plus_parse_tags') == 'onload') {
            if ($this->params->get('google_plus_asynchronous') == 1) {
                if ($this->params->get('google_plus_use_html5') == 0) {
                    $html[] = $googleButton;
                } else {
                    $html[] = $html5Button;
                }
                $script[] = '<script type="text/javascript">';
                $script[] = "window.___gcfg = {lang: \'' . $langTag . '\'};";
                $script[] = '(function() {';
                $script[] = "var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;";
                $script[] = "po.src = \'https://apis.google.com/js/plusone.js\';";
                $script[] = "var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);";
                $script[] = '})();';
                $script[] = '</script>';
                $script[] = '<script type="text/javascript">(function() {var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;po.src = \'https://apis.google.com/js/plusone.js\';var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);})();</script>';
            } else {
                $script[] = '<script type="text/javascript" src="https://apis.google.com/js/plusone.js">{lang: \'' . $langTag . '\'}</script>';
                if ($this->params->get('google_plus_use_html5') == 0) {
                    $html[] = $googleButton;
                } else {
                    $html[] = $html5Button;
                }
            }
        } else {
            // parse tags when document already
            $script[] = '<script type="text/javascript" src="https://apis.google.com/js/plusone.js">{lang: \'' . $langTag . '\', parsetags: \'explicit\'}	</script>';
            if ($this->params->get('google_plus_use_html5') == 0) {
                $html[] = $googleButton;
            } else {
                $html[] = $html5Button;
            }
            $script[] = '<script type="text/javascript">gapi.plus.go();</script>';
        }

        $html[] = '</div>';
        return array(implode($html), implode($script));
    }

    public function getStumbleButton($link_article) {
        $html = array();
        $html[] = '<div class="bt-social-share-button bt-stumble-button">';
        $html[] = '<su:badge layout="'.$this->params->get("stumble_type", 1).'"  location="'.$link_article.'"></su:badge>';
        //$html[] = '<script src="http://www.stumbleupon.com/hostedbadge.php?s=' . $this->params->get("stumble_type", 1) . '&r=' . rawurlencode($link_article) . '"></script>';
        $html[] = '</div>';
        $script = '<script type="text/javascript">(function() { var li = document.createElement(\'script\'); li.type = \'text/javascript\'; li.async = true; 
    li.src = window.location.protocol + \'//platform.stumbleupon.com/1/widgets.js\';var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(li, s); })();</script>';
        return array(implode($html),$script);
    }

    public function getDiggButton($link_article, $title) {
        $html = array();
        $script = array();
        $script[] = '<script type="text/javascript">';
        $script[] = '(function() {';
        $script[] = 'var s = document.createElement(\'SCRIPT\'), s1 = document.getElementsByTagName(\'SCRIPT\')[0];';
        $script[] = 's.type = \'text/javascript\';';
        $script[] = 's.async = true;';
        $script[] = 's.src = \'http://widgets.digg.com/buttons.js\';';
        $script[] = 's1.parentNode.insertBefore(s, s1);';
        $script[] = '})();';
        $script[] = '</script>';

        $html[] = '<div class="bt-social-share-button bt-dig-button">';
        $html[] = '<a ';
        $html[] = 'class="DiggThisButton ' . $this->params->get("digg_type", "DiggCompact") . '"';
        $html[] = 'href="http://digg.com/submit?url=' . rawurlencode($link_article) . '&amp;title=' . rawurlencode($title) . '">';
        $html[] = '</a>';
        $html[] = '</div>';

        return array(implode($html), implode($script));
    }

    public function getFacebookRecommendation($link_article) {
        $html = array();
        $html[] = '<div class="fb-recommendations-bar" data-href="';
        $html[] = $link_article . '" data-read-time="';
        $html[] = $this->params->get("facebook_recommendation_time", "30") . '" data-action="';
        $html[] = $this->params->get("facebook_like_action", "like") . '" data-trigger="';
        $html[] = $this->params->get("facebook_like_trigger", "onvisible") . '" data-side="';
        $html[] = $this->params->get("facebook_recommendation_side", "2") . '" data-num_recommendations="';
        $html[] = $this->params->get("num_recommendations", "right") . '"></div>';
        return implode($html);
    }

    public function showSocialButtons(&$result, $row = null, $addOg = null) {
        $document = JFactory::getDocument();
		if($row ==null){
			$row = new stdClass();
		}
        if (!isset($row->title)) {
            $row->title = $document->getTitle();
        }
		if (!isset($row->description)) {
            $row->description = $document->getDescription();
        }
        if (!isset($row->link)) {
            $row->link = JURI::getInstance()->toString();
        }
		if(!substr_count($row->link,'http://')){
            $row->link = JURI::root().$row->link;
        }
		if (!isset($row->type)) {
			$row->type ='website';
		}
        $row->title = $this->cleanText($row->title);
        $row->description = substr($this->cleanText($row->description,true), 0, 300);
        $lang = JFactory::getLanguage();
        $langTag = $lang->getTag();
        $langTagArr = explode('-', $langTag);

        $document->addStyleSheet(JURI::root() . '/plugins/content/bt_socialshare/assets/bt_socialshare.css');
		$header = $document->getHeadData();
		$addOg = $addOg== null ? $this->params->get('og_meta',1):$addOg;
		foreach($header['custom'] as $custom){
			if(substr_count($custom,'<meta property="og:title"'))
			{
				$addOg =false;
				break;
			}
		}
        if ($addOg) {			
			$document->addCustomTag('<meta property="og:type" content="'.$row->type.'" />');
            $document->addCustomTag('<meta property="og:title" content="' . $row->title . '" />');
            $document->addCustomTag('<meta property="og:url" content="' . $row->link . '" />');
            if (isset($row->image)) {
                $document->addCustomTag('<meta property="og:image" content="' . $row->image . '" />');
            }
            if (isset($row->description)) {
                $document->addCustomTag('<meta property="og:description" content="' . $row->description . '" />');
            }
        }
        if (trim($this->params->get('facebook_api_id'))) {
            $fb_api = "&appId=" . trim($this->params->get('facebook_api_id'));
            if ($addOg) {
                $document->addCustomTag('<meta property="fb:app_id" content="' . trim($this->params->get('facebook_api_id')) . '" />');
                $fb_admin_ids = trim($this->params->get('facebook_admins'));
                if ($fb_admin_ids) {
                    $document->addCustomTag('<meta property="fb:admins" content="' . $fb_admin_ids . '" />');
                }
            }
        } else {
            $fb_api = "";
        }

        $html = '<div class="bt-social-share">';
        $script = '<div id="fb-root"></div>
				<script>(function(d, s, id) {
				  var js, fjs = d.getElementsByTagName(s)[0];
				  if (d.getElementById(id)) {return;}
				  js = d.createElement(s); js.id = id;
				  js.src = "//connect.facebook.net/' . str_replace('-', '_', $langTag) . '/all.js#xfbml=1' . $fb_api . '";
				  fjs.parentNode.insertBefore(js, fjs);
				}(document, \'script\', \'facebook-jssdk\'));</script>';
        #share FB
        if ($this->params->get('facebook_share_button') == 1) {
            $html .= $this->getFacebookShareButton($row->link, $row->title);
        }
        # like FB 
        if ($this->params->get('facebook_like') == 1) {
            $html .= $this->getFacebookeLikeButton($row->link);
        }
        #for twitter
        if ($this->params->get('twitter') == 1) {
            $twitterArrg = $this->getTwitterButton($row->link, $langTagArr[0]);
            $html .= $twitterArrg[0];
            $script .= $twitterArrg[1];
        }

        #for linkedin
        if ($this->params->get('linkedin') == 1) {
            $linkedin = $this->getLinkedinButton($row->link);
            $html .= $linkedin[0];
            $script .= $linkedin[1];
        }

        #for google plus
        if ($this->params->get('google_plus') == 1) {
            $googlePlus = $this->getGooglePlusButton($row->link, $langTag);
            $html .= $googlePlus[0];
            $script .= $googlePlus[1];
        }

        #for stumble
        if ($this->params->get("stumble")) {
           //$html .= $this->getStumbleButton($row->link);
            $stumbleButton = $this->getStumbleButton($row->link);
            $html .= $stumbleButton[0];
            $script .= $stumbleButton[1];
        }

        #for digg
        if ($this->params->get("digg")) {
            $diggButton = $this->getDiggButton($row->link, $row->title);
            $html .= $diggButton[0];
            $script .= $diggButton[1];
        }
        $html .= '</div>';
        $recommendationBar = '';
        if ($this->params->get("facebook_recommendation")) {
            $recommendationBar = $this->getFacebookRecommendation($row->link);
        }
        $commendBox = '';
        if ($this->params->get("facebook_comment")) {
            $commendBox .= $this->getFacebookCommentBox($row->link, $row->title);
        }
        $result = array('script' => $script, 'buttons' => $html, 'recommend' => $recommendationBar, 'comment' => $commendBox);
    }
	
	
	/** using this function in other place
	<?php 
		JPluginHelper::importPlugin('content');
		$share = plgContentBt_socialshare::socialButtons();
		echo $share['script']; // Required
		echo $share['buttons']; // Social button
		echo $share['recommend']; // Recommendation bar
		echo $share['comment']; // facebook comment box
	?>
	
	$row:  object of item
	$addOg: add opengraph metadata
	*/
	public static function socialButtons($row = null, $addOg = null){
        $dispatcher = JDispatcher::getInstance();
        $result = array();
        $dispatcher->trigger('showSocialButtons', array(&$result,$row, $addOg));
		return $result;
	}
	protected function cleanText($text, $stripTags = false){
		if($stripTags) $text = strip_tags($text);
		$text = str_replace('"','&quot;',$text);
		return $text;
	}
	protected function getPretext($type='button'){
		$pretext = '';
		if($type=='button'){
			if($this->params->get('button-pretext')){
				$pretext = '<span class="bt-pretext">'.$this->params->get('button-pretext').'</span>';
			}
		}else{
			if($this->params->get('comment-pretext')){
				$pretext = '<div style="clear:both"><h3>'.$this->params->get('comment-pretext').'</h3>';
			}
		}
		return $pretext;
	}
	
	/**
	 * Display Facebook comment count
	 * @since 2.3.3
	 */
	function onK2CommentsCounter( &$item, &$params, $limitstart) {
		if(JFactory::getApplication()->isAdmin()){
			return;
		}
		if($this->params->get('show_cm_count')){
			$uri = JURI::getInstance();
			$link = $uri->getScheme() . "://" . $uri->getHost() . $item->link;
			$json = json_decode(file_get_contents('http://graph.facebook.com/?ids=' . $link));
			if(isset($json->$link->comments)){
				$commentCount = $json->$link->comments ? $json->$link->comments : 0;
			}else{
				$commentCount = 0;
			}
			JPlugin::loadLanguage('plg_content_bt_socialshare', JPATH_ADMINISTRATOR);
			if(!$commentCount) $output = sprintf(JText::_('PLG_BT_SOCIALSHARE_FB_CM_HTML'), $item->link . '#bt_facebook_comment', JText::_('PLG_BT_SOCIALSHARE_FB_NO_CM_TEXT'));
			else if($commentCount == 1) $output = sprintf(JText::_('PLG_BT_SOCIALSHARE_FB_CM_HTML'), $item->link . '#bt_facebook_comment', '1 ' . JText::_('PLG_BT_SOCIALSHARE_FB_S_CM_TEXT') );
			else $output = sprintf(JText::_('PLG_BT_SOCIALSHARE_FB_CM_HTML'), $item->link . '#bt_facebook_comment', $commentCount . ' ' . JText::_('PLG_BT_SOCIALSHARE_FB_P_CM_TEXT'));
			return $output;
		}
		return '';
	}
}