<?php
/**
 * @package 	mod_bt_media_categories - BT Media Categories Module
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
<div class="bt_media_categories<?php echo $moduleclass_sfx ?>">
	<?php echo modBtMediaCategoryHelper::showListCategories($params); ?>
</div>