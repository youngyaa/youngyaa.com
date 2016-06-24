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
// Import CSS
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

<form action="<?php echo JRoute::_('index.php?option=com_bt_media&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="detail-form" class="form-validate <?php echo (!Bt_mediaLegacyHelper::isLegacy() ? 'isJ30' : 'isJ25') ?>" enctype="multipart/form-data" style="display: block;;">
    <fieldset class="adminform">
        <div id="tabs">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#details"><?php echo JText::_('COM_BT_MEDIA_ITEMS_LEGEND_MEDIAINFORMATION'); ?></a></li>
                <?php if (JFactory::getUser()->authorise('core.admin', 'com_bt_media')): ?>
                    <li><a href="#options"><?php echo JText::_('COM_BT_MEDIA_TAB_OPTION'); ?></a></li>
                <?php endif; ?>
                <li><a href="#metadata"><?php echo JText::_('COM_BT_MEDIA_TAB_METADATA'); ?></a></li>
                <?php if (JFactory::getUser()->authorise('core.admin', 'com_bt_media')): ?>
                    <li><a href="#permissions"><?php echo JText::_('COM_BT_MEDIA_TAB_PERMISSIONS'); ?></a></li>
                <?php endif; ?>
            </ul>

            <div id="details" class="tab-pane active" style="overflow: hidden;">
                <div id="thumb-view">
                    <div class="img">
                        <?php if ($this->item->image_path): ?>
                            <?php
                            $params = JComponentHelper::getParams('com_bt_media');
                            $view_image = $params->get('file_save', 'images/bt_media') . '/images/large/' . $this->item->image_path;
                            ?>
                            <img src="<?php echo JUri::root() . $view_image; ?>"/>
                        <?php endif; ?>
                    </div>
                    <div class="change-image">
                        <div class="change-buttom">
                            <div><?php echo $this->form->getInput('asset'); ?></div>
                            <?php echo $this->form->getInput('media_upload'); ?>
                            <?php echo $this->form->getInput('image_path'); ?>
                        </div>
                    </div>

                </div>
                <div class="detai-info">
                    <ul class="adminformlist">


                        <li><?php echo $this->form->getLabel('id'); ?>
                            <?php echo $this->form->getInput('id'); ?></li>
                        <?php if (JFactory::getUser()->authorise('media.get.image', 'com_bt_media') || JFactory::getUser()->authorise('media.get.video', 'com_bt_media')): ?>
                            <li><?php echo $this->form->getLabel('datafrom'); ?>
                                <?php echo $this->form->getInput('datafrom'); ?></li>
                            <li style="display:none;">
                                <?php echo $this->form->getLabel('dataurl'); ?>
                                <?php echo $this->form->getInput('dataurl'); ?>
                            </li>
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
                        <li style="overflow: hidden;">
                            <?php echo $this->form->getLabel('description'); ?>
                            <div class="clr"></div>
                            <?php echo $this->form->getInput('description'); ?></li>
                    </ul>
                </div>
            </div>
            <?php if (JFactory::getUser()->authorise('core.admin', 'com_bt_media')): ?>
                <div id="options" class="tab-pane">

                    <ul class="adminformlist">
                        <?php foreach ($this->form->getFieldset('options') as $field) : ?>
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
            <?php endif; ?>

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
            <?php if (JFactory::getUser()->authorise('core.admin', 'com_bt_media')): ?>
                <div id="permissions" class="tab-pane">
                    <ul class="adminformlist">
                        <li class="control-group"><?php echo $this->form->getInput('rules'); ?></li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </fieldset>



    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
    <div class="clr"></div>

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
