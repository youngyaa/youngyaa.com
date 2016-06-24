<?php
/**
 * @package 	mod_bt_contentshowcase - BT ContentShowcase Module
 * @version		2.4.4
 * @created		March 2015
 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
$document = JFactory::getDocument();

?>
<?php if(count($list)>0) :?>
<div class="bt-bistro-layout">
	<div id="btcontentshowcase<?php echo $module->id; ?>"  class="bt-contentshowcase<?php echo $moduleclass_sfx ? ' bt-contentshowcase'.$params->get('moduleclass_sfx'):'';?>">	
		<?php if($params->get('content_title', '')){?>
		<h3>
			<?php if($params->get('content_title_link')) {?>
				<a href="<?php echo $params->get('content_title_link');?>"><span><?php echo $params->get('content_title') ?> </span></a>
			<?php } else { ?>
				<span><?php echo $params->get('content_title') ?> </span>                    
			<?php   }?>                
		</h3>
		<?php } ?>
		<div class="bt-bistro-wrapper">
			<?php foreach( $list as $i => $row ): ?>
			<div class="bt-bistro-item">
				<div class="bt-item-thumbnail">
					<?php if($row->thumbnail){?>
					<a target="<?php echo $openTarget; ?>" title="<?php echo $row->title;?>" href="<?php echo $row->link;?>">
						<img src="<?php echo $row->thumbnail; ?>" alt="<?php echo $row->title?>"/>
					</a>
					<?php }?>
				</div>
				<div class="bt-item-summary">
					<?php if($params->get('show_category_image', true) && isset($row->category_image)){?>
					<div class="bt-category-image">
						<img src="<?php echo JURI::root(true).'/media/k2/categories/'. $row->category_image?>" alt="<?php echo $row->category_title?>"/>
					</div>
					<?php }?>
					<?php if($showTitle){?>
					<h4 class="bt-item-title">
						<a target="<?php echo $openTarget; ?>" title="<?php echo $row->title;?>" href="<?php echo $row->link;?>">
						<?php echo $row->title?>
						</a>
					</h4>
					<?php }?>
					<?php 
					$showedExtrafields = $params->get('show_extrafields', array());
					$price = '';
					if(count($showedExtrafields) && $row->extra_fields && count($row->extra_fields)){ 
					?>
					<ul class="bt-item-extrafields">
							<?php
							foreach($row->extra_fields as $ex){
								if(in_array($ex->id, $showedExtrafields)){
									if($ex->id == $params->get('extrafield_price_id', 0)){
										$price = $ex; 
										continue;
									}
							?>
						<li><?php if(isset($ex->name)) {?><span><?php echo $ex->name?></span>: <?php }?><span><?php echo $ex->value?></span></li>
							<?php
								}
							}
							?>
							
					<ul>
					
					<?php if($price){?>
					<div class="bt-item-price">
					<?php if($params->get('price_unit', '')) {
						echo sprintf($params->get('price_unit'), $price->value);
					}else{
						echo $price;
					}
					?>
					</div>
					<?php 
						}
					}?>
				</div>
			</div>
            <?php endforeach; ?>        
        </div>
	</div>
</div>
<?php else : ?>
<div>No result...</div>
<?php endif; ?>
<div style="clear: both;"></div>