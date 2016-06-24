<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_popular
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$modparams = new JRegistry($module->params);
?>

<?php if($module->showtitle || $modparams->get('module-intro') || $count > 3): ?>
<div class="section-header text-left">
  <?php if($module->showtitle): ?>
  <h3 class="section-title ">  <span><?php echo $module->title ?></span> </h3>
  <?php endif; ?>

  <?php if($modparams->get('module-intro')): ?>
  <div class="module-intro">
    <?php echo $modparams->get('module-intro') ?>
  </div>
  <?php endif; ?>
</div>
<?php endif; ?>

<div id="mostread-<?php echo $module->id; ?>" class="mostevent mostread<?php echo $moduleclass_sfx; ?>">
<?php foreach ($list as $item) : ?>
<?php $extrafields = new JRegistry($item->attribs);
		$images = json_decode($item->images); ?>
	<div class="mostread-item">
		<div class="event-img">
			<img class="img-responsive" src="<?php echo $images->image_intro ; ?>" alt="" />
		</div>
		
		<div class="event-info">
			<div class="event-title">
				<a href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a>
			</div>

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
<?php endforeach; ?>
</div>

<script>
(function ($) {
  $(document).ready(function(){ 
    $("#mostread-<?php echo $module->id; ?>.mostread").owlCarousel({
      navigation : true,
      pagination: false,
      items: 3,
      loop: false,
      scrollPerPage : true,
      autoHeight: false,
      navigationText : ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>"],
      itemsDesktop : [1199, 3],
      itemsDesktopSmall : [979, 2],
      itemsTablet : [768, 2],
      itemsTabletSmall : [600, 2],
      itemsMobile : [479, 1]
    });
  });
})(jQuery);
</script>
