<?php
/**
 * @package 	mod_bt_portfoliocategories - BT Portfolio Categories Module
 * @version		1.0
 * @created		Feb 2012
 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<div class="bt-media-list-tags <?php echo $moduleclass_sfx ?>">
    <ul class="tags">
        <?php if ($list != null): ?>
            <?php foreach ($list as $item): ?>
                <li class="tag" style="position: relative; float: left; padding: 0 5px; list-style: none;">
                    <a href="<?php echo JRoute::_('index.php?option=com_bt_media&view=tag&id=' . $item->id.':'.$item->alias); ?>"><?php echo ($params->get('show_item_count', 0) == 1) ? $item->name . ' (' . $item->item_count . ')' : $item->name; ?></a>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</div>

<style type="text/css">
    .bt-media-list-tags {
        display: block;
        overflow: hidden;
    }
    .tags {
        display: block;
        margin: 0;
    }
</style>