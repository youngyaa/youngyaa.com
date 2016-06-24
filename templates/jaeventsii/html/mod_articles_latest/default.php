<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_latest
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

?>
<ul class="latestnews<?php echo $moduleclass_sfx; ?>">
<?php foreach ($list as $item): ?>
	<li class="clearfix">
		<?php echo JLayoutHelper::render('joomla.content.image.intro', array('item'=>$item)); ?>
		<div class="item-ct">
			<a href="<?php echo $item->link; ?>" itemprop="url" class="item-title">
				<span itemprop="name">
					<?php echo $item->title; ?>
				</span>
			</a>
	    <?php echo JLayoutHelper::render('joomla.content.info_block.publish_date', array('item'=>$item)); ?>
		</div>
	</li>
<?php endforeach; ?>
</ul>
