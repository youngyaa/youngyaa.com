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
$pricingStyle = $helper->get('pricing-style');
?>

<div class="section-inner <?php echo $helper->get('block-extra-class'); ?>" >
	<?php if($module->showtitle || $helper->get('block-intro')): ?>
	<h3 class="section-title ">
		<?php if($module->showtitle): ?>
			<span><?php echo $module->title ?></span>
		<?php endif; ?>
		<?php if($helper->get('block-intro')): ?>
			<p class="container-sm section-intro hidden-xs"><?php echo $helper->get('block-intro'); ?></p>
		<?php endif; ?>	
	</h3>
	<?php endif; ?>
    
  <div class="acm-pricing">
  	<div class="pricing-table style-1">
  		<div class="row">
  
  			<?php
  			$count = $helper->getCols('data');
  			$features_count = $helper->getRows('data');
  			if (!$count || !$features_count) {
  				$count = $helper->count('pricing-col-name');
  				$features_count = $helper->count('pricing-row-name');
  			}
  			?>
  
  			<?php for ($col = 0; $col < $count; $col++) : ?>
  				<div
  					class="col col-md-<?php echo 12 / ($count); ?> <?php if ($helper->get('data.pricing-col-featured', $col)): ?> col-featured shadow <?php endif ?> no-padding">
  					<div class="col-header text-center">
  						<h2><?php echo $helper->get('data.pricing-col-name', $col) ?></h2>
  						<p><span class="big-number"><?php echo $helper->get('data.pricing-col-price', $col) ?></span></p>
  					</div>
  					<div class="col-body">
  						<ul>
  							<?php for ($row = 0; $row < $features_count; $row++) :
  								$feature = $helper->getCell('data', $row, 0);
  								$value = $helper->getCell('data', $row, $col + 1);
  								$type = $value[0];
  								
  								if (!$feature) {
  									// compatible with old data
  									$feature = $helper->get('pricing-row-name', $row);
  									$tmp = $helper->get('pricing-row-supportfor', $row);
  									$value = ($tmp & pow(2, $col)) ? 'b1' : 'b0'; // b1: yes, b0: no
  									$type = 'b'; // boolean
  								}
  								?>
  
  							<?php if ($type == 't'): ?>
  								<li class="row<?php echo($row % 2); ?>"><?php echo substr($value, 1); ?></li>
  							<?php elseif ($value == 'b1'): ?>
  								<li class="row<?php echo($row % 2); ?>"><?php echo $feature; ?></li>
  							<?php
  							else: ?>
  								<li class="row<?php echo($row % 2); ?> no"><?php echo $feature; ?></li>
  							<?php endif ?>
  
  							<?php endfor; ?>
  						</ul>
  					</div>
  					<div class="col-footer text-center">
  						<a
  							class="btn btn-lg btn-rounded <?php if ($helper->get('data.pricing-col-featured', $col)): ?> btn-success <?php else: ?> btn-default <?php endif ?>"
  							title="<?php echo $helper->get('data.pricing-col-button', $col); ?>"
  							href="<?php echo $helper->get('data.pricing-col-buttonlink', $col); ?>"><?php echo $helper->get('data.pricing-col-button', $col); ?></a>
  					</div>
  				</div>
  			<?php endfor; ?>
  
  		</div>
  	</div>
  </div>
</div>