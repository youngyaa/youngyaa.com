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
defined('_JEXEC') or die;
if (!Bt_mediaLegacyHelper::isLegacy()) {
    JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
    JHtml::_('formbehavior.chosen', 'select');
}
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

$user = JFactory::getUser();
$sessions = JFactory::getSession();
$params = JComponentHelper::getParams('com_bt_media');
$canUploadImage = $user->authorise('media.upload.image', 'com_bt_media');
$canGetImage = $user->authorise('media.get.image', 'com_bt_media');
$canUploadVideo = $user->authorise('media.upload.video', 'com_bt_media');
$canGetVideo = $user->authorise('media.get.video', 'com_bt_media');
?>
<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        if (task == 'detail.cancel' || document.formvalidator.isValid(document.id('detail-form'))) {
            Joomla.submitform(task, document.getElementById('detail-form'));
        }
        else {
            alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
        }
    };
</script>
<form action="<?php echo JRoute::_('index.php?option=com_bt_media&layout=add'); ?>" method="post" name="adminForm" id="detail-form" class="form-validate" enctype="multipart/form-data" style="display: block;" >
    <fieldset class="adminform">
        <ul class="adminformlist head-fields" style="margin-bottom: 15px;">
            <li><?php echo $this->form->getLabel('cate_id'); ?>
                <?php echo $this->form->getInput('cate_id'); ?>
                <?php echo $this->form->getLabel('language'); ?>
                <?php echo $this->form->getInput('language'); ?> 
                <?php echo $this->form->getLabel('access'); ?>
                <?php echo $this->form->getInput('access'); ?>
                <?php echo $this->form->getLabel('created_by'); ?>
                <?php echo $this->form->getInput('created_by'); ?></li> 
            <li><?php echo $this->form->getInput('asset'); ?></li>
        </ul>
        <?php if ($canGetImage || $canUploadImage || $canGetVideo || $canUploadVideo): ?>

            <div id="tabs" style="border: none;">
                <ul class="head-content-tab">
                    <?php if ($canUploadImage || $canUploadVideo): ?>
                        <li class="li-button upload"><a href="#upload"><?php echo JText::_('Upload from local'); ?></a></li>
                    <?php endif; ?>
                    <?php if ($canGetImage || $canGetVideo): ?>
                        <li class="li-button folder"><a href="#folderget"><?php echo JText::_('Upload from folder'); ?></a></li>
                        <?php if ($canGetImage): ?>
                            <li class="li-button flickr"><a href="#flickr"><?php echo JText::_('Get from Flick'); ?></a></li>
                            <li class="li-button picasa"><a href="#picasa"><?php echo JText::_('Get from Picasa'); ?></a></li>
                            <?php
                        endif;
                        if ($canGetVideo):
                            ?>
                            <li class="li-button youtube"><a href="#youtube"><?php echo JText::_('Get file from Youtube'); ?></a></li>
                            <li class="li-button vimeo"><a href="#vimeo"><?php echo JText::_('Get file from Vimeo'); ?></a></li>

                            <?php
                        endif;
                    endif;
                    ?>
                    <li class="ajax-display"></li>
                </ul>

                <!--Start upload image tabs-->
                <?php if ($canUploadImage || $canUploadVideo): ?>
                    <div id="upload"> 
                        <div class="content-left">
                            <?php echo $this->form->getInput('files_upload_bt'); ?>
                        </div>
                        <div class="content-right">
                            <div id ="display_upload_image">
                                <ul class="display-message"></ul>
                                <ul class="img-list">
                                    <?php if ($sessions->get('files_upload') != null): ?>
                                        <?php
                                        foreach ($sessions->get('files_upload') as $image_up) {
                                            echo '<li class="image"><img class="img-thumb" src="' . JURI::root() . $params->get('file_save', 'images/bt_media') . '/images/thumbnail/' . $image_up->image_path . '"  />';
                                            echo '<img class="img-delete" onclick="removeImage(this)" src="' . JURI::base() . 'components/com_bt_media/assets/images/delete.png" />';
                                            echo '<input type="hidden" name="image_filename" value="' . $image_up->image_path . '" />';
                                            echo '<input type="hidden" name="old_name" value="' . htmlspecialchars($image_up->name) . '" />';
                                            echo '<input type="hidden" name="session_name" value="files_upload" />';
                                            echo '<div class="edit-title"><input class="input-title" type="text" name="image_title" value="' . htmlspecialchars($image_up->name) . '" onblur="changeName(this)"></div>';
                                            echo '</li>';
                                        }
                                        ?>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                    </div>
                <?php endif; ?>
                <?php if ($canGetImage || $canGetVideo): ?>
                    <div id="folderget"> 
                        <div class="content-left">
                            <div class="support-text"><?php echo JText::_('Click on folder to select one or many folder and again to unselect.') ?></div>
                            <?php echo $this->form->getInput('image_upload_folder'); ?>
                        </div>
                        <div class="content-right">
                            <div class="ajax-loading"></div>
                            <div id ="display_upload_image_from_jfolder">
                                <ul class="display-message"></ul>
                                <ul class="img-list">
                                    <?php if ($sessions->get('images_jfolder') != null): ?>
                                        <?php
                                        foreach ($sessions->get('images_jfolder') as $image_jf) {
                                            echo '<li class="image"><img class="img-thumb" src="' . JURI::root() . $params->get('file_save', 'images/bt_media') . '/images/thumbnail/' . $image_jf->image_path . '"   />';
                                            echo '<img class="img-delete" onclick="removeImage(this)" src="' . JURI::base() . 'components/com_bt_media/assets/images/delete.png" />';
                                            echo '<input type="hidden" name="image_filename" value="' . $image_jf->image_path . '" />';
                                            echo '<input type="hidden" name="old_name" value="' . htmlspecialchars($image_jf->name) . '" />';
                                            echo '<input type="hidden" name="session_name" value="images_jfolder" />';
                                            echo '<div class="edit-title"><input class="input-title" type="text" name="image_title" value="' . htmlspecialchars($image_jf->name) . '" onblur="changeName(this)"></div>';
                                            echo '</li>';
                                        }
                                        ?>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                    </div>
                    <div id="flickr">
                        <div class="content-left">
                            <ul class="adminformlist">
                                <li style="display:none;" class="message-display">
                                </li>
                                <li>
                                    <?php echo $this->form->getLabel('flickr_username'); ?>
                                    <?php echo $this->form->getInput('flickr_username'); ?>
                                </li>
                                <li>
                                    <?php echo $this->form->getLabel('flickr_albumid'); ?>
                                    <?php echo $this->form->getInput('flickr_albumid'); ?>
                                </li> 
                                <li id="i_f_show_count" style="display: none;">
                                </li> 
                                <li id="i_f_from_to" style="display: none;">
                                    <?php echo $this->form->getLabel('flickr_from'); ?>
                                    <?php echo $this->form->getInput('flickr_from'); ?>
                                    <?php echo $this->form->getLabel('flickr_to'); ?>
                                    <?php echo $this->form->getInput('flickr_to'); ?>
                                </li> 
                                <li>  
                                    <?php echo $this->form->getInput('image_flickr_bt'); ?>
                                </li>
                            </ul>
                        </div>
                        <div class="content-right">
                            <div class="ajax-loading"></div>
                            <div id ="display_flickr_image">
                                <ul class="img-list">
                                    <?php if ($sessions->get('images_flickr') != null): ?>
                                        <?php
                                        foreach ($sessions->get('images_flickr') as $image_fk) {
                                            echo '<li class="image"><img class="img-thumb" src="' . JURI::root() . $params->get('file_save', 'images/bt_media') . '/images/thumbnail/' . $image_fk->image_path . '"  />';
                                            echo '<img class="img-delete" onclick="removeImage(this)" src="' . JURI::base() . 'components/com_bt_media/assets/images/delete.png" />';
                                            echo '<input type="hidden" name="image_filename" value="' . $image_fk->image_path . '" />';
                                            echo '<input type="hidden" name="old_name" value="' . htmlspecialchars($image_fk->name) . '" />';
                                            echo '<input type="hidden" name="session_name" value="images_flickr" />';
                                            echo '<div class="edit-title"><input class="input-title" type="text" name="image_title" value="' . htmlspecialchars($image_fk->name) . '" onblur="changeName(this)"></div>';
                                            echo '</li>';
                                        }
                                        ?>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                    </div>
                    <div id="picasa">
                        <div class="content-left">
                            <ul class="adminformlist">
                                <li style="display:none;" class="message-display">
                                </li>
                                <li>
                                    <?php echo $this->form->getLabel('picasa_username'); ?>
                                    <?php echo $this->form->getInput('picasa_username'); ?>
                                </li>
                                <li>
                                    <?php echo $this->form->getLabel('picasa_albumid'); ?>
                                    <?php echo $this->form->getInput('picasa_albumid'); ?>
                                </li>
                                <li id="i_p_show_count" style="display: none;">
                                </li> 
                                <li id="i_p_from_to" style="display: none;">
                                    <?php echo $this->form->getLabel('picasa_from'); ?>
                                    <?php echo $this->form->getInput('picasa_from'); ?>
                                    <?php echo $this->form->getLabel('picasa_to'); ?>
                                    <?php echo $this->form->getInput('picasa_to'); ?>
                                </li>
                                <li>
                                    <?php echo $this->form->getInput('image_picasa_bt'); ?>
                                </li>
                            </ul>
                        </div>
                        <div class="content-right">
                            <div class="ajax-loading"></div>
                            <div id ="display_picasa_image">
                                <ul class="display-message"></ul>
                                <ul class="img-list">
                                    <?php if ($sessions->get('images_picasa') != null): ?>
                                        <?php
                                        foreach ($sessions->get('images_picasa') as $image_pi) {
                                            echo '<li class="image"><img class="img-thumb" src="' . JURI::root() . $params->get('file_save', 'images/bt_media') . '/images/thumbnail/' . $image_pi->image_path . '"  />';
                                            echo '<img class="img-delete" onclick="removeImage(this)" src="' . JURI::base() . 'components/com_bt_media/assets/images/delete.png" />';
                                            echo '<input type="hidden" name="image_filename" value="' . $image_pi->image_path . '" />';
                                            echo '<input type="hidden" name="old_name" value="' . htmlspecialchars($image_pi->name) . '" />';
                                            echo '<input type="hidden" name="session_name" value="images_picasa" />';
                                            echo '<div class="edit-title"><input class="input-title" type="text" name="image_title" value="' . htmlspecialchars($image_pi->name) . '" onblur="changeName(this)"></div>';
                                            echo '</li>';
                                        }
                                        ?>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                    </div>
                    <div id="youtube">
                        <div class="content-left">
                            <ul id="youtube-fields" class="adminformlist">
                                <li style="display:none;" class="message-display">
                                </li>
                                <li>
                                    <?php echo $this->form->getLabel('youtube_get_by'); ?>
                                    <?php echo $this->form->getInput('youtube_get_by'); ?>
                                    <span class="ajax-display" style="width: 100px; margin-top: 5px!important;"></span>
                                </li>
                                <li id="yt-url">
                                    <?php echo $this->form->getLabel('youtube_url'); ?>
                                    <?php echo $this->form->getInput('youtube_url'); ?>
                                </li>
                                <li id="yt-username">
                                    <?php echo $this->form->getLabel('youtube_username'); ?>
                                    <?php echo $this->form->getInput('youtube_username'); ?>
                                </li>
                                <li id="yt-playlists">
                                    <?php echo $this->form->getLabel('youtube_playlists'); ?>
                                    <?php echo $this->form->getInput('youtube_playlists'); ?>
                                </li>
                                <li id="i_yt_show_count" style="display: none;">
                                </li> 
                                <li id="i_yt_from_to" style="display: none;">
                                    <?php echo $this->form->getLabel('youtube_from'); ?>
                                    <?php echo $this->form->getInput('youtube_from'); ?>
                                    <?php echo $this->form->getLabel('youtube_to'); ?>
                                    <?php echo $this->form->getInput('youtube_to'); ?>
                                </li>
                                <li id="yt-get-bt">
                                    <?php echo $this->form->getInput('youtube_data'); ?>
                                    <?php echo $this->form->getInput('video_youtube_bt'); ?>
                                </li>
                            </ul>
                        </div>
                        <div class="content-right">
                            <div class="ajax-loading"></div>
                            <div id ="display_youtube_video">
                                <ul class="img-list">
                                    <?php if ($sessions->get('video_youtube') != null): ?>
                                        <?php
                                        foreach ($sessions->get('video_youtube') as $video_you) {
                                            echo '<li class="image"><img class="img-thumb" src="' . JURI::root() . $params->get('file_save', 'images/bt_media') . '/images/thumbnail/' . $video_you->image_path . '"    />';
                                            echo '<img class="img-delete" onclick="removeImage(this)" src="' . JURI::base() . 'components/com_bt_media/assets/images/delete.png" />';
                                            echo '<input type="hidden" name="image_filename" value="' . $video_you->image_path . '" />';
                                            echo '<input type="hidden" name="old_name" value="' . htmlspecialchars($video_you->name) . '" />';
                                            echo '<input type="hidden" name="session_name" value="video_youtube" />';
                                            echo '<div class="edit-title"><input class="input-title" type="text" name="image_title" value="' . htmlspecialchars($video_you->name) . '" onblur="changeName(this)"></div>';
                                            echo '</li>';
                                        }
                                        ?>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                    </div>
                    <div id="vimeo">
                        <div class="content-left">
                            <ul id="vimeo-fields" class="adminformlist">
                                <li style="display:none;" class="message-display">
                                </li>
                                <li>
                                    <?php echo $this->form->getLabel('vimeo_get_by'); ?>
                                    <?php echo $this->form->getInput('vimeo_get_by'); ?>
                                    <span class="ajax-display" style="width: 100px; margin-top: 5px!important;"></span>
                                </li>
                                <li id="vm-url">
                                    <?php echo $this->form->getLabel('vimeo_url'); ?>
                                    <?php echo $this->form->getInput('vimeo_url'); ?>
                                </li>
                                <li id="vm-username">
                                    <?php echo $this->form->getLabel('vimeo_username'); ?>
                                    <?php echo $this->form->getInput('vimeo_username'); ?>
                                </li>
                                <li id="vm-playlists">
                                    <?php echo $this->form->getLabel('vimeo_playlists'); ?>
                                    <?php echo $this->form->getInput('vimeo_playlists'); ?>
                                </li>
                                <li id="i_vm_show_count" style="display: none;">
                                </li> 
                                <li id="i_vm_from_to" style="display: none;">
                                    <?php echo $this->form->getLabel('vimeo_from'); ?>
                                    <?php echo $this->form->getInput('vimeo_from'); ?>
                                    <?php echo $this->form->getLabel('vimeo_to'); ?>
                                    <?php echo $this->form->getInput('vimeo_to'); ?>
                                </li>
                                <li id="vm-get-bt">
                                    <?php echo $this->form->getInput('vimeo_data'); ?>
                                    <?php echo $this->form->getInput('video_vimeo_bt'); ?>
                                </li>
                            </ul>
                        </div>
                        <div class="content-right">
                            <div class="ajax-loading"></div>
                            <div id ="display_vimeo_video">
                                <ul class="img-list">
                                    <?php if ($sessions->get('video_vimeo') != null): ?>
                                        <?php
                                        foreach ($sessions->get('video_vimeo') as $video_vi) {
                                            echo '<li class="image"><img class="img-thumb" src="' . JURI::root() . $params->get('file_save', 'images/bt_media') . '/images/thumbnail/' . $video_vi->image_path . '"   />';
                                            echo '<img class="img-delete" onclick="removeImage(this)" src="' . JURI::base() . 'components/com_bt_media/assets/images/delete.png" />';
                                            echo '<input type="hidden" name="image_filename" value="' . $video_vi->image_path . '" />';
                                            echo '<input type="hidden" name="old_name" value="' . htmlspecialchars($video_vi->name) . '" />';
                                            echo '<input type="hidden" name="session_name" value="video_vimeo" />';
                                            echo '<div class="edit-title"><input class="input-title" type="text" name="image_title" value="' . htmlspecialchars($video_vi->name) . '" onblur="changeName(this)"></div>';
                                            echo '</li>';
                                        }
                                        ?>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                    </div>
                <?php endif; ?>
            </div>


        <?php else: ?>
            <?php echo JText::_("COM_BT_MEDIA_ACCESS_DENY"); ?>
        <?php endif; ?>



        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>
        <div class="clr"></div>

    </fieldset>
</form>
<script type="text/javascript">
    $BM(function() {
        $BM("#tabs").tabs();
    });
    function removeImage(el)
    {
        var file = $BM(el).parent().find('input[name="image_filename"]').val();
        var session_name = $BM(el).parent().find('input[name="session_name"]').val();
        $BM.ajax({
            url: btMediaCfg.siteURL + 'index.php',
            data: "option=com_bt_media&task=detail.deleteImage&file_name=" + file + "&session_name=" + session_name,
            type: 'post',
            beforeSend: function() {
                $BM(".head-content-tab .ajax-display").html("<?php echo JText::_('Delete...'); ?>").fadeIn();
            },
            success: function(response) {
                var rs = $BM.parseJSON(response);
                if (rs.success) {
                    $BM(".head-content-tab .ajax-display").html("<?php echo JText::_('File delete'); ?>").fadeOut(1000);
                    $BM(el).parent().fadeOut(function() {
                        $BM(this).remove();
                    });
                }
            }
        });
    }

    function changeName(val) {
        var nName = $BM(val).val();
        var oName = $BM(val).parent().parent().find('input[name="old_name"]').val();
        var file = $BM(val).parent().parent().find('input[name="image_filename"]').val();
        var session_name = $BM(val).parent().parent().find('input[name="session_name"]').val();

        if (nName !== oName) {
            $BM.ajax({
                url: btMediaCfg.siteURL + 'index.php',
                data: "option=com_bt_media&task=detail.changeName&session_name=" + session_name + "&file=" + file + "&name=" + nName,
                type: "post",
                beforeSend: function() {
                    $BM(".head-content-tab .ajax-display").html("<?php echo JText::_('Name saving...'); ?>").fadeIn();
                },
                success: function(response) {
                    var rs = $BM.parseJSON(response);
                    if (rs.success) {
                        $BM(".head-content-tab .ajax-display").html("<?php echo JText::_('Save success'); ?>").fadeOut(1000);
                        $BM(val).parent().find('input[name="old_name"]').val(nName);
                    }
                }
            });
        }
    }
</script>

