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
class JFormFieldFlickrFetchImage extends JFormField {

    /**
     * The form field type.
     *
     * @var		string
     * @since	1.6
     */
    protected $type = 'flickrfetchimage';

    /**
     * Method to get the field input markup.
     *
     * @return	string	The field input markup.
     * @since	1.6
     */
    protected function getInput() {
        // Initialize variables.
        $html = array();

        $html[] = '<div id="btnGetImagesFromFlickr"><div class="custom-button bt-disable"><strong>' . JText::_('Get Images') . '</strong></div></div>';
        $html[] = '
        <script type="text/javascript">
            $BM(document).ready(function(){
            $BM("#jform_flickr_username").blur(function(){
                var uname = $BM("#jform_flickr_username").val();
               btDisable("flickr");
                if(uname == ""){
                    $BM("#flickr li.message-display").removeClass("ajax-display").fadeOut(1000, function(){
                        $BM("#flickr li.message-display").addClass("error").html("Please inupt flickr username or email!").fadeIn(1000);
                    })
                }else{
                    getAlbums("get_image", "flickr", "getalbums", uname);
                }
            });
            
            $BM("#btnGetImagesFromFlickr .custom-button").click(function(){
                if($BM(this).hasClass("bt-enable")){
                    var albumid = $BM("#jform_flickr_albumid").val();
                    if(albumid != "" && albumid != 0){
                        $BM("#flickr .ajax-loading").removeClass("load-complete").addClass("loader").html("").fadeIn(500);
                        $BM.ajax({
                            url: btMediaCfg.siteURL + "index.php",
                            data: "option=com_bt_media&task=detail.getImage&from=flickr&act=getphotos&album="+albumid,
                            type: "post",
                            beforeSend: function(a,b){
                                $BM("#btnGetImagesFromFlickr .custom-button").removeClass("bt-enable").addClass("bt-disable");
                            },
                            success: function(data){
                                var a = $BM.parseJSON(data);
                                var from = $BM("#jform_flickr_from").val();
                                var to = $BM("#jform_flickr_to").val();
                                if(a.success){
                                    comMediaGetItem("flickr", a.data, from, to, false);
                                }
                            }
                        });
                    }
                }
            });
        });
        
        </script>
        ';
        return implode($html);
    }

}