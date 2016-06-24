<?php
/**
 * @package 	mod_bt_contentshowcase - BT ContentShowcase Module
 * @version		1.0
 * @created		June 2012
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
$document = JFactory::getDocument();
$itemsPerCol = (int)$params->get( 'items_per_cols', 1 );
$itemsPerRow = (int)$params->get( 'items_per_rows', 3 );
//Num of item display
$itemPerLi = $itemsPerCol;
$itemVisible = $itemsPerRow;
//Get pages list array
$pages = array_chunk( $list, $itemPerLi  );
//Get total pages
$totalLiTags = count($pages);
// calculate width of each row.
$itemWidth = 100;
$liWidth = $moduleWidth == 'auto' ? $params->get('item_min_width',300) : number_format(100/$itemsPerRow,2);
$liHeight = $params->get('item_height')=='auto'? 'auto':($params->get('item_height') + 20) * $itemsPerCol;

?>


<?php if(count($list)>0) :?>
<div class="defaultLayout" style="width:<?php echo $moduleWidthWrapper;?>; ">

	<div id="btcontentshowcase<?php echo $module->id; ?>"  class="bt-contentshowcase<?php echo $moduleclass_sfx ? ' bt-contentshowcase'.$params->get('moduleclass_sfx'):'';?>">
		
		<?php 
			$add_style = "";
			if( trim($params->get('content_title')) ){
			$add_style= "border: 1px solid #CFCFCF;";
		?>
		<h3>
			<?php if($params->get('content_title_link')) {?>
				<a href="<?php echo $params->get('content_title_link');?>"><span><?php echo $params->get('content_title') ?> </span></a>
			<?php } else { ?>
				<span><?php echo $params->get('content_title') ?> </span>                    
			<?php   }?>
                        
		</h3>
		<?php } ?>
		<div  style="<?php echo $add_style;?>">
			<?php if(count($list) > ($itemsPerCol * $itemsPerRow)){?>
				<?php if($nextBackPosition == 'flanks'){?>
				<div class="btcontentshowcase-prev"></div>
				<div class="btcontentshowcase-next"></div>
				<?php } ?>
				<?php
				//if both of navigation and button are showed and their position is top
				if(($nextBackPosition == 'top') || ($navigationPosition == 'top')){
					
				?>
				<div id="btcontentshowcase-control">
					<?php 
					//show prev button first if available
					if($nextBackPosition == 'top'){
					?>
					<div class="btcontentshowcase-next"></div>
					<?php
					}
					
					//show navigation
					if($navigationPosition == 'top'){
					?>
					<div class="btcontentshowcase-navigation">
						
					</div>
					<?php
					}
					?>
					<?php 
					//show next button if available
					if($nextBackPosition == 'top'){
					?>
					<div class="btcontentshowcase-prev"></div>
					<?php
					}
					?>
					<div style="clear: both;"></div>
				</div>
				<?php 
				}
				?>
			<?php }?>		
				<ul id="btcontentshowcase<?php echo $module->id; ?>_jcarousel" class="jcarousel jcarousel-skin-tango" >
					<?php foreach( $pages as $key => $list ): ?>
					<?php //class="'. (($i==0) ? 'bt-row-first' : (($i==count($list)-1) ? 'bt-row-last' : '')) . '"?> 
					<?php echo '<li>'?>
					<?php foreach( $list as $i => $row ): ?>
						
						<div class="bt-row " style="width: <?php echo $itemWidth?>%;">
				<div class="bt-inner">
									<?php if($row->thumbnail && $align_image != 'center'){?>
										<div style="float: <?php echo $align_image ;?>;">
					<a target="<?php echo $openTarget; ?>"
						class="bt-image-link<?php echo $modal? ' modal':''?>"
						title="<?php echo $row->title;?>" href="<?php echo $modal?$row->mainImage:$row->link;?>">
						<img <?php echo $imgClass ?> src="<?php echo $row->thumbnail; ?>" alt="<?php echo $row->title?>"  style="width:<?php echo $thumbWidth ;?>px;" title="<?php echo $row->title?>" />
					</a>
										</div>
									<?php } ?>
				<?php if( $show_category_name ): ?>
				<?php if($show_category_name_as_link) : ?>
					<a class="bt-category" target="<?php echo $openTarget; ?>"
						title="<?php echo $row->category_title; ?>"
						href="<?php echo $row->categoryLink;?>"> <?php echo $row->category_title; ?>
					</a>
					<?php else :?>
					<span class="bt-category"> <?php echo $row->category_title; ?> </span>
					<?php endif; ?>
					<?php endif; ?>

					<?php if( $showTitle ): ?>
					<a class="bt-title" target="<?php echo $openTarget; ?>"
						title="<?php echo $row->title; ?>"
						href="<?php echo $row->link;?>"> <?php echo $row->title_cut; ?> </a>
						<?php endif; ?>
						<?php if( $row->thumbnail ): ?>
											<?php if($row->thumbnail && $align_image == 'center') {?>
					<div style="text-align:center">
					<a target="<?php echo $openTarget; ?>"
						class="bt-image-link<?php echo $modal? ' modal':''?>"
						title="<?php echo $row->title;?>" href="<?php echo $modal?$row->mainImage:$row->link;?>">
						<img <?php echo $imgClass ?> src="<?php echo $row->thumbnail; ?>" alt="<?php echo $row->title?>"  style="width:<?php echo $thumbWidth ;?>px;" title="<?php echo $row->title?>" />
					</a>
					</div>
											<?php } ?>
					<?php endif ; ?>
					<?php if( $showAuthor || $showDate ): ?>
					<div class="bt-extra">
					<?php if( $showAuthor ): ?>
						<span class="bt-author"><?php echo JText::sprintf('BT_CREATEDBY',JHtml::_('link',JRoute::_($row->authorLink),$row->author)); ?>
						</span>
						<?php endif; ?>
						<?php if( $showDate ): ?>
						<span class="bt-date"><?php echo JText::sprintf('BT_CREATEDON', $row->date); ?>
						</span>
						<?php endif; ?>
					</div>
					<?php endif; ?>

					<?php if( $show_intro ): ?>
					<div class="bt-introtext">
					<?php echo $row->description; ?>
					</div>
					<?php endif; ?>

					<?php if( $showReadmore ) : ?>
					<p class="readmore">
						<a target="<?php echo $openTarget; ?>"
							title="<?php echo $row->title;?>"
							href="<?php echo $row->link;?>"> <?php echo JText::_('READ_MORE');?>
						</a>
					</p>
					<?php endif; ?>

				</div>
				<!-- bt-inner -->

			</div>
			<!-- bt-row -->
			<?php
				if(($i+1) % $itemsPerCol == 0 || $i == count($list)-1){	
					echo '</li>';
				}
			?>

			<?php endforeach; ?>
					<?php endforeach; ?>
				</ul>
			<?php if(count($list) > ($itemsPerCol * $itemsPerRow)){?>
				<?php
				//if both of navigation and button are showed and their position is bottom
				if(($nextBackPosition == 'bottom') || ($navigationPosition == 'bottom')){
					
				?>
				<div id="btcontentshowcase-control">
					<?php 
					//show prev button first if available
					if($nextBackPosition == 'bottom'){
					?>
					<div class="btcontentshowcase-next"></div>
					<?php
					}
					
					//show navigation
					if($navigationPosition == 'bottom'){
					?>
					<div class="btcontentshowcase-navigation">
						
					</div>
					<?php
					}
					?>
					<?php 
					//show next button if available
					if($nextBackPosition == 'bottom'){
					?>
					<div class="btcontentshowcase-prev"></div>
					<?php
					}
					?>
					<div style="clear: both;"></div>
				</div>
				<?php 
				}
				?>
			<?php }?>	
		</div>
	</div>
	<!-- bt-container -->
</div>
			<?php else : ?>
<div>No result...</div>
			<?php endif; ?>
<div style="clear: both;"></div>

<?php if( $totalLiTags  > 1 ): ?>
    <script type="text/javascript">
        $B(document).ready(function(){
            var moduleID = '#btcontentshowcase<?php echo $module->id; ?>';
            //init jcarousel
            $B(moduleID + ' .jcarousel').jcarousel({
                initCallback: function(carousel, state){
                	<?php if($params->get('touchscreen')){ ?>
					var timeStart, xStart, yStart;
					$B(carousel.list.parent().eq(0)).on('touchstart', function(e){
						var touch = e.originalEvent.touches[0];
						xStart = touch.pageX;
						yStart= touch.pageY;
						timeStart = Number(new Date());
					});
					$B(carousel.list.parent().eq(0)).on('touchend', function(e){
						var touch = e.originalEvent.changedTouches[0];
						var duration = Number(new Date()) - timeStart;
						var xDistance = touch.pageX - xStart;
						var yDistance = touch.pageY - yStart;
						if(duration <= 300 && Math.abs(yDistance) <= 20 && xDistance != 0){
							if(carousel.options.onAnimate){return}
							if(xDistance < 0){
								carousel.next();
							}else{
								carousel.prev();
							}
						}else{
							return;
						}
					});
                	<?php }?>
                    
					<?php if($moduleWidth == 'auto') {?>
                    $B(window).resize(function(){
						var minWidth = <?php echo $liWidth ?>;
						var minOutterWidth = 	minWidth 
												+ parseInt($B(moduleID + ' .jcarousel-item').css('margin-left')) 
												+ parseInt($B(moduleID + ' .jcarousel-item').css('margin-right')); 
						var numberItem = $B(moduleID + ' .jcarousel-item').length;
						var width = $B(moduleID + ' .jcarousel-container').parent().innerWidth();	
						$B(moduleID + ' .jcarousel-container, ' + moduleID + ' .jcarousel-clip').width(width);
						var availableItem = Math.floor( width / minOutterWidth);
						if(availableItem == 0) availableItem = 1;
						var delta = 0;
						var newWidth = 0;
						if(width > minOutterWidth){
							if(availableItem > numberItem){
								delta = Math.floor((width - minOutterWidth * numberItem) / numberItem);
							}else {
								delta = Math.floor(width % minOutterWidth / availableItem);
							}
							newWidth = minWidth + delta;
						}else{
							newWidth = width;
						}

							
						carousel.options.visible = availableItem;
						var isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor);
						var isSafari = /Safari/.test(navigator.userAgent) && /Apple Computer/.test(navigator.vendor);
						if(isChrome || isSafari ){
							$B(moduleID + ' .jcarousel-item').width(newWidth);
							$B(moduleID + ' .jcarousel-list').width(carousel.options.size * $B(moduleID + ' .jcarousel-item').outerWidth(true)); 
						}else{
							carousel.funcResize();
						}
						
						renderNavigation<?php echo $module->id?>(carousel, moduleID);
						
						<?php if($align_image != 'center'){?>
						var currentLiWidth = $B(moduleID + ' .jcarousel-item').width();
						var imageWidth = $B(moduleID + ' .jcarousel-item img').eq(0).width();
						var textWidth = parseInt($B(moduleID + ' .bt-introtext').css('min-width'));
						
						if(newWidth - imageWidth < textWidth){
							$B(moduleID + ' .jcarousel-item img').parents('.bt-image').css({'text-align': 'center', 'float':''});
							$B(moduleID + ' .jcarousel-item .bt-category, ' 
									+ moduleID + ' .jcarousel-item .bt-title, '
									+ moduleID + ' .jcarousel-item .bt-introtext').css({'clear': 'both', 'margin-<?php echo $align_image?>': 0, 'width' : 'auto'});
						}else{
							$B(moduleID + ' .jcarousel-item img').parents('.bt-image').css({'text-align': '', 'float':'<?php echo $align_image?>'});
							$B(moduleID + ' .jcarousel-item .bt-category, ' 
									+ moduleID + ' .jcarousel-item .bt-title, '
									+ moduleID + ' .jcarousel-item .bt-introtext').css({'clear': '', 'margin-<?php echo $align_image?>': '', 'width' : ''});
						}
							
						<?php }?>
					});	
					$B(window).resize();
                      
					<?php }else{ ?>
					//if module's width is
					$B(moduleID + ' .jcarousel li').width(<?php echo $liWidth ?> * $B(moduleID + ' .jcarousel-clip').width() /100 - 10)     ; 
					renderNavigation<?php echo $module->id?>(carousel, moduleID);
					<?php }?>
					<?php 
					//hook next and prev
					if($nextBackPosition){?>    
					var prev = moduleID + ' .btcontentshowcase-prev';
					var next = moduleID + ' .btcontentshowcase-next';
	 
					$B(prev).unbind('click').click(function(){
						carousel.prev();
						carousel.stopAuto();
						carousel.options.auto = 10000;
						return false;
					});

					$B(next).unbind('click').click(function(){
						carousel.next(); 
						carousel.stopAuto();
						carousel.options.auto = 10000;
						return false;
					});
					<?php }?>
					<?php 
					//if turn on pause_hover
					if($params->get('pause_hover')){ ?>
					btContentShowcaseHoverCallback(carousel, state);
					<?php } ?>
					
                },

                
                itemLoadCallback:{
                    <?php if($effect == 'fade'){ ?>
                    onBeforeAnimation : function(carousel, state){
                        if(state != 'init'){
                            var containerID = carousel.clip.context.id;
                            $B('#' + containerID).animate({opacity: 0}, 500);
                        }
                    },
                    <?php }?>
                    onAfterAnimation : function(carousel, state){
						
                        carousel.options.onAnimate = false;
                        if(carousel.first == 1){
                        	carousel.options.posAfterAnimate = 0
                        }else{
							
                    		carousel.options.posAfterAnimate = carousel.pos(carousel.first);
                        }
                        <?php if($effect == 'fade'){ ?>
                        if(state != 'init'){
                            var containerID = carousel.clip.context.id;
							$B('#' + containerID).animate({opacity: 1}, 500);
                        }
                        <?php }?>
						
                        <?php if($navigationPosition) {?>
                            
                            var size = carousel.options.size;
                            var index = carousel.first;

                            
                            $B(moduleID + ' .btcontentshowcase-navigation a').removeClass('current');
                            if($B(moduleID + ' .btcontentshowcase-navigation a.<?php echo $navigationType?>-' + index).length == 0){
                                var last = carousel.last; //alert(last);
                                while (last > size){
                                    last -=size;
                                }
                                if( last == size){
                                    $B(moduleID + ' .btcontentshowcase-navigation a').last().addClass('current');
                                }else{
                                    
                                    
                                    var lastNavigation;
                                    do {
                                        last--;
                                        lastNavigation = $B(moduleID + ' .btcontentshowcase-navigation a.<?php echo $navigationType?>-' + last);
                                        if(last <=0) break;
                                    } while(lastNavigation.length == 0);
                                    
                                    lastNavigation.addClass('current');
                                }
                            }else{
								
                                $B(moduleID + ' .btcontentshowcase-navigation a.<?php echo $navigationType?>-' + index).addClass('current');
                            }
                        <?php }?>    
						
                    }
                },
                
                start: 1,
                auto: <?php echo ($params->get('auto_start')) ? $params->get('interval', 5000) : '0' ?>,
                animation: <?php echo (int)$params->get('duration', '1000')?>,
                buttonNextHTML: null,
                buttonPrevHTML: null,
                scroll : <?php echo $params->get('slide_item_per_time', 1);?>,
				<?php if($moduleWidth != 'auto') {?>
				visible: <?php echo $itemVisible ?>,
				<?php } ?>
                wrap : 'both',
                rtl: <?php echo $params->get('rtl') ? 'true' : 'false'?>
            });
        });
        <?php if($params->get('pause_hover')){ ?>
        function btContentShowcaseHoverCallback(carousel, state){
            carousel.clip.hover(function() {
                carousel.stopAuto();
            }, function() {
                carousel.startAuto();
            });
        }
        <?php } ?>
		function renderNavigation<?php echo $module->id?>(carousel, moduleID){
			<?php if($navigationPosition){?>
			if($B(moduleID + ' .btcontentshowcase-navigation').html() != ''){
				$B(moduleID + ' .btcontentshowcase-navigation').html('');
			}		
			var i = 1;
			var step = <?php echo $slide_item_per_time ?>;
			var size = $B(moduleID + ' .jcarousel li').length;
			if(carousel.options.visible < size){
				if(step >=  size){
					$B(moduleID + ' .btcontentshowcase-navigation').append('<a href="#" class="<?php echo $navigationType?> <?php echo $navigationType?>-' + (1) + '" rel="' + (1) + '">' + (1) + '</a>');
					$B(moduleID + ' .btcontentshowcase-navigation').append('<a href="#" class="<?php echo $navigationType?> <?php echo $navigationType?>-' + (size) + '" rel="' + (size) + '">' + (2) + '</a>');
				}else{
					$B(moduleID + ' .jcarousel li').each(function(){
						if((($B(this).index()) % step == 0)){
							$B(moduleID + ' .btcontentshowcase-navigation').append('<a href="#" class="<?php echo $navigationType?> <?php echo $navigationType?>-' + ($B(this).index() + 1) + '" rel="' + ($B(this).index() + 1) + '">' + (i++) + '</a>');
							if($B(this).index() + 1 + carousel.options.visible > size) return false;
							if($B(this).index() + 1 + carousel.options.visible <= size && $B(this).index() + 1 + step > size){
								$B(moduleID + ' .btcontentshowcase-navigation').append('<a href="#" class="<?php echo $navigationType?> <?php echo $navigationType?>-' + (size) + '" rel="' + (size) + '">' + (i) + '</a>');
							}
						}
					});
				}
			}
			$B(moduleID + ' .btcontentshowcase-navigation a').eq(0).addClass('current');
			$B(moduleID + ' .btcontentshowcase-navigation').append('<div style="clear: both;"></div>');
			
			//hook navigation
			$B('.btcontentshowcase-navigation a').bind('click', function() {
				if($B(this).hasClass('current')) return false;
				carousel.stopAuto();
				carousel.options.auto = 10000;
				$B(this).parent().find('.current').removeClass('current');
				$B(this).addClass('current');
				carousel.scroll($B.jcarousel.intval($B(this).attr('rel')));
				return false;
			});
			<?php } ?>
			return false;
		}
    </script>
    <?php 
    //declare css for the module
    $moduleCssId = '#btcontentshowcase'. $module->id;
    $css = $moduleCssId.' .jcarousel li{width: ' . $liWidth . ($moduleWidth == 'auto' ? 'px' : '%') .'; height: '. ($liHeight == 'auto' ? $liHeight : $liHeight .'px') . ';} ';
 
    if ($nextBackPosition == 'flanks'){
        $css.=  $moduleCssId.' .btcontentshowcase-prev{ position: absolute; left: -15px; top: 48%;} ';
        $css.=  $moduleCssId.' .btcontentshowcase-next{ position: absolute; right: -15px; top: 48%;} ';
    }

    if($params->get('item_height'))
        $css.= $moduleCssId. ' .bt-inner{ height: '. $params->get('item_height') . 'px;} ';
    
    if($align_image != 'center')
        $css.= $moduleCssId. ' .bt-inner .bt-title, '. $moduleCssId . ' .bt-inner .bt-title-nointro, '. $moduleCssId . ' .bt-inner .bt-category, '. $moduleCssId . ' .bt-inner .bt-introtext, ' . $moduleCssId . ' .bt-inner .bt-extra{ margin-' . $align_image . ': '. ($thumbWidth + 10) .'px;} ';
    
    $document->addStyleDeclaration($css);
    ?>
<?php else: ?>
<script type="text/javascript">	
	(function(){
		$B('#btcontentshowcase<?php echo $module->id; ?>').fadeIn("fast");
	})();
</script>
<?php endif; ?>