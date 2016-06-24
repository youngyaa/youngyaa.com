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
<ul class="eventnews<?php echo $moduleclass_sfx; ?>">
<?php foreach ($list as $item): ?>
<?php $extrafields = new JRegistry($item->attribs); ?>
<?php $images = json_decode($item->images); ?>
	<li class="clearfix">
		<div class="mask" style="background-image:url("<?php echo $images->image_intro ; ?>")"></div>
		<?php echo JLayoutHelper::render('joomla.content.image.intro', array('item'=>$item)); ?>
		<div class="sub-title"><?php  echo JText::_('TPL_EVENT_NEW');  ?></div>
		<div class="item-ct">
			<a href="<?php echo $item->link; ?>" itemprop="url" class="item-title">
				<span itemprop="name">
					<?php echo $item->title; ?>
				</span>
			</a>
		</div>
		<div class="event-info">
			<div class="event-time">
				<i class="fa fa-clock-o"></i> <?php echo $extrafields->get('link_1'); ?>
			</div>
			
			<div class="event-date">
				<i class="fa fa-calendar"></i> <?php echo $extrafields->get('link_2'); ?>
			</div>
			
			<div class="event-location">
				<i class="fa fa-globe"></i> <?php echo $extrafields->get('link_3'); ?>
			</div>
		</div>
	</li>
<?php endforeach; ?>
</ul>