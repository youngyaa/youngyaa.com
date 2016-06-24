<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_category
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<div class="category-grid">
	<div class="category-module<?php echo $moduleclass_sfx; ?>">
		<div class="row">
			<?php foreach ($list as $item) : ?>
			<?php $extrafields = new JRegistry($item->attribs); ?>
			<div class="col-sm-6 col-md-3">
				<div class="event">
					<?php $images = json_decode($item->images); ?>
					<div class="event-img">
						<img class="img-responsive" src="<?php echo $images->image_intro ; ?>" alt="" />
					</div>
					<div class="event-info">
						<?php if ($params->get('link_titles') == 1) : ?>
							<div class="event-title">
								<a class="<?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
									<?php echo $item->title; ?>
								</a>
							</div>
						<?php else : ?>
							<?php echo $item->title; ?>
						<?php endif; ?>
			
						<?php if ($extrafields->get('link_1')) : ?>
							<div class="event-time">
								<i class="fa fa-clock-o"></i> <?php echo $extrafields->get('link_1'); ?>
							</div>
						<?php endif; ?>
						
						<?php if ($extrafields->get('link_2')) : ?>
							<div class="event-date">
								<i class="fa fa-calendar"></i> <?php echo $extrafields->get('link_2'); ?>
							</div>
						<?php endif; ?>
						
						<?php if ($extrafields->get('link_3')) : ?>
							<div class="event-location">
								<i class="fa fa-globe"></i> <?php echo $extrafields->get('link_3'); ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>