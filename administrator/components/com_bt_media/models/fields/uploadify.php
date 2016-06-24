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

class JFormFieldUploadify extends JFormField {

    protected $type = 'uploadify';

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

        if ($allowUploadExt == '') {
            return JText::_('COM_BT_MEDIA_NOT_PERMISSION_FILE_UPLOAD');
        }
        if ($params->get('file_upload_type', 'flash') == 'flash' && $browser['name'] != 'msie') {
            $comURL = JURI::root() . 'administrator/components/com_bt_media';
            $session = JFactory::getSession();
            $html = '<script type="text/javascript">
            $BM(document).ready(function() {
                $BM("#image-uploadify").uploadify({
                    "buttonClass": "custom-button bt-enable",
                    "buttonText" : "UPLOAD",
                    "swf"  : "' . $comURL . '/lib/uploadify/uploadify.swf",
                    "uploader"    : "' . JURI::base() . 'index.php",
                    "formData" : {"option":"com_bt_media", "task":"detail.uploadify", "' . $session->getName() . '":"' . $session->getId() . '", "' . JSession::getFormToken() . '":1},
                    "fileTypeExts"     : "' . $allowUploadExt . '",
                    "fileTypeDesc"    : "Allow Files Upload",
                    "width"     : 110,
                    "height"    : 35,
                    "auto"      : true,
                    "multi"     : ' . ((JFactory::getUser()->authorise('media.multi.upload', 'com_bt_media')) ? 'true' : 'false') . ',
                    "method"    : "POST",
                    "itemTemplate" : "<div id=\"${fileID}\" class=\"uploadify-queue-item\"><div class=\"cancel\"><a href=\"javascript:$BM(\'#${instanceID}\').uploadify(\'cancel\', \'${fileID}\')\">X</a></div><span class=\"fileName\">${fileName} (${fileSize})</span><span class=\"data\"></span></div>",
                    onUploadSuccess: function(file, data, response){
                        var a = $BM.parseJSON(data);
                        if(a.success){
                            $BM("#display_upload_image .img-list").append(a.data);
                            $BM("#display_upload_image .img-list li:last-child").fadeIn(1000);
                            $BM("#display_upload_image .display-message").append("<li class=\"success-rs\">"+a.message+"</li>");
                            $BM("#display_upload_image ul.display-message li:last-child").fadeIn(250).delay(1500).fadeOut(250);
                        }else{
                            $BM("#display_upload_image .display-message").append("<li class=\"error-rs\">"+a.message+"</li>");
                            $BM("#display_upload_image ul.display-message li:last-child").fadeIn(250).delay(1500).fadeOut(250);
                        }
                    }
                });
                $BM("#uploadify-upload").click(function(){
                    $BM("#uploadify-file").uploadifyUpload();
                    return false;
                });
            });
        </script>
        <input class="btss-ac upload" id="image-uploadify" type="file" name="uploadify-file" />';
            return $html;
        } elseif ($params->get('file_upload_type', 'flash') == 'html5') {
            $html = array();
            $document->addScript(JUri::root() . 'administrator/components/com_bt_media/assets/js/jquery.filedrop.js');
            $html[] = '<div id="dropbox">
			<span class="message">' . JText::_('Drop images here to upload. <br /><i>(they will only be visible to you)</i>') . '</span>
		</div>';
            $html[] = '
                <script type="text/javascript">
                    var dropbox = $BM("#dropbox"),
                    message = $BM(".message", dropbox);

                    dropbox.filedrop({
                    // The name of the $_FILES entry:
                    paramname: "pic",
                    maxfiles: ' . ((JFactory::getUser()->authorise('media.multi.upload', 'com_bt_media')) ? 20 : 1) . ',
                    maxfilesize: 5,
                    queuewait:5,
                    url: "' . JUri::base() . 'index.php",
                    data: {"task":"detail.html5Upload", "option":"com_bt_media", "view":"detail","layout":"add"},
                    uploadFinished: function(i, file, response) {
                        if(response.success){
                            $BM.data(file).find(".progress").css({"width": "100%"});
                            $BM("#display_upload_image .img-list").append(response.data);
                            $BM("#display_upload_image .img-list li:last-child").fadeIn(1000, function(){
                                $BM.data(file).remove();
                            });
                            $BM("#display_upload_image .display-message").append("<li class=\"success-rs\">"+response.message+"</li>");
                            $BM("#display_upload_image ul.display-message li:last-child").fadeIn(250).delay(1500).fadeOut(250);
                        }else{
                            $BM("#display_upload_image .display-message").append("<li class=\"error-rs\">"+response.message+"</li>");
                            $BM("#display_upload_image ul.display-message li:last-child").fadeIn(250).delay(1500).fadeOut(250);
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
                                alert(file.name + " is too large! Please upload files up to 5mb (configurable).");
                                break;
                            default:
                                break;
                        }
                    },
                    // Called before each upload is started
                    beforeEach: function(file) {
                        if (!file.type.match(/^image\//) && !file.type.match(/^video\//)) {
                            alert("File type upload not allowed!");

                            // Returning false will cause the
                            // file to be rejected
                            return false;
                        }
                    },
                    drop: function(e){
                        var files = e.dataTransfer.files;
                        for(var i = 0; i < files.length; i++){
                            if(files[i].type.match(/^image\//)){
                                createImage(files[i]);
                            }
                            if(files[i].type.match(/^video\//)){
                                createVideo(files[i]);
                            }
                            
                        }
                        return true;
                    },
                    progressUpdated: function(i, file, progress) {
                        $BM.data(file).find(".progress").css({"width": progress+"%"});
                    }

                });

                var template = "<div class=\"preview\">" +
                        "<span class=\"imageHolder\">" +
                        "<img />" +
                        "<span class=\"uploaded\"></span>" +
                        "</span>" +
                        "<div class=\"progressHolder\">" +
                        "<div class=\"progress\"></div>" +
                        "</div>" +
                        "</div>";

                function createVideo(file) {

                    var preview = $BM(template),
                        title = $BM("span", preview),
                        file_name = file.name.split(".")[0];
                         
                        title.html(file_name);
                    message.hide();
                    preview.appendTo(dropbox);

                    $BM.data(file, preview);
                }

                function createImage(file) {

                    var preview = $BM(template),
                            image = $BM("img", preview);

                    var reader = new FileReader();

                    image.width = 100;
                    image.height = 100;

                    reader.onload = function(e) {

                        // e.target.result holds the DataURL which
                        // can be used as a source of the image:

                        image.attr("src", e.target.result);
                    };

                    // Reading the file as a DataURL. When finished,
                    // this will trigger the onload function above:
                    reader.readAsDataURL(file);

                    message.hide();
                    preview.appendTo(dropbox);

                    // Associating a preview container
                    // with the file, using $BM\'s $.data():

                    $BM.data(file, preview);
                }

                function showMessage(msg) {
                    message.html(msg);
                }
                </script>
                ';
            return implode($html);
        } else {
            $html = array();
            $html[] = '<ul class="uploading">';
            $html[] = '<li>';
            $html[] = '<label class="" aria-invalid="false">' . JText::_('Select file') . '</label> ';
            $html[] = '<input type="file" name="fileuploads[]" class="" aria-invalid="false">';
            if (JFactory::getUser()->authorise('media.multi.upload', 'com_bt_media')) {
                $html[] = '<a onclick="return addFile(this)" class="button" href="#">+</a>';
            }
            $html[] = '</li>';
            $html[] = '</ul>';
            return implode($html);
        }
    }

}

?>