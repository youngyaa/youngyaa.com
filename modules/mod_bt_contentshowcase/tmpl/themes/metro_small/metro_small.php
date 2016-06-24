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
if($modal){
	JHTML::_('behavior.modal');
}
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
								<a href="<?php echo $modal ? $item->mainImage: $item->link; ?>" rel="{handler: 'image'}"  class="maginfier <?php echo $modal? ' modal':''?>"></a>
								<a target="<?php echo $openTarget; ?>" title="<?php echo $item->title; ?>" href="<?php echo $item->link;?>" rel="lightbox" class="readmore"></a>
							</div>
							<div class="bottom">
								<?php if( $show_category_name ): ?>
								<?php 	if($show_category_name_as_link) : ?>
								<a class="bt-category" target="<?php echo $openTarget; ?>" title="<?php echo $item->category_title; ?>" href="<?php echo $item->categoryLink;?>"> <?php echo $item->category_title; ?></a>
								<?php else :?>
								<span class="bt-category"> <?php echo $title->category_title; ?> </span>
							<?php 	endif; ?>
							<?php endif; ?>
							
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
			visible: <?php echo $visible?>, //number of item per slide which you can be see
			itemWidth: <?php echo $thumbWidth ?>, //specify width of item
			itemHeight: <?php echo $thumbHeight ?>, //specify height of item
		});
	});
</script>	
<?php else : ?>
<div>No result...</div>
<?php endif; ?>
<div style="clear: both;"></div>

