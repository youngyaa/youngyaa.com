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
// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.form.formfield');

class JFormFieldImageVideoUpload extends JFormField {

    protected $type = 'imagevideoupload';

    protected function getInput() {
        $document = JFactory::getDocument();
        $params = JComponentHelper::getParams('com_bt_media');
        $browser = Bt_mediaLegacyHelper::getBrowser();
        $allowUploadExt = '';
        if (JFactory::getUser()->authorise('media.upload.image', 'com_bt_media')) {
            $allowUploadExt .= '*.jpg;*.jpeg;*.gif;*.png';
        }
        if (JFactory::getUser()->authorise('media.upload.video', 'com_bt_media')) {
            if ($allowUploadExt == '') {
                $allowUploadExt .= '*.flv;*.mp4';
            } else {
                $allowUploadExt .= ';*.flv;*.mp4';
            }
        }
        
        if($allowUploadExt == '')  {
            return JText::_('COM_BT_MEDIA_NOT_PERMISSION_FILE_UPLOAD');
        }
        if ($params->get('file_upload_type', 'flash') == 'flash' && $browser['name'] != 'msie') {
            $comURL = JURI::root() . 'administrator/components/com_bt_media';
            $session = JFactory::getSession();
            $html =
                    '<script type="text/javascript">
            $BM(document).ready(function() {
            var file_name = $BM("#jform_image_path").val();
            var source_server = $BM("#jform_source_of_media").val();
                $BM("#image-uploadify").uploadify({
                    "buttonClass": "image-update-new",
                    "buttonText" : "Upload media",
                    "swf"  : "' . $comURL . '/lib/uploadify/uploadify.swf",
                    "uploader"    : "' . JURI::base() . 'index.php",
                    "formData" : {"option":"com_bt_media", "task":"detail.editImageUpload", "filename": file_name, "source_server": source_server, "' . $session->getName() . '":"' . $session->getId() . '", "' . JSession::getFormToken() . '":1},
                    "fileTypeExts"     : "'.$allowUploadExt.'",
                    "fileTypeDesc"    : "Media Files",
                    "width"     : 200,
                    "height"    : 35,
                    "multi"     : false,
                    "auto"      : true,
                    "method"    : "POST",
                    "itemTemplate" : "<div id=\"${fileID}\" class=\"uploadify-queue-item\"><div class=\"cancel\"><a href=\"javascript:$BM(\'#${instanceID}\').uploadify(\'cancel\', \'${fileID}\')\">X</a></div><span class=\"fileName\">${fileName} (${fileSize})</span><span class=\"data\"></span></div>",
                    onUploadSuccess: function(file, data, response){
                        var a = $BM.parseJSON(data);
                        if(a.success){
                            $BM("#jform_image_path").val(a.data[0]);
                            $BM("#jform_source_of_media").val(a.data[2]);
                            $BM("#thumb-view .img").fadeOut(1000, function(){
                                $BM("#thumb-view .img").html(a.data[1]).fadeIn();
                            });
                        }else{
                            $BM("#system-message-container").html(a.message).fadeIn(300).delay(2000).fadeOut(300);
                        }
                    }
                });
                $BM("#uploadify-upload").click(function(){
                    $BM("#uploadify-file").uploadifyUpload();
                    return false;
                });
            });
        </script>
        <input class="bt-ig-ac upload" id="image-uploadify" type="file" name="uploadify-file" />';
            return $html;
        } elseif ($params->get('file_upload_type', 'flash') == 'html5') {
            $document->addScript(JUri::root() . 'administrator/components/com_bt_media/assets/js/jquery.filedrop.js');
            $html = array();
            $itemID = intval($this->form->getValue('id'));

            $html[] = '<div class="message">' . JText::_('Drag and Drop an image in here to change image for media item.') . '</div>';
            $html[] = '<div class="progressHolder" style="display:none;"><div class="progress"></div></div>';
            $html[] = '
                <script type="text/javascript">
                    var dropbox = $BM("#thumb-view"),
                    control_bar = $BM(".change-buttom", dropbox),
                    message = $BM(".message", control_bar),
                    progress = $BM(".progressHolder", control_bar);

                    dropbox.filedrop({
                    // The name of the $_FILES entry:
                    paramname: "update_pic",
                    maxfiles: 1,
                    maxfilesize: 2,
                    url: "' . JUri::base() . 'index.php",
                    data: {"option":"com_bt_media", "task":"detail.html5UpdateUpload", "id":' . $itemID . '},
                    uploadFinished: function(i, file, response) {
                        if(response.success){
                            $BM.data(file).find(".progress").css({"width": "100%"});
                            $BM("#jform_image_path").val(response.data[0]);
                            $BM("#jform_source_of_media").val(response.data[2]);
                            $BM("#thumb-view .img").fadeOut(1000, function(){
                                $BM("#thumb-view .img").html(response.data[1]).fadeIn(1000, function(){
                                    progress.fadeOut(1000, function(){
                                        message.fadeIn(1000);
                                    });
                                });
                            });
                        }
                    },
                    error: function(err, file) {
                        switch (err) {
                            case "BrowserNotSupported":
                                showMessage("Your browser does not support HTML5 file uploads!");
                                break;
                            case "TooManyFiles":
                                alert("Too many files! Please select 5 at most! (configurable)");
                                break;
                            case "FileTooLarge":
                                alert(file.name + " is too large! Please upload files up to 2mb (configurable).");
                                break;
                            default:
                                break;
                        }
                    },
                    // Called before each upload is started
                    beforeEach: function(file) {
                        if (!file.type.match(/^image\//) && !file.type.match(/^video\//)) {
                            alert("Only images or videos are allowed!");

                            // Returning false will cause the
                            // file to be rejected
                            return false;
                        }
                    },
                    drop: function(e){
                        message.hide();
                        progress.show();
                        var files = e.dataTransfer.files;
                        createProgressBar(files[0]);
                        return true;
                    },
                    progressUpdated: function(i, file, progress) {
                        $BM.data(file).find(".progress").css({"width": progress+"%"});
                    }

                });

                function createProgressBar(file) {

                   var reader = new FileReader();

                    // Reading the file as a DataURL. When finished,
                    // this will trigger the onload function above:
                    reader.readAsDataURL(file);

                    message.hide();
                    progress.show();

                    // Associating a preview container
                    // with the file, using $BM\'s $.data():

                    $BM.data(file, progress);
                }

                function showMessage(msg) {
                    message.html(msg);
                }
                </script>
                ';
            return implode($html);
        } else {
            return '<input type="file" name="image" class="" aria-invalid="false" style="width:100%;">';
        }
    }

}

?>
