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

//Load admin language file
$user = JFactory::getUser();
$lang = JFactory::getLanguage();
$lang->load('com_bt_media', JPATH_ADMINISTRATOR);
?>
<div id="bt-media-wrapper">
    <?php if ($this->params->get('show_page_heading')) : ?>
        <h1>
            <?php echo $this->escape($this->params->get('page_heading')); ?>
        </h1>
    <?php endif; ?>
    <?php if ($this->item) : ?>
        <?php
        $registry = new JRegistry();
        $registry->loadString($this->item->params);
        $this->params->merge($registry);
        $this->item->tags = Bt_mediaLegacyHelper::getAllTagsItem($this->item->id);
        $sharePlugin = Bt_mediaLegacyHelper::getSharePlugin($this->item, $this->params);

        $commentBox = $sharePlugin['comment'];
        $shareButton = $sharePlugin['buttons'];
        $item_detail = array();
        $item_detail[] = '<div class="item-detail">';

        //Display media item description
        if ($this->params->get('show_description', 1) && $this->params->get('show_description', 1) == 1 && $this->item->description) {
            $item_detail[] = '<div class="item-description">' . $this->item->description . '</div>';
        }

        //Display more information about media item: category, created by, hits
        if (($this->params->get('show_category', 1) && $this->params->get('show_category', 1) == 1) || ($this->params->get('show_created_by', 1) && $this->params->get('show_created_by', 1) == 1) || ($this->params->get('show_hits', 1) && $this->params->get('show_hits', 1) == 1)) {

            $item_detail[] = '<div class="item-more">';
            $item_detail[] = '<div class="item-more-info">';
            if ($this->params->get('show_category', 1) && $this->params->get('show_category', 1) == 1) {
                $item_detail[] = '<span>' . JText::_('COM_BT_MEDIA_CATEGORY') . ': <strong>' . Bt_mediaHelper::getCategorybyId($this->item->cate_id)->name . '</strong></span>';
            }

            if ($this->params->get('show_category', 1) && $this->params->get('show_category', 1) == 1 && $this->params->get('show_created_by', 1) && $this->params->get('show_created_by', 1) == 1) {
                $item_detail[] = ' | ';
            }

            if ($this->params->get('show_created_by', 1) && $this->params->get('show_created_by', 1) == 1) {
                $item_detail[] = '<span>' . JText::_('COM_BT_MEDIA_CREATED_BY') . ': <strong>' . JFactory::getUser($this->item->created_by)->name . '</strong></span>';
            }

            if ($this->params->get('show_created_by', 1) && $this->params->get('show_created_by', 1) == 1 && $this->params->get('show_hits', 1) && $this->params->get('show_hits', 1) == 1) {
                $item_detail[] = ' | ';
            }

            if ($this->params->get('show_hits', 1) && $this->params->get('show_hits', 1) == 1) {
                $item_detail[] = '<span class="hits">' . JText::_('COM_BT_MEDIA_HITS') . ': <strong>' . $this->item->hits . '</strong/></span>';
            }
            $item_detail[] = '</div><!--end item-more-info-->';

            //Display media item vote system
            if ($this->params->get('allow_voting', 1) && $this->params->get('allow_voting', 1) == 1) {
                $item_detail[] = '<div class="btp-detail-voting">';
                $item_detail[] = '<span style="float: left; margin-right: 10px;">' . JText::_('COM_BT_MEDIA_VOTE_IT') . '</span>';
                $item_detail[] = Bt_mediaHelper::getRatingPanel($this->item->id, $this->item->vote_sum, $this->item->vote_count);
                $item_detail[] = '</div>';
            }
            $item_detail[] = '</div><!--end item-more-->';
        }
        //Display media item tags
        if ($this->params->get('show_tags', 1) && $this->params->get('show_tags', 1) == 1 && $this->item->tags) {
            $item_detail[] = '<div class="item-tag"><div class="tag-icon"></div><span><strong>' . JText::_('COM_BT_MEDIA_TAGS') . ':</strong></span>';
            $item_detail[] = '<ul>';
            foreach ($this->item->tags as $tag) {
                $item_detail[] = '<a href="' . JRoute::_('index.php?option=com_bt_media&view=tag&id=' . $tag->id . ':' . $tag->alias) . '"><li>' . $tag->name . '</li></a>';
            }
            $item_detail[] = '</ul>';
            $item_detail[] = '</div><!--end tags-->';
        }

        //Display sharing system
        if ($this->params->get('allow_social_share', 1) && $this->params->get('allow_social_share', 1) == 1) {
            $item_detail[] = $shareButton;
        }
        $item_detail[] = '</div><!--end detail-->';
        ?>
        <div class="item-wrap">
            <div class="item-view" id="item-<?php echo $this->item->id; ?>">
                <h2>
                    <?php if ($this->params->get('show_name', 1) && $this->params->get('show_name', 1) == 1): ?>
                        <?php echo $this->escape($this->item->name); ?>
                    <?php endif; ?>
                </h2>
                <?php if ($this->item->media_type == 'image'): ?>
                    <div class="image-wrap">
                        <div id="<?php echo 'inner-image-' . $this->item->id; ?>">
                            <?php $largeImage = Bt_mediaHelper::getPathImage('large', $this->item->image_path, $this->item->cate_id); ?>
                            <img alt="<?php echo $this->escape($this->item->name); ?>" src="<?php echo $largeImage; ?>"/>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($this->item->media_type == 'video'): ?>
                    <div class="video-wrap">
                        <?php if ($this->item->source_of_media == 'Youtube Server'): ?>
                            <div id="<?php echo 'inner-image-' . $this->item->id; ?>">
                                <iframe width="<?php echo $this->params->get('media_show_width', 700); ?>" height="<?php echo $this->params->get('media_show_height', 450); ?>" src="http://www.youtube.com/embed/<?php echo $this->item->video_path . '?autohide=1&fs=1&rel=0&hd=1&wmode=opaque&enablejsapi=1'; //www.youtube.com/embed/3jD4141YEt0?autoplay=1&autohide=1&fs=1&rel=0&hd=1&wmode=opaque&enablejsapi=1                                                               ?>" frameborder="0" allowfullscreen></iframe>
                            </div>
                        <?php endif; ?>
                        <?php if ($this->item->source_of_media == 'Flickr Server'): ?>
                            <div id="<?php echo 'inner-image-' . $this->item->id; ?>">
                                <iframe id="flickr-video" width="<?php echo $this->params->get('media_show_width', 700); ?>" height="<?php echo $this->params->get('media_show_height', 450); ?>" src="https://www.flickr.com/apps/video/stewart.swf?photo_id=<?php echo $this->item->video_path; ?>" frameborder="0" allowfullscreen></iframe>
                            </div>
                        <?php endif; ?>
                        <?php if ($this->item->source_of_media == 'Vimeo Server'): ?>
                            <div id="<?php echo 'inner-image-' . $this->item->id; ?>">
                                <iframe src="http://player.vimeo.com/video/<?php echo $this->item->video_path; ?>?hd=1&show_title=1&show_byline=1&show_portrait=0&fullscreen=1" width="<?php echo $this->params->get('media_show_width', 700); ?>" height="<?php echo $this->params->get('media_show_height', 450); ?>" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
                            </div>
                        <?php endif; ?>
                        <?php if ($this->item->source_of_media == 'Upload from local' || $this->item->source_of_media == 'JFolder'): ?>
                            <?php
                            $html5 = false;
                            $ext_p = explode('.', $this->item->video_path);
                            $ext = strtolower($ext_p[1]);
                            if ($ext == 'mp4' || $ext == 'webm' || $ext == 'ogv') {
                                $html5 = true;
                            }
                            ?>
                            <div id="<?php echo 'inner-video-' . $this->item->id; ?>">
                                <video id="example_video_<?php echo $this->item->id; ?>" class="video-js vjs-default-skin"
                                       controls preload="auto" width="<?php echo $this->params->get('media_show_width', 700); ?>" height="<?php echo $this->params->get('media_show_height', 450); ?>"
                                       poster="<?php echo $this->params->get('file_save', 'images/bt_media') . '/images/thumbnail/' . $this->item->image_path; ?>"
                                       data-setup='{<?php echo ($html5) ? '"techOrder": ["html5", "flash", "other supported tech"],' : '"techOrder": ["flash", "html5", "other supported tech"],'; ?>"example_option":true}'>
                                    <source src="<?php echo JURI::root() . $this->params->get('file_save', 'images/bt_media') . '/videos/' . $this->item->video_path; ?>" type='video/mp4' />
                                </video>
                            </div>
                            <script type="text/javascript">
                                videojs("example_video_<?php echo $this->item->id; ?>", {"height": "<?php echo $this->params->get('media_show_height', 450); ?>", "width": "<?php echo $this->params->get('media_show_width', 700); ?>", "techOrder": <?php echo ($html5) ? '["html5", "flash", "other supported tech"]' : '["flash", "html5", "other supported tech"]'; ?>, "example_option": true}).ready(function() {
                                    var myPlayer = this;
                                    var width = myPlayer.width();
                                    var height = myPlayer.height();
                                    var aspectRatio = width / height;
                                    function resizeVideoJS() {
                                        var width = $BM('.video-wrap').width();
                                        myPlayer.width(width).height(width / aspectRatio);
                                    }
                                    resizeVideoJS();
                                    $BM(window).resize(function() {
                                        resizeVideoJS();
                                    });
                                });
                            </script>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php echo implode($item_detail); ?>
            </div>

            <?php if (Bt_mediaHelper::allowComment() && $commentBox != ''): ?>
                <div id="fb-comment-buttom">
                    <?php echo $commentBox; ?>
                </div>
            <?php endif; ?>
            <?php
            if ($this->params->get('allow_comment', 1) == '1' || $this->params->get('allow_social_share', 1) == '1') {
                echo $sharePlugin['script'];
            }
            ?>
        </div>
        <style type="text/css">
            .item-detail, .item-view-title {
                max-width: <?php echo $this->params->get('media_show_width', 700); ?>px;
            }
            .item-wrap{
                height: 100%;
            }
        </style>
    <?php else: ?>
        <div class="no-item"><?php echo JText::_('COM_BT_MEDIA_COULD_NOT_LOAD_ITEM'); ?></div>
    <?php endif; ?>
    <div style="clear:both"></div>
</div>
