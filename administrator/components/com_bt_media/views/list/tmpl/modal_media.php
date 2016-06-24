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

if (Bt_mediaLegacyHelper::isLegacy()) {
    JHtml::_('behavior.tooltip');
} else {
    JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
    JHtml::_('bootstrap.tooltip');
    JHtml::_('formbehavior.chosen', 'select');
}
JHtml::_('behavior.multiselect');
// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_bt_media/assets/css/bt_media.css');
if (Bt_mediaLegacyHelper::isLegacy()) {
    $document->addScript('components/com_bt_media/assets/js/jquery-1.8.2.min.js');
}
$app = JFactory::getApplication();
if ($app->isSite()) {
    JSession::checkToken('get') or die(JText::_('JINVALID_TOKEN'));
}
$function = $app->input->getCmd('function', 'jSelectMedia');
$params = JComponentHelper::getParams('com_bt_media');

$user = JFactory::getUser();
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
?>

<form action="<?php echo JRoute::_('index.php?option=com_bt_media&view=list&layout=modal_media&tmpl=component&function='.$function.'&'.JSession::getFormToken().'=1'); ?>" method="post" name="adminForm" id="adminForm">

    <div class="well" style="float: left;">
        <div class="row">
            <?php if (!$this->legacy): ?>
                <div class="span9 control-group">
                    <div class="filter-search btn-group pull-left">
                        <label for="filter_search" class="element-invisible"><?php echo JText::_('JSEARCH_FILTER'); ?></label>
                        <input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('JSEARCH_FILTER'); ?>" />
                    </div>
                    <div class="btn-group pull-left">
                        <button class="btn hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
                        <button class="btn hasTooltip" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value = '';
                                    this.form.submit();"><i class="icon-remove"></i></button>
                    </div>
                </div>
            <?php else: ?>
                <div class="modal-filter-search fltlft">
                    <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
                    <input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('Search'); ?>" />
                    <button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
                    <button type="button" onclick="document.id('filter_search').value = '';
                                    this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
                </div>
                <style>
                    .row {
                        display: table;
                    }
                </style>
            <?php endif; ?>
            <?php if ($app->isAdmin()) : ?>
                <input onclick="if (window.parent)
                                        window.parent.<?php echo $this->escape($function); ?>('0', '<?php echo $this->escape(addslashes(JText::_('COM_BT_MEDIA_SELECT_AN_ITEM'))); ?>', null, null);" class="btn" type="button" value="<?php echo JText::_('JNONE'); ?>" />
                   <?php endif; ?>
        </div>
        <hr class="hr-condensed" />
        <div class="filters">
            <div class='filter-select fltrt' style="width: 137px; margin-right: 5px;">
                <select name="filter_access" class="input-medium" onchange="this.form.submit();">
                    <option value=""><?php echo JText::_('JOPTION_SELECT_ACCESS'); ?></option>
                    <?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access')); ?>
                </select>
            </div>
            <div class='filter-select fltrt' style="width: 131px; margin-right: 5px;">
                <select name="filter_published" class="input-medium" onchange="this.form.submit();">
                    <option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED'); ?></option>
                    <?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.state'), true); ?>
                </select>
            </div>

            <div class='filter-select fltrt' style="width: 145px; margin-right: 5px;">
                <select name="filter_cate_id" class="inputbox" onchange="this.form.submit();">
                    <option value=""><?php echo JText::_('COM_BT_MEDIA_CATEGORY_SELECT'); ?></option>
                    <?php echo JHtml::_('select.options', Bt_mediaLegacyHelper::getCategoryOptions(), 'value', 'text', $this->state->get('filter.cate_id')); ?>
                </select>
            </div>
            <div class='filter-select fltrt' style="width: 149px; margin-right: 5px;">
                <select name="filter_language" class="input-medium" onchange="this.form.submit();">
                    <option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE'); ?></option>
                    <?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language')); ?>
                </select>
            </div>

            <div class='filter-select fltrt' style="width: 160px;">
                <?php
                //filter by media type
                $media_type = array();
                $media_type[0] = new stdClass();
                $media_type[0]->value = "image";
                $media_type[0]->text = "Image";
                $media_type[1] = new stdClass();
                $media_type[1]->value = "video";
                $media_type[1]->text = "Video";
                ?>
                <select name="filter_media_type" class="inputbox" onchange="this.form.submit();">
                    <option value=""><?php echo JText::_('COM_BT_MEDIA_ITEMS_MEDIASMANAGEMENT_MEDIA_TYPE_FILTER'); ?></option>
                    <?php echo JHtml::_('select.options', $media_type, "value", "text", $this->state->get('filter.media_type'), true); ?>
                </select>
            </div>
        </div>
    </div>
    <table class="adminlist table table-striped" id="medias-list" style="float: left;">
        <thead>
            <tr>
                <th class='left'>
                    <?php echo JHtml::_('grid.sort', 'COM_BT_MEDIA_NAME', 'a.name', $listDirn, $listOrder); ?>
                </th>
                <th class='left'>
                    <?php echo JHtml::_('grid.sort', 'COM_BT_MEDIA_MEDIA_TYPE', 'a.media_type', $listDirn, $listOrder); ?>
                </th>
                <th class='left'>
                    <?php echo JHtml::_('grid.sort', 'COM_BT_MEDIA_LANGUAGE', 'a.language', $listDirn, $listOrder); ?>
                </th>
                <th class='left'>
                    <?php echo JHtml::_('grid.sort', 'COM_BT_MEDIA_ACCESS', 'a.access', $listDirn, $listOrder); ?>
                </th>
                <th class='left'>
                    <?php echo JHtml::_('grid.sort', 'COM_BT_MEDIA_HITS', 'a.hits', $listDirn, $listOrder); ?>
                </th>


                <?php if (isset($this->items[0]->id)) { ?>
                    <th width="1%" class="nowrap">
                        <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                    </th>
                <?php } ?>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="13">
                    <?php echo $this->pagination->getListFooter(); ?>
                </td>
            </tr>
        </tfoot>
        <tbody>
            <?php
            foreach ($this->items as $i => $item) :
                $canEdit = $user->authorise('core.edit', 'com_bt_media');
                $canCheckin = $user->authorise('core.manage', 'com_bt_media');
                $canChange = $user->authorise('core.edit.state', 'com_bt_media');
                ?>
                <?php
                if ($item->language && JLanguageMultilang::isEnabled()) {
                    $tag = strlen($item->language);
                    if ($tag == 5) {
                        $lang = substr($item->language, 0, 2);
                    } elseif ($tag == 6) {
                        $lang = substr($item->language, 0, 3);
                    } else {
                        $lang = "";
                    }
                } elseif (!JLanguageMultilang::isEnabled()) {
                    $lang = "";
                }
                ?>
                <tr class="row<?php echo $i % 2; ?>">
                    <td>
                        <a class="pointer" onclick="if (window.parent)
                                        window.parent.<?php echo $this->escape($function); ?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->name)); ?>', '<?php echo $this->escape($item->cate_id); ?>', '<?php echo $this->escape($lang); ?>');">
                               <?php if ($canEdit) : ?>
                                <div  class="link-image-item">
                                    <?php if ($item->media_type == 'video'): ?>
                                        <img class="video-item-icon" style="width: 20px; margin-left: -10px; margin-top: -10px;" src="<?php echo JUri::root() . 'administrator/components/com_bt_media/assets/images/view_video.png' ?>"/>
                                    <?php endif; ?>
                                    <img class="img-thumb" style="width:100px;" src="<?php echo JUri::root() . $params->get('file_save', 'images/bt_media') . '/images/thumbnail/' . $item->image_path; ?>" title="<?php echo $this->escape($item->name); ?>" alt="<?php echo $this->escape($item->name); ?>" />
                                </div>
                            <?php else : ?>
                                <div  class="link-image-item">
                                    <?php if ($item->media_type == 'video'): ?>
                                        <img class="video-item-icon" style="width: 20px; margin-left: -10px; margin-top: -10px;" src="<?php echo JUri::root() . 'administrator/components/com_bt_media/assets/images/view_video.png' ?>"/>
                                    <?php endif; ?>
                                    <img class="img-thumb" style="width:100px;" src="<?php echo JUri::root() . $params->get('file_save', 'images/bt_media') . '/images/thumbnail/' . $item->image_path; ?>" title="<?php echo $this->escape($item->name); ?>" alt="<?php echo $this->escape($item->name); ?>" />
                                </div>
                            <?php endif; ?>
                        </a>
                    </td>
                    <td>
                        <?php echo $item->media_type; ?>
                    </td>
                    <td>
                        <?php if ($item->language == '*'): ?>
                            <?php echo JText::alt('JALL', 'language'); ?>
                        <?php else: ?>
                            <?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php echo $item->access ?>
                    </td>
                    <td>
                        <?php echo $item->hits ?>
                    </td>

                    <?php if (isset($this->items[0]->id)) { ?>
                        <td class="center">
                            <?php echo (int) $item->id; ?>
                        </td>
                    <?php } ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div>
        <input type="hidden" id="boxchecked" name="boxchecked" value="0" />
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
    </div>
</form>

<script type="text/javascript">
                            function insertGallery() {
                                var checkedids = $B('.checkboxid:checked');
                                if (!checkedids.length) {
                                    alert('<?php echo JText::_('PLUGIN_ALERT_PLEASE_SELECT_ITEMS'); ?>')
                                }
                                else {
                                    var html = '{mediagallery id=[';
                                    var commas = false;
                                    checkedids.each(function() {
                                        if (commas) {
                                            html += ',';
                                        } else {
                                            commas = true;
                                        }
                                        html += $B(this).val();
                                    });
                                    html += ']}';
                                    window.parent.jSelectGallery(html);
                                }
                                return false;

                            }
</script>
<style type="text/css">
    .pull-right .chzn-container{
        width: auto !important;
    }
</style>