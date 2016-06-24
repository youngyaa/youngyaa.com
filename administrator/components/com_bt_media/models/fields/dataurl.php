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
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

//jimport('joomla.html.html');
//jimport('joomla.form.formfield');

/**
 * Supports an HTML select list of categories
 */
class JFormFieldDataUrl extends JFormFieldList {

    /**
     * The form field type.
     *
     * @var		string
     * @since	1.6
     */
    protected $type = 'dataurl';

    /**
     * Method to get the field input markup.
     *
     * @return	string	The field input markup.
     * @since	1.6
     */
    protected function getInput() {
        // Initialize variables.
        $html = array();
        $html[] = '<input type="text" name="' . $this->name . '" id="' . $this->id . '"/>';
        $html[] = '<a href="javascript:getData(this);"><div class="bt-button">Load data<span style="display:none;" class="ajax-loading"><img src="'.JUri::root().'administrator/components/com_bt_media/assets/images/ajax-loader_2.gif"/></span></div></a>';
        $html[] = '
                <script type="text/javascript">
                function getData(el){
                   var url = $BM("#' . $this->id . '").val();
                   var file_name = $BM("#jform_image_path").val();
                   var source_server = $BM("#jform_source_of_media").val();
                   var media_type = $BM("#jform_media_type").val();
                   if(isValidURL(url)){
                        $BM.ajax({
                             url: btMediaCfg.siteURL + "index.php",
                             data: "option=com_bt_media&task=detail.getDataUrl&filename=" +file_name+ "&source_server="+source_server+"&media_type=" +media_type+ "&data=" + encodeURIComponent(url),
                             type: "post",
                             beforeSend: function() {
                                 $BM("#' . $this->id . '").parent().find(".bt-button span").show();
                             },
                             success: function(response) {
                                 var a = $BM.parseJSON(response);
                                 if (a.success) {
                                    $BM("#jform_image_path").val(a.data[0]);
                                    $BM("#thumb-view .img").fadeOut(1000, function(){
                                        $BM("#thumb-view .img").html(a.data[1]).fadeIn();
                                    });
                                    var objVideo = a.data[2];
                                    $BM("#jform_name").val(objVideo.name);
                                    $BM("#jform_alias").val("");
                                    $BM("#jform_tags").val(objVideo.tags);
                                    $BM("#jform_media_type").val(objVideo.media_type);
                                    $BM("#jform_source_of_media").val(objVideo.source_of_media);
                                    if(objVideo.video_path){
                                        $BM("#jform_video_path").val(objVideo.video_path);
                                    }
                                    tinyMCE.get("jform_description").setContent(objVideo.description);
                                    $BM(this).parent().find("span.loadding").html("").fadeOut(500);
                                    $BM("#' . $this->id . '").parent().find(".bt-button span").hide();
                                 }else{
                                        $BM("#system-message-container").html(a.message).fadeIn(300).delay(2000).fadeOut(300);
                                  }
                             }
                         });
                   }else{
                        alert("Url not valid!");
                   }
                }
                
                function isValidURL(url){
                    var RegExp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;

                    if(RegExp.test(url)){
                        return true;
                    }else{
                        return false;
                    }
                } 
                </script>
            ';
        return implode($html);
    }

}