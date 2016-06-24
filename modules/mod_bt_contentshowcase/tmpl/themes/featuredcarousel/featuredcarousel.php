<?php
/**
 * @package 	mod_bt_contentshowcase - BT ContentShowcase Module
 * @version		2.3.3
 * @created		January 2013
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
if(count($list)>0) {?>
<div class="fcLayout" style="width:<?php echo $moduleWidthWrapper;?>; ">
	<div id="btcontentshowcase<?php echo $module->id; ?>"  class="bt-contentshowcase<?php echo $moduleclass_sfx ? ' bt-contentshowcase'.$params->get('moduleclass_sfx'):'';?>">
		<h3>
			<?php if($params->get('content_title_link')) {?>
				<a href="<?php echo $params->get('content_title_link');?>"><span><?php echo $params->get('content_title') ?> </span></a>
			<?php } else { ?>
				<span><?php echo $params->get('content_title') ?> </span>                    
			<?php   }?>
                        
		</h3>
		<?php if($navigationPosition == 'top'){?>
		<div class="bt-pagination bt-pagination-<?php echo $navigationType?>" id="btcontentshowcase-pagination-<?php echo $module->id?>"></div>
		<?php }?>
		<div class="fc-container">
			<div class="fc-slider">
				<?php foreach($list as $row){?>			
				<div class="bt-slide">
					<img alt="<?php echo $row->title?>" src="<?php echo $row->thumbnail; ?>"/>
					<div class="bt-slide-info">
						<div class="bt-slide-cross">
							<?php if($modal){?>
							<a href="<?php echo $row->mainImage?>" class="modal">+</a>
							<?php }else{?>
							<a title="<?php echo $row->title; ?>" href="<?php echo $row->link?>" target="<?php echo $openTarget?>">+</a>
							<?php }?>
						</div>
						<div class="bt-slide-inner">
							<?php if( $showAuthor || $showDate ){ ?>
							<div class="bt-slide-extra">
								<?php if( $showAuthor ){ ?>
								<span class="bt-author"><?php echo JText::sprintf('BT_CREATEDBY',JHtml::_('link',JRoute::_($row->authorLink),$row->author)); ?></span>
								<?php }?>
								<?php if( $showDate ){?>
								<span class="bt-date"><?php echo JText::sprintf('BT_CREATEDON', $row->date); ?></span>
								<?php }?>
							</div>
							<?php }?>
							<?php if( $showTitle ){ ?>
							<div class="bt-slide-title">
								<a title="<?php echo $row->title; ?>" href="<?php echo $row->link?>" target="<?php echo $openTarget?>"><?php echo $row->title_cut; ?></a>
							</div>
							<?php }?>
							<?php if( $show_category_name ){ ?>
							<div class="bt-slide-category">
								<?php if($show_category_name_as_link){ ?>
								<a target="<?php echo $openTarget; ?>" title="<?php echo $row->category_title; ?>" href="<?php echo $row->categoryLink;?>"> 
								<?php echo $row->category_title; ?>
								</a>
								<?php }else{?>
								<span> <?php echo $row->category_title; ?> </span>
								<?php }?>
							</div>
							<?php }?>
						</div>	
					</div>
				</div>
				<?php }?>
			</div>
			<?php if($params->get('next_prev', 1)){?>
			<a class="btcontentshowcase-prev" id="btcontentshowcase-prev-<?php echo $module->id?>" href="#"></a>
			<a class="btcontentshowcase-next" id="btcontentshowcase-next-<?php echo $module->id?>" href="#"></a>
			<?php }?>
		</div>
		<?php if($navigationPosition == 'bottom'){?>
		<div class="bt-pagination bt-pagination-<?php echo $navigationType?>" id="btcontentshowcase-pagination-<?php echo $module->id?>"></div>
		<?php }?>
	</div>
	<script type="text/javascript">
	$B(document).ready(function($) {
		var thumbnailWidth = <?php echo $thumbWidth?>;
		var thumbnailHeight = <?php echo $thumbHeight?>;
		var smallWidth = <?php echo $params->get('thumbnail_small_width', 90)?>;
		var smallHeight = smallWidth * thumbnailHeight / thumbnailWidth;
		var topMargin = (thumbnailHeight - smallHeight) / 2;
		var duration = <?php echo (int)$params->get('duration', '1000')?>;
		var lrMargin = <?php echo $params->get('item_margin', 0)?>;
		var cssSmall = { 
			width: smallWidth,
			height: smallHeight,
			marginTop: topMargin,
			marginLeft: lrMargin,
			marginRight: lrMargin,
			opacity: 0.5
		};
		var cssLarge = { 
			width: thumbnailWidth, // fixed image width
			height: thumbnailHeight, // fixed image height
			marginTop: 0,
			opacity: 1.0
		};
		
		var conf = {
			queue: false,
			duration: duration
		};

		var slider = $('#btcontentshowcase<?php echo $module->id; ?> .fc-slider');
		//
		slider.hide(); // hide the div to prevent Flash Of Unstyled Content (F.O.U.C.)
		slider.imagesLoaded( function() {
			slider.show();
			slider.find('.bt-slide').css( cssSmall );
			slider.find('.bt-slide').eq(1).css( cssLarge );
			slider.find( '.bt-slide' ).eq(1).mouseenter(function(){
				$(this).find('.bt-slide-info').stop();
				$(this).find('.bt-slide-info' ).css({ display:'block' }).animate({opacity: '1'}, 300);
			}).mouseleave(function(){
			$(this).find('.bt-slide-info').stop();
				$(this).find('.bt-slide-info').animate({ opacity: '0' }, 300, function(){ $(this).css({ display:'none' }) });
			});
			
			slider.carouFredSel({
				width: thumbnailWidth + 2 * smallWidth + 6 * lrMargin,
				height: thumbnailHeight,
				direction: 'left',
				auto: {
					play: <?php echo $auto_start ? 'true' : 'false'?>,
					duration: <?php echo $params->get('interval', 5) * 200 ?>
				},
				items   : 3,					
				scroll  : {
					easing: 'linear',
					items: 1,
					fx: 'scroll',
					duration:  <?php echo $params->get('interval', 5) * 200 ?>,
					pauseOnHover: <?php echo $params->get('pause_hover', 1) ? 'true' : 'false'?>,
					onBefore: function( data ) {
						//	0 [ 1 ] 2
						data.items.old.eq(1).animate(cssSmall, conf);							
						data.items.old.eq(1).unbind( 'mouseenter mouseleave' ); // Unbind slides that are not in center
						data.items.old.eq(1).find('.bt-slide-info').animate({ opacity:'0' }, conf.duration, function(){ $(this).css({ display:'none'}) }); // When using keyboard
						
						//	0  1 [ 2 ]
						data.items.old.eq(2).animate(cssLarge, conf);						
						data.items.old.eq(2).mouseenter(function(){
							$(this).find('.bt-slide-info').stop();
							$(this).find('.bt-slide-info').css({ display:'block' }).animate({ opacity: '1' });
						}).mouseleave(function(){
							$(this).find('.bt-slide-info').stop();
							$(this).find('.bt-slide-info').animate({ opacity: '0' }, conf.duration, function(){ $(this).css({ display:'none' }) });
						});
					}
				},
				next: {
					button: '.btcontentshowcase-next'
				},
				prev: {
					button: '.btcontentshowcase-prev',
					onBefore: function( data ) {
						//	0 [ 1 ] 2
						data.items.old.eq(1).animate(cssSmall, conf);
						data.items.old.eq(1).unbind('mouseenter mouseleave'); // Unbind slides that are not in center
						data.items.old.eq(1).find('.bt-slide-info').animate({ opacity:'0' }, 300, function(){ $(this).css({ display:'none'}) }); // When using keyboard
						
						//	[ 0 ]  1 2
						data.items.old.eq(0).animate(cssLarge, conf);
						data.items.old.eq(0).mouseenter(function(){
							$(this).find('.bt-slide-info').stop();
							$(this).find('.bt-slide-info').css({ display:'block' }).animate({ opacity: '1'}, 300);
						}).mouseleave(function(){
							$(this).find('.bt-slide-info').stop();
							$(this).find('.bt-slide-info').animate({ opacity: '0' }, 300, function(){ $(this).css({ display:'none' }) });
						});
					}
				},
				pagination : {
					container: '#btcontentshowcase-pagination-<?php echo $module->id?>',
					onBefore: function( data ) {
						data.items.old.eq(1).animate(cssSmall, conf);
						data.items.old.eq(1).unbind('mouseenter mouseleave'); // Unbind slides that are not in center
						data.items.old.eq(1).find('.bt-slide-info').animate({ opacity:'0' }, 300, function(){ $(this).css({ display:'none'}) }); // When using keyboard

						data.items.visible.eq(1).animate(cssLarge, conf);
						data.items.visible.eq(1).mouseenter(function(){
							$(this).find('.bt-slide-info').stop();
							$(this).find('.bt-slide-info').css({ display:'block' }).animate({ opacity: '1'}, 300);
						}).mouseleave(function(){
							$(this).find('.bt-slide-info').stop();
							$(this).find('.bt-slide-info').animate({ opacity: '0' }, 300, function(){ $(this).css({ display:'none' }) });
						});
						
					},
					onAfter: function(data){
						slider.css('left', 0);
					}
				},
				swipe: {
					onTouch: <?php echo $params->get('touchscreen', 1) ? 'true' : 'false'?>
				}
			});
			
			$(window).resize(function(){
				var width = $('#btcontentshowcase<?php echo $module->id; ?> .fc-container').width();
				var sliderWidth  = $('#btcontentshowcase<?php echo $module->id; ?> .caroufredsel_wrapper').width();
				var left = Math.floor((width - sliderWidth)/2);
				$('#btcontentshowcase<?php echo $module->id; ?> .caroufredsel_wrapper').css('left', left + 'px');
			}).trigger('resize');
			
			slider.find('.bt-slide').click(function(){
				var direction = $(this).index() == 2 ? 'next' : ($(this).index() == 0 ? 'prev' : '');
				if(direction != ''){
					slider.trigger(direction);
				}
			});
		});
	
	});	
	</script>
</div>
<?php }else{?>
<?php }?>