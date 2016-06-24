<?php
/**
 * @package 	mod_bt_media_gallery - BT Media Gallery Module
 * @version		1.0
 * @created		Aug 2013
 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
JFactory::getLanguage()->load('com_bt_media');
$com_params = JComponentHelper::getParams('com_bt_media');
$com_params->merge($params);
?>

<script type="text/javascript">
<?php echo Bt_mediaHelper::getScripts($com_params, 'mod'); ?>
    $BM('.items_list li').hover(
            function() {
                $BM(this).animate({opacity: 0.5}, 400);
            },
            function() {
                $BM(this).animate({opacity: 1}, 400);
            });

</script>

<div class="bt_media_items_gallery <?php echo $moduleclass_sfx ?>">
    <div class="items_list">
        <?php
        $script = '';
        foreach ($list as $item):
            ?>
            <?php
            $documnet = JFactory::getDocument();
            $registry = new JRegistry();
            $registry->loadString($item->params);
            $com_params->merge($registry);

            if ($com_params->get('theme', 'default') == 'default') {
                $html_class = 'img-thumb';
                $style = '';
                $itemMark = '';
                $title = $item->name;
                if (str_word_count($item->name) == 1 && strlen($item->name) >= 20) {
                    $title = substr($title, 0, 20) . '...';
                } else {
                    $title = $item->name;
                }

                $title = '<div class="item-title"><a href="' . JRoute::_('index.php?option=com_bt_media&view=detail&id=' . (int) $item->id . ':' . $item->alias.'&cat_rel='.$item->cate_id) . '">' . $title . '</a></div>';
            }
            if ($com_params->get('theme', 'default') == 'modern') {
                $html_class = 'img-thumb item-image';
                $style ='margin-bottom:15px;';
                $itemMark = Bt_mediaHelper::getItemMark($item, $com_params);
                $title = '';
            }

            if ($com_params->get('allow_comment', 1) == '1' || $com_params->get('allow_social_share', 1) == '1') {
                $row = new stdClass();
                $row->title = $item->name;
                $row->link = JFactory::getURI()->toString(array('scheme', 'host', 'port')) . JRoute::_('index.php?option=com_bt_media&view=detail&id=' . (int) $item->id . ':' . $item->alias.'&cat_rel='.$item->cate_id);
                $row->image = JUri::root() . $item->image_path;
                $row->description = $item->description;
                JPluginHelper::importPlugin('content');
                $sharePlugin = array('buttons' => '', 'comment' => '', 'script' => '');
                if (class_exists('plgContentBt_socialshare')) {
                    $sharePlugin = plgContentBt_socialshare::socialButtons($row, false);
                }
                $script = $sharePlugin['script'];
            }
            ?>
            <div class="item" style="position: relative;<?php echo $style;?>;display:inline-block;max-width:100%;">
                <?php if ($item->media_type == 'image'): ?>
                    <a rel="media-item" id="<?php echo (int) $item->id; ?>" onclick="return false;" class="mod-view-box" href="<?php echo JRoute::_('index.php?option=com_bt_media&ajax=1&view=detail&id=' . $item->id . ':' . $item->alias.'&cat_rel='.$item->cate_id); ?>" >
                        <div class="<?php echo $html_class; ?>"> 
                            <?php echo $itemMark; ?>
                            <img class="image" name="thumnai" title="<?php echo $item->name; ?>" src="<?php echo JURI::root() . $com_params->get('file_save', 'images/bt_media') . '/images/thumbnail/' . $item->image_path; ?>" />
                        </div>
                    </a>
                <?php endif; ?>
                <?php if ($item->media_type == 'video') : ?>
                    <a rel="media-item" id="<?php echo (int) $item->id; ?>" onclick="return false;" class="mod-view-box" href="<?php echo JRoute::_('index.php?option=com_bt_media&ajax=1&view=detail&id=' . $item->id . ':' . $item->alias.'&cat_rel='.$item->cate_id); ?>">
                        <div class="<?php echo $html_class; ?>">
                            <?php echo $itemMark; ?>
                            <img title="<?php echo $item->name; ?>" src="<?php echo JURI::root() . $com_params->get('file_save', 'images/bt_media') . '/images/thumbnail/' . $item->image_path; ?>" />
                            <img class="img-preview" title="<?php echo JText::_('Preview'); ?> " src="<?php echo JURI::base(); ?>administrator/components/com_bt_media/assets/images/view_video.png" />
                        </div>
                    </a>
                <?php endif; ?>
                <?php
                echo $title;
                ?>
                <!--</li>-->
            </div>
            <?php
        endforeach;
        if ($script != '') {
            echo $script;
        }
        ?>
    </div>
</div>