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
class JFormFieldVimeoGetVideo extends JFormField {

    /**
     * The form field type.
     *
     * @var		string
     * @since	1.6
     */
    protected $type = 'vimeogetvideo';

    /**
     * Method to get the field input markup.
     *
     * @return	string	The field input markup.
     * @since	1.6
     */
    protected function getInput() {
        // Initialize variables.
        $params = JComponentHelper::getParams('com_bt_media');
        $html = array();
        $html[] = '<div id="btnGetVideosFromVimeo"><div class="custom-button bt-disable"><strong>' . JText::_('Get Video') . '</strong></div></div>';

        $html[] = '
        <script type="text/javascript">
            $BM(document).ready(function(){
            $BM("#jform_vimeo_url").blur(function(){
                var vm_url = $BM("#jform_vimeo_url").val();
                getvideoFromURL("vimeo", vm_url);
            });
            
            $BM("#btnGetVideosFromVimeo .custom-button").click(function(){
                var getby = $BM("#jform_vimeo_get_by").val();
                var data = "";
                if(getby == "username"){
                    data = $BM("#jform_vimeo_playlists").val();
                }
                if(getby == "playlist_video"){
                    data = $BM("#jform_vimeo_data").val();
                }
                if($BM("#btnGetVideosFromVimeo .custom-button").hasClass("bt-enable")){
                    $BM("#vimeo .ajax-loading").removeClass("load-complete").addClass("loader").html("").fadeIn(500);
                    $BM.ajax({
                        url: btMediaCfg.siteURL + "index.php",
                        data: "option=com_bt_media&task=detail.getVideo&from=vimeo&method=getvideos&data="+data,
                        type: "post",
                        beforeSend: function(a,b){
                                $BM("#btnGetVideosFromVimeo .custom-button").removeClass("bt-enable").addClass("bt-disable");
                        },
                        success: function(response){
                            var data = $BM.parseJSON(response);
                            var from = $BM("#jform_vimeo_from").val();
                            var to = $BM("#jform_vimeo_to").val();
                            if(data.success){
                                if(typeof(data.data)=="object"&&(data.data instanceof Array)){
                                    comMediaGetItem("vimeo", data.data, from, to, false);
                                }else{
                                    comMediaGetItem("vimeo", new Array(data.data), from, to, false);
                                }
                            }else{
                                $BM("#vimeo .ajax-loading").removeClass("loader").addClass("load-complete").html("").fadeOut();
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