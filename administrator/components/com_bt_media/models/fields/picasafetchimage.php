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
class JFormFieldPicasaFetchImage extends JFormField {

    /**
     * The form field type.
     *
     * @var		string
     * @since	1.6
     */
    protected $type = 'picasafetchimage';

    /**
     * Method to get the field input markup.
     *
     * @return	string	The field input markup.
     * @since	1.6
     */
    protected function getInput() {
        // Initialize variables.
        $html = array();

        $html[] = '<div id="btnGetImagesFromPicasa"><div class="custom-button bt-disable"><strong>' . JText::_('Get Images') . '</strong></div></div>';
        $html[] = '
        <script type="text/javascript">
            $BM(document).ready(function(){
            $BM("#jform_picasa_username").blur(function(){
                btDisable("picasa");
                var uname = $BM("#jform_picasa_username").val();
                if(uname == ""){
                    $BM("#tabs-5 li.message-display").removeClass("ajax-display").fadeOut(1000, function(){
                        $BM("#tabs-5 li.message-display").addClass("error").html("Please input picasa username!").fadeIn(1000);
                    })
                }else{
                    getAlbums("get_image", "picasa", "getalbums", uname);
                }
            });
            $BM("#btnGetImagesFromPicasa .custom-button").click(function(){
                if($BM(this).hasClass("bt-enable")){
                    var uname = $BM("#jform_picasa_username").val();
                    var albumid = $BM("#jform_picasa_albumid").val();
                    if(albumid != "" && albumid != 0){
                        $BM("#picasa .ajax-loading").removeClass("load-complete").addClass("loader").html("").fadeIn(500);
                        $BM.ajax({
                            url: btMediaCfg.siteURL + "index.php",
                            data: "option=com_bt_media&task=detail.getImage&from=picasa&act=getphotos&username="+uname+"&album="+albumid,
                            type: "post",
                            beforeSend: function(a,b){
                                $BM("#btnGetImagesFromPicasa .custom-button").removeClass("bt-enable").addClass("bt-disable");
                            },
                            success: function(data){
                                var a = $BM.parseJSON(data);
                                var from = $BM("#jform_picasa_from").val();
                                var to = $BM("#jform_picasa_to").val();
                                if(a.success){
                                    comMediaGetItem("picasa", a.data, from, to, false);
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
