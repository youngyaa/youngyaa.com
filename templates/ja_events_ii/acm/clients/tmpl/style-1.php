<?php 
/**
 * ------------------------------------------------------------------------
 * JA Events II template
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
 */
?>

<?php 
	jimport( 'joomla.application.module.helper' );
	$columns						= $helper->get('columns');
	$count 							= $helper->getRows('client-item.client-logo');
	$gray								= $helper->get('img-gray');
	$opacity						= $helper->get('img-opacity');
	$float = 0;
	$style              = $helper->get('acm-style');
	if ($opacity=="") {
		$opacity = 100;
	}
	
	if (100%$columns) {
		$float = 0.01;
	}
	
	$blockImg 				= $helper->get('block-bg');
	$blockImgBg  			= 'background-image: url("'.$blockImg.'"); background-repeat: no-repeat; background-size: cover; background-position: center center;';
	 
?>

<div id="uber-cliens-<?php echo $module->id; ?>" class="uber-cliens style-1 <?php if($gray): ?> img-grayscale <?php endif; ?> <?php echo $style; ?>">
  <?php if($module->showtitle): ?>
  <div class="cliens-header">
    <?php if($module->showtitle): ?>
    <h3 class="section-title">  <span><?php echo $module->title ?></span> </h3>
    <?php endif; ?>
  </div>
  <?php endif; ?>
   <div class="row <?php if($count > $columns): ?> owl-carousel <?php endif; ?>">
	 <?php 
	 	for ($i=0; $i<$count; $i++) : 
	 	
		$clientName = $helper->get('client-item.client-name',$i);
		$clientLink = $helper->get('client-item.client-link',$i);
		$clientLogo = $helper->get('client-item.client-logo',$i);
	  ?>
		<div class="<?php if($count <= $columns): ?>col-xs-12 <?php endif; ?>client-item" <?php if($count <= $columns): ?>style="width:<?php echo number_format(100/$columns, 2, '.', ' ') - $float;?>%;"<?php endif; ?>>
			<div class="client-img">
				<?php if($clientLink):?><a href="<?php echo $clientLink; ?>" title="<?php echo $clientName; ?>" ><?php endif; ?>
					<img class="img-responsive" alt="<?php echo $clientName; ?>" src="<?php echo $clientLogo; ?>">
				<?php if($clientLink):?></a><?php endif; ?>
			</div>
		</div> 
 	<?php endfor ?>
  </div>
</div>

<?php if($opacity>=0 && $opacity<=100): ?>
<script>
(function ($) {
	$(document).ready(function(){ 
		$('#uber-cliens-<?php echo $module->id ?> .client-img img.img-responsive').css({
			'filter':'alpha(opacity=<?php echo $opacity ?>)', 
			'zoom':'1', 
			'opacity':'<?php echo $opacity/100 ?>'
		});
	});
})(jQuery);
</script>
<?php endif; ?>

<script>
(function ($) {
  $(document).ready(function(){ 
    $("#uber-cliens-<?php echo $module->id ?> .owl-carousel").owlCarousel({
      navigation : true,
      pagination: false,
      items: <?php echo $columns; ?>,
      loop: false,
      scrollPerPage : true,
      autoHeight: true,
      navigationText : ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>"],
      itemsDesktop : [1199, 5],
      itemsDesktopSmall : [979, 4],
      itemsTablet : [768, 4],
      itemsTabletSmall : [600, 3],
      itemsMobile : [479, 2]
    });
  });
})(jQuery);
</script>