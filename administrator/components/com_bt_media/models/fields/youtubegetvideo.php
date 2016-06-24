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
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

/**
 * Supports an HTML select list of categories
 */
class JFormFieldYoutubeGetVideo extends JFormField {

    /**
     * The form field type.
     *
     * @var		string
     * @since	1.6
     */
    protected $type = 'youtubegetvideo';

    /**
     * Method to get the field input markup.
     *
     * @return	string	The field input markup.
     * @since	1.6
     */
    protected function getInput() {
        // Initialize variables.
        $html = array();
        $html[] = '<div id="btnGetVideosFromYoutube"><div class="custom-button bt-disable"><strong>' . JText::_('Get Video') . '</strong></div></div>';

        $html[] = '
        <script type="text/javascript">
            $BM(document).ready(function(){
            $BM("#jform_youtube_url").blur(function(){
                var yt_url = $BM("#jform_youtube_url").val();
                getvideoFromURL("youtube", yt_url);
            });
            
            $BM("#btnGetVideosFromYoutube .custom-button").click(function(){
                var getby = $BM("#jform_youtube_get_by").val();
                var data = "";
                if(getby == "username"){
                    var uname = $BM("#jform_youtube_username").val();
                    var pl = $BM("#jform_youtube_playlists").val();
                    if(pl == ""){
                        if(uname != ""){
                            data = "user|"+uname;
                        }
                    }else{
                        data = pl;
                    }
                }
                if(getby == "playlist_video"){
                    data = $BM("#jform_youtube_data").val();
                }
                
                
                if($BM("#btnGetVideosFromYoutube .custom-button").hasClass("bt-enable")){
                    $BM("#youtube .ajax-loading").removeClass("load-complete").addClass("loader").html("").fadeIn(500);
                    $BM.ajax({
                        url: btMediaCfg.siteURL + "index.php",
                        data: "option=com_bt_media&task=detail.getVideo&from=youtube&method=getvideos&data="+data,
                        type: "post",
                        beforeSend: function(a,b){
                                $BM("#btnGetVideosFromYoutube .custom-button").removeClass("bt-enable").addClass("bt-disable");
                        },
                        success: function(response){
                            var data = $BM.parseJSON(response);
                            var from = $BM("#jform_youtube_from").val();
                            var to = $BM("#jform_youtube_to").val();
                            if(data.success){
                                if(typeof(data.data)=="object"&&(data.data instanceof Array)){
                                    comMediaGetItem("youtube", data.data, from, to, false);
                                }else{
                                    comMediaGetItem("youtube", new Array(data.data), from, to, false);
                                }
                            }else{
                                $BM("#youtube .ajax-loading").removeClass("loader").addClass("load-complete").html("").fadeOut();
                            }
                        }
                    });
                }
            });
        });
        
        </script>
        ';

        return implode($html);
    }

}