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
	$featuresImg 				= $helper->get('block-bg');
	$featuresBackground  = 'background-image: url("'.$featuresImg.'"); background-repeat: no-repeat; background-size: cover; background-position: center center;';
?>

<div class="acm-features style-2 <?php echo $helper->get('features-style'); ?> <?php if($params->get('module-full-width')) echo 'full-width'; ?>">
	<?php $count = $helper->getRows('data.title'); ?>
	<?php $column = $helper->get('columns'); ?>
	<?php 
		for ($i=0; $i<$count; $i++) : 
		if ($i%$column==0) echo '<div class="row equal-height">'; 
	?>
	
		<div class="features-item col <?php if($i==0) echo 'features-item-first'; ?> col-sm-<?php echo 12/$column ?> center">
			
			<?php if($helper->get('data.font-icon', $i)) : ?>
				<div class="font-icon">
					<span><i class="<?php echo $helper->get('data.font-icon', $i) ; ?>"></i></span>
				</div>
			<?php endif ; ?>
			
			<?php if($helper->get('data.title', $i)) : ?>
				<h3><?php echo $helper->get('data.title', $i) ?></h3>
			<?php endif ; ?>
			
			<?php if($helper->get('data.description', $i)) : ?>
				<p data-letters="<?php echo $helper->get('data.description', $i) ?>"><?php echo $helper->get('data.description', $i) ?></p>
			<?php endif ; ?>

			<?php if($helper->get('data.button-value', $i)) : ?>
				<a class="btn <?php if($i%2==0): echo 'btn-secondary'; else: echo 'btn-primary'; endif; ?>" href="<?php echo $helper->get('data.link', $i); ?>"><?php echo $helper->get('data.button-value', $i); ?></a>
			<?php endif ; ?>
		</div>
		<?php if ( ($i%$column==($column-1)) || $i==($count-1) )  echo '</div>'; ?>
	<?php endfor ?>
</div>