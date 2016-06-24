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
//jimport( 'joomla.environment.uri' );
if (!Bt_mediaLegacyHelper::isLegacy()) {
    JHtml::_('formbehavior.chosen', 'select');
}

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_bt_media', JPATH_ADMINISTRATOR);
$user = JFactory::getUser();

echo '<script type="text/javascript">' . Bt_mediaHelper::getScripts($this->params, 'media') . '</script>';
?>
<script type="text/javascript">
    var filterBar = false;
<?php if ($this->params->get('show_filter_bar', 1) == 1): ?>
        filterBar = true;
        var keySearch = "<?php echo $this->jinput->getString('filter_search') ?>";
        var filter_type = "<?php echo $this->jinput->getString('filter_type') ?>";
        var filter_ordering = "<?php echo $this->jinput->getString('filter_ordering') ?>";
<?php endif; ?>
    var scrollLoad = false;
    var scrollLimit = <?php echo $this->params->get('show_list_limit_item', 20); ?>;
    var scrollStart = scrollLimit;
    var scrollPages = <?php echo $this->media->pagination->get('pages.total'); ?>;
    $BM(window).load(function() {
        $BM(window).scroll(function() {
            if ($BM(window).scrollTop() == ($BM(document).height() - $BM(window).height())) {
                loadMoreItem();
            }
        });

        $BM('.load-more-item').click(function() {
            loadMoreItem();
        });
    });
</script>
<div id="bt-media-wrapper">
    <?php if ($this->params->get('show_page_heading')) : ?>
        <h1>
            <?php echo $this->escape($this->params->get('page_heading')); ?>
        </h1>
    <?php endif; ?>

    <?php if ($this->item): ?>
        <!-- Start Current category information-->
        <?php if ($this->params->get('cat_cat_info', 1) == 1): ?>
            <div class="category-information">			
                <div class="cat-wrap">
                    <div class="cat-image">
                        <?php if ($this->item->category_image): ?>
                            <img name="thumnai" alt="<?php echo $this->escape($this->item->name); ?>" title="<?php echo $this->item->name; ?>" src="<?php echo JURI::base() . $this->item->category_image; ?>" />
                        <?php endif; ?>
                    </div>
                    <?php if ($this->params->get('cat_show_name', 1) == 1 || $this->params->get('cat_show_created_by', 1) == 1 || $this->params->get('cat_show_hits', 1) == 1): ?>
                        <div class="cat-info">
                            <?php if ($this->params->get('cat_show_name', 1) == 1): ?>
                                <h2 class="cat-title">
                                    <?php echo $this->item->name; ?>
                                </h2>
                            <?php endif; ?>
                            <?php if ($this->params->get('cat_show_created_by', 1) == 1 || $this->params->get('cat_show_hits', 1) == 1): ?>
                                <div class="more-info">
                                    <?php if ($this->params->get('cat_show_created_by', 1) == 1): ?>
                                        <?php echo JText::_('COM_BT_MEDIA_CREATED_BY') . ': ' . JFactory::getUser($this->item->created_by)->name; ?>
                                    <?php endif; ?>
                                    <?php if ($this->params->get('cat_show_created_by', 1) == 1 && $this->params->get('cat_show_hits', 1) == 1): ?>
                                        <?php echo ' | '; ?>
                                    <?php endif; ?>
                                    <?php if ($this->params->get('cat_show_hits', 1) == 1): ?>
                                        <?php echo JText::_('COM_BT_MEDIA_HITS') . ': ' . $this->item->hits; ?>
                                    <?php endif; ?>
                                </div>
                                <div style="clear:both;"></div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if ($this->params->get('cat_show_description', 1) == 1): ?>
                    <div class="cat-des">
                        <?php echo $this->item->description; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <!-- End Current category information-->
        <?php
        if ($this->params->get('show_filter_bar', 1) == 1) {
            echo '<form action="' . JRoute::_('index.php?option=com_bt_media&view=category&catid=' . $this->item->id . ':' . $this->item->alias) . '" method="post" name="adminForm" id="adminForm">';
            echo Bt_mediaHelper::getFilterBar($this->params);
            echo '</form>';
        }
        ?>
        <div style="clear:both"></div>
        <?php if ($this->items || $this->item->parent_id || $this->media->items) : ?>

            <div class="item_fields">

                <div class="image-information">
                    <div id="bt-media-container"  class="items_list">
                        <?php if ($this->params->get('cat_show_parent', 1) == 1 && $this->item->parent_id): ?>
                            <div class="item cat-item">
                                <?php
                                $cat = Bt_mediaHelper::getCategorybyId($this->item->parent_id);
                                $title = $this->escape($cat->name);
                                if (strlen($cat->name) >= 20)
                                    $title = substr($title, 0, 20) . '...';
                                ?>
                                <a title="Parent category" href="<?php echo JRoute::_('index.php?option=com_bt_media&view=category&catid=' . (int) $this->item->parent_id . ':' . $cat->alias); ?>" >
                                    <div class="item-image">
                                        <div class="item-mark">
                                            <div class="go-parent-cat"> <?php echo JText::_('COM_BT_MEDIA_GO_TO_PARENT'); ?></div>
                                            <div class="cat-title"><?php echo $title; ?></div>
                                        </div>
                                        <div class="album-image parent-cat">
                                            <span><?php echo JText::_('COM_BT_MEDIA_GO_TO_PARENT'); ?></span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endif; ?>
                        <?php if ($this->params->get('cat_show_child', 1) == 1 && $this->items): ?>
                            <?php foreach ($this->items as $item) : ?>
                                <div class="item cat-item">
                                    <a title="<?php echo $item->name; ?>" href="<?php echo JRoute::_('index.php?option=com_bt_media&view=category&catid=' . $item->id . ':' . $item->alias); ?>" >
                                        <div class="item-image">
                                            <div class="item-mark">
                                                <?php
                                                $title = $this->escape($item->name);
                                                if (strlen($item->name) >= 20)
                                                    $title = substr($title, 0, 20) . '...';
                                                ?>
                                                <div class="go-sub-cat"> <?php echo JText::_('COM_BT_MEDIA_GO_TO_SUBCAT'); ?></div>
                                                <div class="cat-title"><?php echo $title; ?></div>
                                            </div>
                                            <?php if ($item->category_image): ?>
                                                <img src="<?php echo $item->category_image; ?>"/>
                                            <?php else: ?>
                                                <div class="album-image sub-cat">
                                                    <span><?php echo JText::_('COM_BT_MEDIA_GO_TO_SUBCAT'); ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </a>
                                </div>

                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php if ($this->media->items): ?>
                            <?php
                            foreach ($this->media->items as $media) :
                                ?>
                                <?php
                                $registry = new JRegistry();
                                $registry->loadString($media->params);
                                $this->media->params->merge($registry);
                                $thumbImage = Bt_mediaHelper::getPathImage('thumb', $media->image_path, $media->cate_id);

                                // bt social share plugin intergration
                                $sharePlugin = Bt_mediaLegacyHelper::getSharePlugin($media, $this->params);

                                if ($this->params->get('allow_comment', 1) == '1' || $this->params->get('allow_social_share', 1) == '1') {
                                    $script = $sharePlugin['script'];
                                }
                                //end intergration
                                $canEdit = $user->authorise('core.edit', 'com_bt_media.media.' . $media->id);
                                $canEditOwn = ($user->authorise('core.edit.own', 'com_bt_media.media.' . $media->id) and $user->name == $media->created_by);
                                $canEditState = $user->authorise('core.edit.state', 'com_bt_media.media.' . $media->id);
                                $canDelete = $user->authorise('core.delete', 'com_bt_media.media.' . $media->id);
                                $canDeleteOwn = ($user->authorise('media.delete.own', 'com_bt_media.media.' . $media->id) and $user->name == $media->created_by);
                                ?>
                                <div class="item media-item">
                                    <?php if ($canEdit || $canEditOwn || $canEditState || $canDelete || $canDeleteOwn): ?>
                                        <div class="acl-allow">
                                            <?php if ($canEditState): ?>
                                                <a class="edit-state-btn" href="javascript:document.getElementById('bt-media-state-<?php echo $media->id; ?>').submit()">
                                                    <?php if ($media->state == 1): ?>
                                                        <img src="<?php echo COM_BT_MEDIA_THEME_URL ?>images/unpublish.png" title="<?php echo JText::_("COM_BT_MEDIA_UNPUBLISH_ITEM"); ?>" alt="<?php echo JText::_("COM_BT_MEDIA_UNPUBLISH_ITEM"); ?>"/>
                                                    <?php else: ?>
                                                        <img src="<?php echo COM_BT_MEDIA_THEME_URL ?>images/publish.png" title="<?php echo JText::_("COM_BT_MEDIA_PUBLISH_ITEM"); ?>" alt="<?php echo JText::_("COM_BT_MEDIA_PUBLISH_ITEM"); ?>"/>
                                                    <?php endif; ?>
                                                </a>
                                                <form id="bt-media-state-<?php echo $media->id ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_bt_media&task=detail.save'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
                                                    <input type="hidden" name="jform[id]" value="<?php echo $media->id; ?>" />
                                                    <input type="hidden" name="jform[cate_id]" value="<?php echo $media->cate_id; ?>" />
                                                    <input type="hidden" name="jform[state]" value="<?php echo (int) !((int) $media->state); ?>" />
                                                    <input type="hidden" name="jform[alias]" value="<?php echo $media->alias; ?>" />
                                                    <input type="hidden" name="jform[media_type]" value="<?php echo $media->media_type; ?>" />
                                                    <input type="hidden" name="jform[source_of_media]" value="<?php echo $media->source_of_media; ?>" />
                                                    <input type="hidden" name="layout" value="edit" />
                                                    <input type="hidden" name="option" value="com_bt_media" />
                                                    <input type="hidden" name="task" value="detail.save" />
                                                    <?php echo JHtml::_('form.token'); ?>
                                                </form>
                                            <?php endif; ?>
                                            <?php if ($canEdit || $canEditOwn): ?>
                                                <a class="edit-btn" href="<?php echo JRoute::_('index.php?option=com_bt_media&view=detail&layout=edit&id=' . (int) $media->id . ":" . $media->alias); ?>"><img src="<?php echo COM_BT_MEDIA_THEME_URL ?>images/edit.png" title="<?php echo JText::_("COM_BT_MEDIA_EDIT_ITEM"); ?>" alt="<?php echo JText::_("COM_BT_MEDIA_EDIT_ITEM"); ?>"/></a>
                                            <?php endif; ?>
                                            <?php if ($canDelete || $canDeleteOwn): ?>
                                                <a class="delete-btn" href="javascript:deleteItem(<?php echo $media->id; ?>)"><img src="<?php echo COM_BT_MEDIA_THEME_URL ?>images/delete.png" title="<?php echo JText::_("COM_BT_MEDIA_DELETE_ITEM"); ?>" alt="<?php echo JText::_("COM_BT_MEDIA_DELETE_ITEM"); ?>"/></a>
                                                <form id="bt-media-delete-<?php echo $media->id ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_bt_media&task=detail.remove'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
                                                    <input type="hidden" name="jform[id]" value="<?php echo $media->id; ?>" />
                                                    <input type="hidden" name="option" value="com_bt_media" />
                                                    <input type="hidden" name="task" value="detail.remove" />
                                                    <?php echo JHtml::_('form.token'); ?>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($media->media_type == 'image'): ?>
                                        <a rel="media-item" id="<?php echo (int) $media->id; ?>" class="media-view-box" onclick="return false;" href="<?php echo JRoute::_('index.php?option=com_bt_media&ajax=1&view=detail&id=' . $media->id . ':' . $media->alias.'&cat_rel='.$media->cate_id); ?>" >
                                            <div class="img-thumb item-image">
                                                <?php echo Bt_mediaHelper::getItemMark($media, $this->params); ?>
                                                <img class="image" alt="<?php echo $this->escape($media->name); ?>" title="<?php echo $this->escape($media->name); ?>" src="<?php echo $thumbImage; ?>" />
                                            </div>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($media->media_type == 'video') : ?>
                                        <a rel="media-item" id="<?php echo (int) $media->id; ?>" class="media-view-box" onclick="return false;" href="<?php echo JRoute::_('index.php?option=com_bt_media&ajax=1&view=detail&id=' . $media->id . ':' . $media->alias.'&cat_rel='.$media->cate_id); ?>" >
                                            <div class="img-thumb item-image">
                                                <?php echo Bt_mediaHelper::getItemMark($media, $this->params); ?>
                                                <img class="image" alt="<?php echo $this->escape($media->name); ?>" title="<?php echo $this->escape($media->name); ?>" src="<?php echo $thumbImage; ?>" />
                                                <img class="img-preview" title="<?php echo JText::_('Preview'); ?> " src="<?php echo COM_BT_MEDIA_THEME_URL ?>images/view_video.png" />
                                            </div>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div style="clear:both"></div>
                <?php if ($this->media->pagination->get('pages.total') > 1) : ?>
                    <div class="load-more-item"><span><?php echo JText::_('COM_BT_MEDIA_LOAD_MORE_ITEM'); ?></span></div>
                    <div class="ajax-load-more" style="display: none;"><span><?php echo JText::_('COM_BT_MEDIA_LOADING') ?></span></div>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <div class="no-item"><?php echo JText::_('COM_BT_MEDIA_NO_ITEM'); ?></div>
        <?php endif; ?>
    <?php else: ?>
        <div class="no-item"><?php echo JText::_('COM_BT_MEDIA_COULD_NOT_LOAD_ITEM'); ?></div>
    <?php endif; ?>
    <?php
    if (isset($script)) {
        echo $script;
    }
    ?>

    <div style="clear:both"></div>
</div>