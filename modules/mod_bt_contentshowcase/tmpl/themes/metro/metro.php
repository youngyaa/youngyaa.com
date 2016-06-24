<?php
/**
 * @package 	mod_bt_contentshowcase - BT ContentShowcase Module
 * @version		2.1
 * @created		Jan 2013
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
if(count($list)>0) :
	$document = JFactory::getDocument();
	$itemsPerCol = (int)$params->get( 'items_per_cols', 2 );
	$itemsPerRow = (int)$params->get( 'items_per_rows', 2);
	$scroll_direction = $params->get('scroll_direction');
	$itemMargin = $params->get('item_margin');
	switch($scroll_direction){
		case '0': $scroll_direction = 'ltr'; break;
		case '1': $scroll_direction = 'rtl'; break;
		case '2': $scroll_direction = 'ttb'; break;
		case '3': $scroll_direction = 'btt'; break;
		default: $scroll_direction = 'ltr'; break;
	}
	//declare css for the module
	$width = $itemsPerRow * $thumbWidth + ($itemsPerRow - 1) * $itemMargin;
	$height = $itemsPerCol * $thumbHeight + ($itemsPerCol - 1) * $itemMargin;
    $moduleCssId = '#btcontentshowcase'. $module->id;
    $css = $moduleCssId.' .metro-item{width: ' . $thumbWidth . 'px ; height: '. $thumbHeight .'px; margin: 0 ' . $itemMargin . 'px '. $itemMargin . 'px 0;}';
	$visible = $itemsPerCol * $itemsPerRow >= count($list)? 0 : $itemsPerCol * $itemsPerRow;
	if($visible){
		//$css .= $moduleCssId . ' .metro-slider {width: ' . $width . 'px; height: ' . $height . 'px;}';
		//$css .= $moduleCssId .' .metro-slide{width: ' . ($width + $itemMargin). 'px; height: ' . $height . 'px;}';
	}
	$css .= ' '. $moduleCssId . ' .metro-slider .mi-back{background-color: ' . $params->get('back_side_bg') . ';}';
	$css .= ' '. $moduleCssId . ' .mi-back *{color: ' . $params->get('bs_text_color', '#ffffff') . ';}';
    $document->addStyleDeclaration($css);
	

?>

<div class="metro-layout">

	<div id="btcontentshowcase<?php echo $module->id; ?>"  class="bt-contentshowcase<?php echo $moduleclass_sfx ? ' bt-contentshowcase'.$params->get('moduleclass_sfx'):'';?>">
		<?php
		if($params->get('content_title')){
		?>
		<h3>
                    <?php if($params->get('content_title_link')) {?>
                        <a href="<?php echo $params->get('content_title_link');?>"><span><?php echo $params->get('content_title') ?> </span></a>
                    <?php } else { ?>
                        <span><?php echo $params->get('content_title') ?> </span>                    
                    <?php   }?>
                        
		</h3>
		<?php } ?>
		<div  class="wrapper">
			<div class="metro-slider">
			<?php 
			foreach($list as $item){
			?>
				<div class="metro-item">
					<div class="mi-front">
						<img src="<?php echo $item->thumbnail; ?>" alt="<?php echo $item->title?>"  style="width:100%;" />
					</div>
					<div class="mi-back">
						<div class="mi-back-container">
							<div class="top">
							<?php 
							if($showDate):
								$date = getdate(strtotime($item->date));
							?>
								<div class="bt-date">
									<div class="bt-day"><?php echo str_pad($date['mday'], 2, '0', STR_PAD_LEFT) ?></div>
									<div class="bt-month"><?php echo JText::_('BT_MONTH_' . strtoupper($date['month'])) ?></div>
								</div>	
							<?php endif;?>	
							<?php
							if($showTitle):?>
								<div class="bt-title">
									<h5>
										<a class="bt-title" target="<?php echo $openTarget; ?>" title="<?php echo $item->title; ?>" href="<?php echo $item->link;?>"> 
											<?php echo $item->title_cut; ?> 
										</a>
									</h5>
								</div>
							<?php endif;?>
							</div>
							<div class="bottom">
							<?php 
							$separator = '';
							if($showAuthor && isset($item->author)): 
								$separator = ',';
							?>
								<span class="bt-author"><?php echo JText::sprintf('BT_CREATEDBY' , JHtml::_('link',JRoute::_($item->authorLink),$item->author)); ?></span>
							<?php endif?>

							<?php if(isset($item->review_count)): ?>
								<?php if($separator) echo $separator;?>
								<span class="bt-comment"><?php echo $item->review_count?> <?php echo JText::_('BT_NUMBER_COMMENT_' . (($item->review_count > 1) ? 'PLURAL' : 'SINGULAR')); ?></span>
							<?php endif;?>
							
							</div>
						</div>	
					</div>
				</div>
			<?php
			}
			?>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$B(document).ready(function(){
		$B('#btcontentshowcase<?php echo $module->id; ?> .metro-slider').metroslide({
			mode: '<?php echo $params->get('metro_effect', 'slide')?>',
			direction: '<?php echo $scroll_direction?>',
			autoplay: <?php echo $params->get('auto_start') ? 'true' : 'false'?>, 
			interval : <?php echo $params->get('interval', 3) * 1000 ?>,
			speed: <?php echo $params->get('duration', 500)?> , 
			pauseOnHover: <?php echo $params->get('pause_hover') ? 'true' : 'false'?> , 
			animateOnHover: <?php echo $params->get('hovereffect') ? 'true' : 'false' ?>,
			preloadImages: true, // preload images
			visible: <?php echo $visible;?>, //number of item per slide which you can be see
			itemWidth: <?php echo $thumbWidth ?>, //specify width of item
			itemHeight: <?php echo $thumbHeight ?>, //specify height of item
		});
	});
</script>	
<?php else : ?>
<div>No result...</div>
<?php endif; ?>
<div style="clear: both;"></div>

