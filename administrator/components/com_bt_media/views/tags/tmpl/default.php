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

$user = JFactory::getUser();
$userId = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
$canOrder = $user->authorise('core.edit.state', 'com_bt_media');
$saveOrder = $listOrder == 'a.ordering';
if (!Bt_mediaLegacyHelper::isLegacy()) {
	if ($saveOrder) {
		$saveOrderingUrl = 'index.php?option=com_bt_media&task=tags.saveOrderAjax&tmpl=component';
		JHtml::_('sortablelist.sortable', 'tags-list', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
	}
}

$sortFields = $this->getSortFields();
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_bt_media/assets/css/bt_media.css');
?>

<script type="text/javascript">
    Joomla.orderTable = function() {
        table = document.getElementById("sortTable");
        direction = document.getElementById("directionTable");
        order = table.options[table.selectedIndex].value;
        if (order != '<?php echo $listOrder; ?>') {
            dirn = 'asc';
        } else {
            dirn = direction.options[direction.selectedIndex].value;
        }
        Joomla.tableOrdering(order, dirn, '');
    }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_bt_media&view=tags'); ?>" method="post" name="adminForm" id="adminForm" style="display: block;">
    <?php if (!empty($this->sidebar)): ?>
        <div id="j-sidebar-container" class="span2">
            <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
        <?php else : ?>
            <div id="j-main-container">
            <?php endif; ?>
            <?php if (!$this->legacy): ?>

                <div id="filter-bar" class="btn-toolbar">
                    <div class="filter-search btn-group pull-left">
                        <label for="filter_search" class="element-invisible"><?php echo JText::_('JSEARCH_FILTER'); ?></label>
                        <input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('JSEARCH_FILTER'); ?>" />
                    </div>
                    <div class="btn-group pull-left">
                        <button class="btn hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
                        <button class="btn hasTooltip" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value = '';
                                this.form.submit();"><i class="icon-remove"></i></button>
                    </div>
                    <div class="btn-group pull-right hidden-phone">
                        <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
                        <?php echo $this->pagination->getLimitBox(); ?>
                    </div>
                    <div class="btn-group pull-right hidden-phone">
                        <label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></label>
                        <select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
                            <option value=""><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></option>
                            <option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING'); ?></option>
                            <option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING'); ?></option>
                        </select>
                    </div>
                    <div class="btn-group pull-right">
                        <label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY'); ?></label>
                        <select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
                            <option value=""><?php echo JText::_('JGLOBAL_SORT_BY'); ?></option>
                            <?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder); ?>
                        </select>
                    </div>
                </div>

            <?php else: ?>
                <fieldset id="filter-bar">
                    <div class="filter-search fltlft">
                        <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
                        <input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('Search'); ?>" />
                        <button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
                        <button type="button" onclick="document.id('filter_search').value = '';
                                this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
                    </div>


                    <div class='filter-select fltrt'>
                        <select name="filter_published" class="inputbox" onchange="this.form.submit()">
                            <option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED'); ?></option>
                            <?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true); ?>
                        </select>
                    </div>

                    <div class='filter-select fltrt'>
                        <select name="filter_language" class="inputbox" onchange="this.form.submit()">
                            <option value=""><?php echo JText::_('COM_BT_MEDIA_LANGUAGE_FILTER'); ?></option>
                            <?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language')); ?>
                        </select>
                    </div>

                    <div class='filter-select fltrt'>
                        <select name="filter_access" class="inputbox" onchange="this.form.submit()">
                            <option value=""><?php echo JText::_('COM_BT_MEDIA_ACCESS_FILTER'); ?></option>
                            <?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access')); ?>
                        </select>
                    </div>


                </fieldset>
            <?php endif; ?>
            <div class="clr"> </div>

            <table class="adminlist table table-striped" id="tags-list">
                <thead>
                    <tr>
                        <?php if (!Bt_mediaLegacyHelper::isLegacy()): ?>
                            <?php if (isset($this->items[0]->ordering)): ?>

                                <th width="1%" class="nowrap center hidden-phone">
                                    <?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
                                </th>
                            <?php endif; ?>
                        <?php endif; ?>
                        <th width="1%">
                            <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this);" />
                        </th>

                        <th class='left'>
                            <?php echo JHtml::_('grid.sort', 'COM_BT_MEDIA_CATEGORY_CATEGORYS_NAME', 'a.name', $listDirn, $listOrder); ?>
                        </th>
        <!--                <th class='left'>
                        <?php // echo JHtml::_('grid.sort', 'COM_BT_MEDIA_CATEGORY_CATEGORYS_CREATED_BY', 'a.created_by', $listDirn, $listOrder);  ?>
                        </th>-->

                        <th class='left'>
                            <?php echo JHtml::_('grid.sort', 'COM_BT_MEDIA_LANGUAGE', 'a.language', $listDirn, $listOrder); ?>
                        </th>
                        <th class='left'>
                            <?php echo JHtml::_('grid.sort', 'COM_BT_MEDIA_ACCESS', 'a.access', $listDirn, $listOrder); ?>
                        </th>
                        <th class='left'>
                            <?php echo JHtml::_('grid.sort', 'COM_BT_MEDIA_HITS', 'a.hits', $listDirn, $listOrder); ?>
                        </th>

                        <?php if (isset($this->items[0]->state)) { ?>
                            <th width="5%">
                                <?php echo JHtml::_('grid.sort', 'JPUBLISHED', 'a.state', $listDirn, $listOrder); ?>
                            </th>
                        <?php } ?>
                        <?php if (Bt_mediaLegacyHelper::isLegacy()): ?>
                            <?php if (isset($this->items[0]->ordering)) { ?>
                                <th width="10%">
                                    <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
                                    <?php if ($canOrder && $saveOrder) : ?>
                                        <?php echo JHtml::_('grid.order', $this->items, 'filesave.png', 'tags.saveorder'); ?>
                                    <?php endif; ?>
                                </th>
                            <?php } ?>
                        <?php endif; ?>
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
                        $ordering = ($listOrder == 'a.ordering');
                        $canCreate = $user->authorise('core.create', 'com_bt_media');
                        $canEdit = $user->authorise('core.edit', 'com_bt_media');
                        $canCheckin = $user->authorise('core.manage', 'com_bt_media');
                        $canChange = $user->authorise('core.edit.state', 'com_bt_media');
                        ?>
                        <tr class="row<?php echo $i % 2; ?>">
                            <?php if (!Bt_mediaLegacyHelper::isLegacy()): ?>
                                <?php if (isset($this->items[0]->ordering)): ?>
                                    <td class="order nowrap center hidden-phone">
                                        <?php
                                        if ($canChange) :
                                            $disableClassName = '';
                                            $disabledLabel = '';
                                            if (!$saveOrder) :
                                                $disabledLabel = JText::_('JORDERINGDISABLED');
                                                $disableClassName = 'inactive tip-top';
                                            endif;
                                            ?>
                                            <span class="sortable-handler hasTooltip <?php echo $disableClassName ?>" title="<?php echo $disabledLabel ?>">
                                                <i class="icon-menu"></i>
                                            </span>
                                            <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
                                        <?php else : ?>
                                            <span class="sortable-handler inactive" >
                                                <i class="icon-menu"></i>
                                            </span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                            <td class="center">
                                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                            </td>

                            <td>
                                <?php if (isset($item->checked_out) && $item->checked_out) : ?>
                                    <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'tags.', $canCheckin); ?>
                                <?php endif; ?>
                                <?php if ($canEdit) : ?>
                                    <a href="<?php echo JRoute::_('index.php?option=com_bt_media&task=tag.edit&id=' . (int) $item->id); ?>">
                                        <?php echo $this->escape($item->name); ?></a>
                                <?php else : ?>
                                    <?php echo $this->escape($item->name); ?>
                                <?php endif; ?>
                                <p class="smallsub">
                                    <?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
                                </p>
                            </td>
        <!--                    <td>
                            <?php // echo $item->created_by;  ?>
                            </td>-->

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


                            <?php if (isset($this->items[0]->state)) { ?>
                                <td class="center">
                                    <?php echo JHtml::_('jgrid.published', $item->state, $i, 'tags.', $canChange, 'cb'); ?>
                                </td>
                            <?php } ?>
                            <?php if (Bt_mediaLegacyHelper::isLegacy()): ?>
                                <?php if (isset($this->items[0]->ordering)) { ?>
                                    <td class="order">
                                        <?php if ($canChange) : ?>
                                            <?php if ($saveOrder) : ?>
                                                <?php if ($listDirn == 'asc') : ?>
                                                    <span><?php echo $this->pagination->orderUpIcon($i, true, 'tags.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                                                    <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'tags.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                                                <?php elseif ($listDirn == 'desc') : ?>
                                                    <span><?php echo $this->pagination->orderUpIcon($i, true, 'tags.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                                                    <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'tags.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <?php $disabled = $saveOrder ? '' : 'disabled="disabled"'; ?>
                                            <input type="text" name="order[]" size="5" value="<?php echo $item->ordering; ?>" <?php echo $disabled ?> class="text-area-order" />
                                        <?php else : ?>
                                            <?php echo $item->ordering; ?>
                                        <?php endif; ?>
                                    </td>
                                <?php } ?>
                            <?php endif; ?>
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
                <input type="hidden" name="task" value="" />
                <input type="hidden" name="boxchecked" value="0" />
                <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
                <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
                <?php echo JHtml::_('form.token'); ?>
            </div>
        </div>
</form>
<style type="text/css">
     .pull-right .chzn-container{
        width: auto !important;
    }
</style>