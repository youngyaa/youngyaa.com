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
        if (task == 'tag.cancel' || document.formvalidator.isValid(document.id('category-form'))) {
            Joomla.submitform(task, document.getElementById('category-form'));
        }
        else {
            alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
        }
    }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_bt_media&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="category-form" class="form-validate" style="display: block;">
    <fieldset class="adminform">
        <div id="tabs">
            <ul>
                <li class="active"><a href="#details"><?php echo JText::_('COM_BT_MEDIA_TAG_LEGEND_INFO'); ?></a></li>
                <li><a href="#metadata"><?php echo JText::_('COM_BT_MEDIA_TAB_METADATA'); ?></a></li>
            </ul>
            <div id="details" class="tab-pane active">
                <ul class="adminformlist">

                    <li><?php echo $this->form->getLabel('id'); ?>
                        <?php echo $this->form->getInput('id'); ?></li>
                    <li><?php echo $this->form->getLabel('name'); ?>
                        <?php echo $this->form->getInput('name'); ?></li>
                    <li><?php echo $this->form->getLabel('alias'); ?>
                        <?php echo $this->form->getInput('alias'); ?></li>
                    <li><?php echo $this->form->getLabel('created_date'); ?>
                        <?php echo $this->form->getInput('created_date'); ?></li>
                    <li><?php echo $this->form->getLabel('language'); ?>
                        <?php echo $this->form->getInput('language'); ?></li>
                    <li><?php echo $this->form->getLabel('access'); ?>
                        <?php echo $this->form->getInput('access'); ?></li>
                    <li><?php echo $this->form->getLabel('state'); ?>
                        <?php echo $this->form->getInput('state'); ?></li>
                    <li><?php echo $this->form->getLabel('created_by'); ?>
                        <?php echo $this->form->getInput('created_by'); ?></li>
                    <li><?php echo $this->form->getLabel('description'); ?>
                        <div class="clr"></div>
                        <?php echo $this->form->getInput('description'); ?></li>
                    <li><?php echo $this->form->getInput('asset'); ?></li>
                </ul>
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
    $BM(document).ready(function(){
        $BM(function() {
            $BM("#tabs").tabs();
        });
    });

</script>


