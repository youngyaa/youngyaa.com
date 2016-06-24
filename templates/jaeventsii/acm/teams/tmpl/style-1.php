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
 
  $count = $helper->getRows('member-name');
  $col = $helper->get('number_col');
?>

<div class="acm-teams">
	<div class="style-1 team-items <?php if($params->get('module-full-width')) echo 'full-width'; ?>">
  <?php if($module->showtitle || $count >($col*2)): ?>
  <div class="team-items-head">
    <?php if($module->showtitle): ?>
    <h3 class="section-title ">  <span><?php echo $module->title ?></span> </h3>
    <?php endif; ?>
  </div>
  <?php endif; ?> 
		<?php
      for ($i=0; $i < $count; $i++) :
        if ($i%$col==0) echo '<div class="row">'; 
    ?>
		<div class="item col-sm-6 col-md-<?php echo (12/$col); ?>">
      <div class="item-inner">
        <div class="member-image">
          <img src="<?php echo $helper->get('member-image', $i); ?>" alt="<?php echo $helper->get('member-name', $i); ?>" />
        </div>
        
        <div class="mask">
          <h4><?php echo $helper->get('member-name', $i); ?><span><?php echo $helper->get('member-position', $i); ?></span></h4>
          <ul class="member-links">
            <?php
              for($j=1; $j <= 5; $j++) :
                if(trim($helper->get('member-link-icon'.$j, $i)) != ""):
            ?>
            <li><a href="<?php echo $helper->get('member-link'.$j, $i); ?>" title=""><i class="<?php echo $helper->get('member-link-icon'.$j, $i); ?>"></i></a></li>
            <?php
              endif;
            endfor;
            ?>
          </ul>
        </div>
      </div>
		</div>
    
    <?php if ( ($i%$col==($col-1)) || $i==($count-1) )  echo '</div>'; ?>
		<?php endfor; ?>

    <?php if($helper->get('block-intro')): ?>
    <div class="team-items-footer">
      <?php echo $helper->get('block-intro'); ?>
    </div>
    <?php endif; ?>
	</div>
</div>