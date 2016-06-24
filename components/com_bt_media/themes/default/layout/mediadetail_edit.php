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

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_bt_media', JPATH_ADMINISTRATOR);
?>

<!-- Styling for making front end forms look OK -->
<!-- This should probably be moved to the template CSS file -->
<style>
    .media-edit .detai-info{
        min-width: 100px;
    }
    .front-end-edit ul {
        padding: 0 !important;
    }
    .front-end-edit li {
        list-style: none;
        margin-bottom: 6px !important;
    }
    .front-end-edit label {
        margin-right: 10px;
        display: block;
        float: left;
        width: 200px !important;
    }
    .front-end-edit .radio label {
        float: none;
    }
    .front-end-edit .readonly {
        border: none !important;
        color: #666;
    }    
    .front-end-edit #editor-xtd-buttons {
        height: 50px;
        float: left;
    }
    .front-end-edit .toggle-editor {
        height: 50px;
        float: right;
    }

    #jform_rules-lbl{
        display:none;
    }

    #access-rules a:hover{
        background:#f5f5f5 url('../images/slider_minus.png') right  top no-repeat;
        color: #444;
    }

    fieldset.radio label{
        width: 50px !important;
    }
</style>
<div class="media-edit front-end-edit">
    <?php if (!empty($this->item->id)): ?>
        <h1>Edit <?php echo $this->item->name; ?></h1>
    <?php else: ?>
        <h1>Add</h1>
    <?php endif; ?>
    <form action="<?php echo JRoute::_('index.php?option=com_bt_media&layout=edit'); ?>" method="post" name="adminForm" id="detail-form" class="form-validate <?php echo (!Bt_mediaLegacyHelper::isLegacy() ? 'isJ30' : 'isJ25') ?>" enctype="multipart/form-data" style="display: block;">
        <div class="adminform">
            <div id="tabs">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#details"><?php echo JText::_('COM_BT_MEDIA_ITEMS_LEGEND_MEDIAINFORMATION'); ?></a></li>
                    <li><a href="#metadata"><?php echo JText::_('COM_BT_MEDIA_TAB_METADATA'); ?></a></li>
                </ul>

                <div id="details" class="tab-pane active" style="overflow: hidden;">
                    <!--<div class="detai-info">-->
                    <ul class="adminformlist">


                        <li><?php echo $this->form->getLabel('id'); ?>
                            <?php echo $this->form->getInput('id'); ?></li>
                        <?php if (JFactory::getUser()->authorise('media.upload.image', 'com_bt_media') || JFactory::getUser()->authorise('media.upload.video', 'com_bt_media') || JFactory::getUser()->authorise('media.get.image', 'com_bt_media') || JFactory::getUser()->authorise('media.get.video', 'com_bt_media')): ?>
                            <li><?php echo $this->form->getLabel('datafrom'); ?>
                                <?php echo $this->form->getInput('datafrom'); ?></li>
                            <?php if (JFactory::getUser()->authorise('media.get.image', 'com_bt_media') || JFactory::getUser()->authorise('media.get.video', 'com_bt_media')): ?>
                                <li style="display:none;">
                                    <?php echo $this->form->getLabel('dataurl'); ?>
                                    <?php echo $this->form->getInput('dataurl'); ?>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                        <li><?php echo $this->form->getLabel('name'); ?>
                            <?php echo $this->form->getInput('name'); ?></li>
                        <li><?php echo $this->form->getLabel('alias'); ?>
                            <?php echo $this->form->getInput('alias'); ?></li>
                        <li><?php echo $this->form->getLabel('cate_id'); ?>
                            <?php echo $this->form->getInput('cate_id'); ?></li>
                        <li><?php echo $this->form->getLabel('language'); ?>
                            <?php echo $this->form->getInput('language'); ?></li>
                        <li><?php echo $this->form->getLabel('access'); ?>
                            <?php echo $this->form->getInput('access'); ?></li>
                        <li><?php echo $this->form->getLabel('created_date'); ?>
                            <?php echo $this->form->getInput('created_date'); ?></li>
                        <li><?php echo $this->form->getLabel('state'); ?>
                            <?php echo $this->form->getInput('state'); ?></li>
                        <li><?php echo $this->form->getLabel('created_by'); ?>
                            <?php echo $this->form->getInput('created_by'); ?></li>
                        <li><?php echo $this->form->getLabel('featured'); ?>
                            <?php echo $this->form->getInput('featured'); ?></li>
                        <li>
                            <?php echo $this->form->getLabel('tags'); ?>
                            <?php echo $this->form->getInput('tags'); ?>
                            <?php echo $this->form->getInput('media_type'); ?>
                        </li>
                        <li style="display:none;">
                            <?php echo $this->form->getLabel('source_of_media'); ?>
                            <?php echo $this->form->getInput('source_of_media'); ?>
                            <?php echo $this->form->getLabel('video_path'); ?>
                            <?php echo $this->form->getInput('video_path'); ?>
                        </li>
                        <li>
                            <?php echo $this->form->getLabel('media_upload'); ?>

                            <div id="thumb-view">
                                <div class="img">
                                    <?php if ($this->item->image_path) { ?>
                                        <?php
                                        $params = JComponentHelper::getParams('com_bt_media');
                                        $view_image = $params->get('file_save', 'images/bt_media') . '/images/large/' . $this->item->image_path;
                                        ?>
                                        <img src="<?php echo JUri::root() . $view_image; ?>"/>
                                    <?php } ?>
                                </div>
                                <div class="change-image">
                                    <div class="change-buttom">
                                        <?php echo $this->form->getInput('media_upload'); ?>
                                        <?php echo $this->form->getInput('image_path'); ?>
                                    </div>
                                </div>

                            </div>

                        </li>
                        <li style="overflow: hidden;">
                            <?php echo $this->form->getLabel('description'); ?>
                            <div class="clr"></div>
                            <?php echo $this->form->getInput('description'); ?></li>
                    </ul>
                    <!--</div>-->
                </div>

                <div id="metadata" class="tab-pane">

                    <ul class="adminformlist">
                        <?php foreach ($this->form->getFieldset('metadata') as $field) : ?>
                            <li class="control-group"><?php
                                echo $field->label;
                                if ($field->type == "Editor")
                                    echo '<div class="clr"></div>';
                                echo $field->input;
                                ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>



        <div>
            <button type="submit" class="validate"><span><?php echo JText::_('JSUBMIT'); ?></span></button>
            <?php echo JText::_('or'); ?>
            <a href="<?php echo JRoute::_('index.php?option=com_bt_media&task=detail.cancel&return=' . urlencode(JFactory::getApplication()->input->getString('return'))); ?>" title="<?php echo JText::_('JCANCEL'); ?>"><?php echo JText::_('JCANCEL'); ?></a>

            <input type="hidden" name="option" value="com_bt_media" />
            <input type="hidden" name="task" value="detail.save" />
            <input type="hidden" name="layout" value="edit" />
            <?php echo JHtml::_('form.token'); ?>
        </div>
        <div class="clr"></div>
</div>

<style type="text/css">
    /* Temporary fix for drifting editor fields */
    .adminformlist li {
        clear: both;
    }
</style>
</form>
<script type="text/javascript">
    $BM(document).ready(function() {
        $BM(function() {
            $BM("#tabs").tabs();
        });
    });
</script>
