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
defined('_JEXEC') or die;

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_bt_media', JPATH_ADMINISTRATOR);

if ($this->item):
    $view = $this->jinput->getString('iview');
    $registry = new JRegistry();
    $registry->loadString($this->item->params);
    $this->params->merge($registry);
    $this->item->tags = Bt_mediaLegacyHelper::getAllTagsItem($this->item->id);
    $sharePlugin = Bt_mediaLegacyHelper::getSharePlugin($this->item, $this->params);
    if (($this->params->get('allow_social_share', 1) && $this->params->get('allow_social_share', 1) == 1) || (Bt_mediaHelper::allowComment() && $sharePlugin['comment'] != '')) {
        echo $sharePlugin['script'];
    }
    ?>
    <style type="text/css">
    <?php if ($this->item->media_type == 'video'): ?>
            .item-detail, .item-view-title {
                max-width: <?php echo $this->params->get('media_show_width', 700); ?>px;
            }
            .inner-image{
                max-width:<?php echo $this->params->get('media_show_width', 700); ?>px;
            }
            @media (min-width: <?php echo ($this->params->get('media_show_width', 700) + (2 * $this->params->get('op_padding', 15)) + 58); ?>px)  and (max-width: <?php echo ($this->params->get('media_show_width', 700) + (2 * $this->params->get('op_padding', 15)) + 308); ?>px){
                .fancybox-wrap{
                    width: <?php echo ($this->params->get('media_show_width', 700) + (2 * $this->params->get('op_padding', 15))); ?>px!important;
                }
                .fancybox-inner{
                    width: <?php echo $this->params->get('media_show_width', 700); ?>px!important;
                }
                .fb-comment-right{
                    width:100%;
                    margin: 0;
                }

            }
            @media (max-width: <?php echo ($this->params->get('media_show_width', 700) + (2 * $this->params->get('op_padding', 15)) + 58); ?>px) {
                .fb-comment-right{
                    width:100%;
                    margin: 0;
                }   
            }
            @media (min-width: <?php echo ($this->params->get('media_show_width', 700) + (2 * $this->params->get('op_padding', 15)) + 308); ?>px) {
                .item-container{
                    float: left;
                    width: <?php echo $this->params->get('media_show_width', 700); ?>px;
                }  
            }
    <?php endif; ?>
    <?php if ($this->item->media_type == 'image'): ?>
        <?php list($img_w, $img_h) = getimagesize(JPATH_SITE . '/' . $this->params->get('file_save', 'images/bt_media') . '/images/large/' . $this->item->image_path); ?>
            .item-detail, .item-view-title {
                max-width: <?php echo $img_w; ?>px;
            }
            .inner-image{
                max-width:<?php echo $img_w; ?>px;
            }
            @media (min-width: <?php echo ($img_w + (2 * $this->params->get('op_padding', 15)) + 58); ?>px)  and (max-width: <?php echo ($img_w + (2 * $this->params->get('op_padding', 15)) + 308); ?>px){
                .fancybox-wrap{
                    width: <?php echo ($img_w + (2 * $this->params->get('op_padding', 15))); ?>px!important;
                }
                .fancybox-inner{
                    width: <?php echo $img_w; ?>px!important;
                }
                .fb-comment-right{
                    width:100%;
                    margin: 0;
                }

            }
            @media (max-width: <?php echo ($img_w + (2 * $this->params->get('op_padding', 15)) + 58); ?>px) {
                .fb-comment-right{
                    width:100%;
                    margin: 0;
                }   
            }
            @media (min-width: <?php echo ($img_w + (2 * $this->params->get('op_padding', 15)) + 308); ?>px) {
                .item-container{
                    float: left;
                }  
            }
    <?php endif; ?>
    </style>
    <?php if ($this->item->media_type == 'image'): ?>
        <script type="text/javascript">
            if (<?php echo $img_w + 250; ?> < $BM(window).width()) {
                $BM('.image-wrap').css({'width': '<?php echo $img_w; ?>', 'height': '<?php echo $img_h; ?>'});
            }
        <?php if (Bt_mediaHelper::allowComment() && $sharePlugin['comment'] != ''): ?>
                $BM.fancybox.current.maxWidth = <?php echo $img_w + 250; ?>;
        <?php else: ?>
                $BM.fancybox.current.maxWidth = <?php echo $img_w; ?>;
        <?php endif; ?>
            $BM.fancybox.update();
        </script>
    <?php endif; ?>
    <?php if ($this->item->media_type == 'video'): ?>
        <script type="text/javascript">
            if (<?php echo $this->params->get('media_show_width', 700) + 250; ?> < $BM(window).width()) {
                $BM('.image-wrap').css({'width': '<?php echo $this->params->get('media_show_width', 700); ?>', 'height': '<?php echo $this->params->get('media_show_height', 450); ?>'});
            }
        <?php if (Bt_mediaHelper::allowComment() && $sharePlugin['comment'] != ''): ?>
                $BM.fancybox.current.maxWidth = <?php echo $this->params->get('media_show_width', 700) + 250; ?>;
        <?php else: ?>
                $BM.fancybox.current.maxWidth = <?php echo $this->params->get('media_show_width', 700); ?>;
        <?php endif; ?>

            $BM.fancybox.update();
        </script>
    <?php endif; ?>
    <div class="item-view" id="media-item-<?php echo $this->item->id; ?>">
        <div class="item-view-title">
            <?php if ($this->params->get('show_name', 1) && $this->params->get('show_name', 1) == 1): ?>
                <a href="<?php echo JRoute::_('index.php?option=com_bt_media&view=detail&id=' . $this->item->id . ':' . $this->item->alias.'&cat_rel='.$this->item->cate_id); ?>"><?php echo $this->escape($this->item->name); ?></a>
            <?php endif; ?>
        </div>
        <div class="item-container">
            <div id="inner-image-<?php echo $this->item->id; ?>" class="inner-image">
                <a title="Next" onclick="$BM.fancybox.next();" class="fancybox-nav fancybox-next" href="javascript:;"><span></span></a>
                <a title="Prev" onclick="$BM.fancybox.prev();" class="fancybox-nav fancybox-prev" href="javascript:;"><span></span></a>
                <?php if ($this->item->media_type == 'image'): ?>
                    <div class="image-wrap">
                        <div id="<?php echo 'inner-image-' . $this->item->id; ?>">
                            <?php $largeImage = Bt_mediaHelper::getPathImage('large', $this->item->image_path, $this->item->cate_id); ?>
                            <img alt="<?php echo $this->escape($this->item->name); ?>" width="<?php echo $img_w; ?>" src="<?php echo $largeImage; ?>"/>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($this->item->media_type == 'video'): ?>
                    <div class="video-wrap">
                        <?php if ($this->item->source_of_media == 'Youtube Server'): ?>
                            <iframe id="youtube-video" width="<?php echo $this->params->get('media_show_width', 700); ?>" height="<?php echo $this->params->get('media_show_height', 450); ?>" src="http://www.youtube.com/embed/<?php echo $this->item->video_path; ?>?autohide=1&fs=1&rel=0&hd=1&wmode=opaque&enablejsapi=1" frameborder="0" allowfullscreen></iframe>
                        <?php endif; ?>
                        <?php if ($this->item->source_of_media == 'Flickr Server'): ?>
                            <iframe id="flickr-video" width="<?php echo $this->params->get('media_show_width', 700); ?>" height="<?php echo $this->params->get('media_show_height', 450); ?>" src="https://www.flickr.com/apps/video/stewart.swf?photo_id=<?php echo $this->item->video_path; ?>" frameborder="0" allowfullscreen></iframe>
                        <?php endif; ?>
                        <?php if ($this->item->source_of_media == 'Vimeo Server'): ?>
                            <iframe src="http://player.vimeo.com/video/<?php echo $this->item->video_path; ?>?hd=1&show_title=1&show_byline=1&show_portrait=0&fullscreen=1" width="<?php echo $this->params->get('media_show_width', 700); ?>" height="<?php echo $this->params->get('media_show_height', 450); ?>" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
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
                                <video id="<?php echo $view; ?>-video-js-<?php echo $this->item->id; ?>" class="video-js vjs-default-skin" controls preload="auto" width="<?php echo $this->params->get('media_show_width', 700); ?>" height="<?php echo $this->params->get('media_show_height', 450); ?>" poster="<?php echo JURI::root() . $this->params->get('file_save', 'images/bt_media') . '/images/large/' . $this->item->image_path; ?>" data-setup='{<?php echo (($html5) ? '"techOrder": ["html5", "flash", "other supported tech"],' : '"techOrder": ["flash", "html5", "other supported tech"],'); ?> "example_option":true}'>
                                    <source src="<?php echo JURI::root() . $this->params->get('file_save', 'images/bt_media') . '/videos/' . $this->item->video_path; ?>" type='video/mp4' />
                                    <source src="<?php echo JURI::root() . $this->params->get('file_save', 'images/bt_media') . '/videos/' . $this->item->video_path; ?>" type='video/x-flv' />
                                </video>
                            </div>
                            <script type="text/javascript">
            $BM(document).ready(function() {
                videojs("<?php echo $view; ?>-video-js-<?php echo $this->item->id; ?>", {"height": "<?php echo $this->params->get('media_show_height', 450); ?>", "width": "<?php echo $this->params->get('media_show_width', 700); ?>", "techOrder": <?php echo ($html5) ? '["html5", "flash", "other supported tech"]' : '["flash", "html5", "other supported tech"]'; ?>, "example_option": true}).ready(function() {
                    var myPlayer = this;
                    var width = myPlayer.width();
                    var height = myPlayer.height();
                    var aspectRatio = width / height;
                    function resizeVideoJS() {
                        var width = $BM('.video-wrap').width();
                        myPlayer.width(width).height(width / aspectRatio);
                        if ($BM(window).width() >= <?php echo ($this->params->get('media_show_width', 700) + (2 * $this->params->get('op_padding', 15)) + 308); ?>) {
                            $BM('.item-container').css({'float': 'left'});
                        } else {
                            $BM('.item-container').css({'float': ''});
                        }

                    }
                    setTimeout(function() {
                        resizeVideoJS();
                    });
                    var timeOut = 0;
                    $BM(window).resize(function() {
                        clearTimeout(timeOut);
                        timeOut = setTimeout(function() {
                            resizeVideoJS();
                        }, 600);
                    });
                });
            });
                            </script>
                        <?php endif; ?>
                    </div>
                <?php endif; ?> 
            </div>

            <div class="item-detail">
                <?php if ($this->params->get('show_description', 1) && $this->params->get('show_description', 1) == 1 && $this->item->description): ?>
                    <div class="item-description"><?php echo $this->item->description; ?></div>
                <?php endif; ?>

                <?php if (($this->params->get('show_category', 1) && $this->params->get('show_category', 1) == 1) || ($this->params->get('show_created_by', 1) && $this->params->get('show_created_by', 1) == 1) || ($this->params->get('show_hits', 1) && $this->params->get('show_hits', 1) == 1)): ?>

                    <div class="item-more">
                        <div class="item-more-info">
                            <?php if ($this->params->get('show_category', 1) && $this->params->get('show_category', 1) == 1): ?>
                                <span><?php echo JText::_('COM_BT_MEDIA_CATEGORY'); ?>: <strong><a href="<?php echo JRoute::_('index.php?option=com_bt_media&view=category&catid=' . $this->item->cate_id . ':' . $this->item->category->alias); ?>"><?php echo $this->item->category->name; ?></a></strong></span>
                            <?php endif; ?>

                            <?php
                            if ($this->params->get('show_category', 1) && $this->params->get('show_category', 1) == 1 && $this->params->get('show_created_by', 1) && $this->params->get('show_created_by', 1) == 1) {
                                echo ' | ';
                            }
                            ?>

                            <?php if ($this->params->get('show_created_by', 1) && $this->params->get('show_created_by', 1) == 1): ?>
                                <span><?php echo JText::_('COM_BT_MEDIA_CREATED_BY'); ?>: <strong><?php echo JFactory::getUser($this->item->created_by)->name; ?></strong></span>
                            <?php endif; ?>

                            <?php
                            if ($this->params->get('show_created_by', 1) && $this->params->get('show_created_by', 1) == 1 && $this->params->get('show_hits', 1) && $this->params->get('show_hits', 1) == 1) {
                                echo ' | ';
                            }
                            ?>

                            <?php if ($this->params->get('show_hits', 1) && $this->params->get('show_hits', 1) == 1): ?>
                                <span class="hits"><?php echo JText::_('COM_BT_MEDIA_HITS'); ?>: <strong><?php echo $this->item->hits; ?></strong></span>
                            <?php endif; ?>
                        </div><!--end item-more-info-->
                        <!--Display media item vote system-->
                        <?php if ($this->params->get('allow_voting', 1) && $this->params->get('allow_voting', 1) == 1): ?>
                            <div class="btp-detail-voting">
                                <span style="float: left; margin-right: 10px;"><?php echo JText::_('COM_BT_MEDIA_VOTE_IT'); ?></span>
                                <?php echo Bt_mediaHelper::getRatingPanel($this->item->id, $this->item->vote_sum, $this->item->vote_count); ?>
                            </div>
                        <?php endif; ?>
                    </div><!--end item-more-->
                <?php endif; ?>

                <?php if ($this->params->get('show_tags', 1) && $this->params->get('show_tags', 1) == 1 && $this->item->tags): ?>
                    <div class="item-tag"><div class="tag-icon"></div><span><strong><?php echo JText::_('COM_BT_MEDIA_TAGS'); ?>:</strong></span>
                        <ul>
                            <?php foreach ($this->item->tags as $tag) : ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_bt_media&view=tag&id=' . $tag->id . ':' . $tag->alias); ?>"><li><?php echo trim($tag->name); ?></li></a>
                            <?php endforeach; ?>
                        </ul>
                    </div><!--end tags-->
                <?php endif; ?>

                <!--Display sharing system-->
                <?php if ($this->params->get('allow_social_share', 1) && $this->params->get('allow_social_share', 1) == 1): ?>
                    <?php
                    if ($sharePlugin['buttons'] == ''):
                        echo '<div style="float:left;">';
                        echo JText::_('COM_BT_MEDIA_NOTICE_SHARE_PLG_NOT_INSTALL');
                        echo '</div>';
                    else:
                        echo $sharePlugin['buttons'];
                    endif;
                    ?>
                <?php endif; ?>
            </div><!--end detail-->

        </div>
        <!--Comment box-->
        <?php if (Bt_mediaHelper::allowComment() && $sharePlugin['comment'] != ''): ?>
            <div class="fb-comment-right">
                <?php echo $sharePlugin['comment']; ?>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>
