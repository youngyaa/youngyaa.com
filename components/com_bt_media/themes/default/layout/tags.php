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
    JHtml::_('formbehavior.chosen', 'select');
}
//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_bt_media', JPATH_ADMINISTRATOR);
echo '<script type="text/javascript">' . Bt_mediaHelper::getScripts($this->params, 'media') . '</script>';
$tags = JFactory::getApplication()->input->getString('tags');
$tagid = JFactory::getApplication()->input->getInt('id');
$user = JFactory::getUser();
?>
<div id="bt-media-wrapper">
    <?php if ($this->params->get('show_page_heading')) : ?>
        <h1>
            <?php echo $this->escape($this->params->get('page_heading')); ?>
        </h1>
    <?php endif; ?>
    <?php
    if ($this->params->get('show_filter_bar', 1) == 1) {
        echo '<form action="' . JRoute::_("index.php?option=com_bt_media&view=tag&" . (($tagid) ? "id=" . (int) $this->item->id . ":" . $this->item->alias : (($tags) ? "tags=" . $tags : ""))) . '" method="post" name="adminForm" id="adminForm">';
        echo Bt_mediaHelper::getFilterBar($this->params);
        echo '</form>';
    }
    ?>
    <?php if ($this->items): ?>

            <div class="item_fields">

                <div class="image-information">
                    <div id="bt-media-container" class="items_list">

                        <?php
                        foreach ($this->items as $media) :
                            ?>
                            <?php
                            $registry = new JRegistry();
                            $registry->loadString($media->params);
                            $this->params->merge($registry);
                            $thumbImage = Bt_mediaHelper::getPathImage('thumb', $media->image_path, $media->cate_id);

                            $sharePlugin = Bt_mediaLegacyHelper::getSharePlugin($media, $this->params);
                            
                            if ($this->params->get('allow_comment', 1) == '1' || $this->params->get('allow_social_share', 1) == '1') {
                                $script = $sharePlugin['script'];
                            }

                            $canEdit = $user->authorise('core.edit', 'com_bt_media.media.' . $media->id);
                            $canEditOwn = ($user->authorise('core.edit.own', 'com_bt_media.media.' . $media->id) and $user->name == $media->created_by);
                            $canEditState = $user->authorise('core.edit.state', 'com_bt_media.media.' . $media->id);
                            $canDelete = $user->authorise('core.delete', 'com_bt_media.media.' . $media->id);
                            $canDeleteOwn = ($user->authorise('media.delete.own', 'com_bt_media.media.' . $media->id) and $user->name == $media->created_by);
                            ?>
                            <div class="item" style="position: relative;">
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
                                        <div class="img-thumb">
                                            <img class="image" alt="<?php echo $this->escape($media->name); ?>" title="<?php echo $this->escape($media->name); ?>" src="<?php echo $thumbImage; ?>" />
                                        </div>
                                    </a>
                                <?php endif; ?>
                                <?php if ($media->media_type == 'video') : ?>
                                    <a rel="media-item" id="<?php echo (int) $media->id; ?>" class="media-view-box" onclick="return false;" href="<?php echo JRoute::_('index.php?option=com_bt_media&ajax=1&view=detail&id=' . $media->id . ':' . $media->alias.'&cat_rel='.$media->cate_id); ?>" >
                                        <div class="img-thumb">
                                            <img alt="<?php echo $this->escape($media->name); ?>" title="<?php echo $this->escape($media->name); ?>" src="<?php echo $thumbImage; ?>" />
                                            <img class="img-preview" title="<?php echo JText::_('Preview'); ?> " src="<?php echo COM_BT_MEDIA_THEME_URL; ?>images/view_video.png" />
                                        </div>
                                    </a>
                                <?php endif; ?>
                                <?php
                                $title = $media->name;
                                if (str_word_count($media->name) == 1 && strlen($media->name) >= 20) {
                                    $title = substr($title, 0, 20) . '...';
                                } else {
                                    $title = $media->name;
                                }
                                ?>
                                <div class="item-title"><a href="<?php echo JRoute::_('index.php?option=com_bt_media&view=detail&id=' . (int) $media->id . ':' . $media->alias.'&cat_rel='.$media->cate_id); ?>"><?php echo $title; ?></a></div>
                            </div>
                        <?php endforeach; ?>

                    </div>
                </div>
                <?php if ($this->pagination->getPagesCounter()): ?>
                    <div class="pagination">
                        <?php echo $this->pagination->getPagesLinks(); ?>
                        <span class="page-count"><?php echo $this->pagination->getPagesCounter(); ?></span>
                        <div style="clear:both"></div>
                    </div>
                <?php endif; ?>
            </div>
            <style type="text/css">

                .image-information .items_list div.item{
                    width: <?php echo $this->params->get('thumb_image_width', 200); ?>px;

                }


                .image-information .items_list div .item-image {
                    width: <?php echo $this->params->get('thumb_image_width', 200); ?>px;
                    height: <?php echo $this->params->get('thumb_image_height', 130); ?>px;
                }

                .album-image {
                    margin-top: <?php echo $this->params->get('thumb_image_height', 130) / 2 - 20; ?>px;
                    margin-left: <?php echo $this->params->get('thumb_image_width', 200) / 2 - 20; ?>px;
                }

            </style>

    <?php else: ?>
        <div class="no-item"><?php echo JText::_('COM_BT_MEDIA_NO_ITEM'); ?></div>
    <?php endif; ?>
    <?php
    if (isset($script)) {
        echo $script;
    }
    ?>
    <div style="clear:both"></div>
</div>