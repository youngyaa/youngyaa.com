<?php
/**
 * @package 	mod_bt_contentshowcase - BT ContentShowcase Module
 * @version		1.0
 * @created		Oct 2012
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



JHTML::_('behavior.tooltip');
$content_title = $params->get('content_title',JText::_('BT_BREAKINGNEW_TITLE'));
$bn_effect = $params->get('bn_effect');
$scrollAmount =$params->get('scroll_amount');
$slide_direction = $params->get('slide_direction');
$slide_direction = ($slide_direction == 'vertical') ? true : false;
$scroll_direction = $params->get('scroll_direction');
if($scroll_direction == 0 || $scroll_direction == 1)	
	$scrollStyle = 'scrollHorizontal';
else 
	$scrollStyle='scrollVertical';
	
if($scroll_direction == 0)	
	$scroll_direction = 'right';
else if($scroll_direction == 1)	
	$scroll_direction = 'left';
else if($scroll_direction == 2)	
	$scroll_direction = 'down';
else	
	$scroll_direction = 'up';
	
$document = JFactory::getDocument();
$document->addScript(JURI::root() . 'modules/mod_bt_contentshowcase/tmpl/themes/breakingnews/js/style.js');

?>
<?php if(count($list) > 0) :?>
<div id="btcontentshowcase<?php echo $module->id; ?>" class="breakingLayout-wrap <?php echo $moduleclass_sfx; ?>" style="width:<?php echo $moduleWidth; ?>">
		<div class="title-breakingnews">
				<?php if($params->get('content_title_link')): ?>
					<a href="<?php echo $params->get('content_title_link') ?>"><span><?php echo $params->get('content_title',JText::_('BT_BREAKINGNEW_TITLE')); ?></span></a>
				<?php else : ?>
					<span><?php echo $params->get('content_title',JText::_('BT_BREAKINGNEW_TITLE')); ?></span>
				<?php endif; ?>
		</div>	
	<?php if($bn_effect == 'scrollnews'): ?>
				<div class="breaking-content scrollShowcase scrollShowcase<?php echo $module->id; ?> <?php echo $scrollStyle; ?>">	
				<marquee scrollamount="<?php echo $scrollAmount; ?>" <?php if($params->get('pause_hover') == 1) echo 'onmouseover="this.stop();" onmouseout="this.start();"';?> loop="infinite" height="40" behavior="scroll" direction="<?php echo $scroll_direction; ?>" <?php if($scroll_direction == 'up'||$scroll_direction == 'down') echo "style='margin-left: 150px'"?>>
					<ul>
						<?php foreach($list as $i => $row): ?>
							<li>
								<?php 
									if( $row->thumbnail )	$title = '<img src="'. $row->thumbnail .'" />';
									else $title = $row->title;
										$title .= '::' . $row->description;
										$title = htmlspecialchars($title);
								?>							
								<?php if( $show_intro ): ?>
									<a class="hasTip" title="<?php echo $title; ?>" href="<?php echo $row->link; ?>"><span class="bn-title">
									<?php echo $row->title_cut; ?>
									</span></a>
								<?php else: ?>
									<a href="<?php echo $row->link; ?>"><span class="bn-title"><?php echo $row->title_cut; ?></span></a>
								<?php endif; ?>
								<?php if( $showDate ): ?>
									<span class="bn-date"> - <?php echo JText::sprintf('BT_CREATEDON', $row->date); ?></span>
								<?php endif; ?>
								<?php if( $showAuthor ): ?>
									<span class="bn-author"> - <?php echo JText::sprintf('BT_CREATEDBY' ,
										JHtml::_('link',JRoute::_($row->authorLink),$row->author)); ?>
									</span>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</marquee>
				</div>
	<?php else: ?>
		<div class='slideshow'>
			<ul>
				<?php foreach( $list as $i => $row ): ?>
					<li>
							<?php 
								if( $row->thumbnail )	$title = '<img src="'. $row->thumbnail .'" />';
								else	$title = $row->title_cut;
									$title .= '::' . $row->description;
									$title = htmlspecialchars($title);
							?>
							<?php if( $show_intro ): ?>
								<a class="hasTip" title="<?php echo $title; ?>" href="<?php echo $row->link; ?>"><span class="bn-title">
								<?php echo $row->title_cut; ?>
								</span></a>
							
							<?php else: ?>
								<a href="<?php echo $row->link; ?>"><span class="bn-title"></span><?php echo $row->title_cut; ?></a>
							<?php endif; ?>
							
							<?php if( $showDate ): ?>
								<span class="bn-date"> - <?php echo JText::sprintf('BT_CREATEDON', $row->date); ?></span>
							<?php endif; ?>
							
							<?php if( $showAuthor ): ?>
								<span class="bn-author"> - <?php echo JText::sprintf('BT_CREATEDBY' ,
									JHtml::_('link',JRoute::_($row->authorLink),$row->author)); ?>
								</span>
							<?php endif; ?>															
					</li>								
				<?php endforeach; ?>
			</ul>
		</div>
			<?php if($bn_effect == 'slidenews'){?>
			<div class='next'></div>
			<div class='prev' style="clear: both"></div>
			<?php }?>
	<?php endif; ?>
</div>
<?php else: ?>
	<div>No result ..</div>
<?php endif; ?>

<script type="text/javascript">
	$B(document).ready(function(){
		
		$B('#btcontentshowcase<?php echo $module->id; ?> .slideshow').bstyle(
				{
					 width				:'<?php echo $params->get('module_width')?>',
					 vertical 			:'<?php echo $slide_direction?>',
					 generateNextPrev	:<?php echo ($params->get('next_prev')) ? 'true': 'false' ?>,
					 slideSpeed			:<?php echo ($params->get('duration'))?>,
					 hoverPause			:<?php echo ($params->get('pause_hover'))?>,
					 autoPlay			:<?php echo $auto_start?>,
					 interval			:<?php echo ($params->get('interval',3)*1000)?>,
					 pause				:500 
				}
			);
});
</script>

<style>
.tip{
	max-width: <?php echo $thumbWidth+15; ?>px;
}
</style>