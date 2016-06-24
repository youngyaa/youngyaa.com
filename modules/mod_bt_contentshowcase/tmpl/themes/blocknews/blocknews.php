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
$currcat = 0;
$start = true;
?>

<?php if(count($list)>0) :?>
<div class="blocknews">
	<div id="btcontentshowcase<?php echo $module->id; ?>"  class="bt-contentshowcase<?php echo $moduleclass_sfx ? ' bt-contentshowcase'.$params->get('moduleclass_sfx'):'';?>">
		<?php if( trim($params->get('content_title')) ):?>
		<div class="bt-header">
			<h3>
				<?php if($params->get('content_title_link')):?>
					<a href="<?php echo $params->get('content_title_link');?>"><span><?php echo $params->get('content_title') ?> </span></a>
				<?php else: ?>
					<span><?php echo $params->get('content_title') ?> </span>                    
				<?php endif; ?>
			</h3>
		</div>
		<?php endif; ?>
		<div class="bt-blocks">
			<?php foreach( $list as $i => $row ): ?>	
			<?php if($currcat != $row->catid)
			{ 	
				
				$currcat = $row->catid;
				if(!$start){
					// end list,block
					echo '<p class="read-all"><a href="'.$link[$i-1]->categoryLink.'">'.JText::_('READ_ALL_NEWS').'</a></p>';
					echo '</div></div>';
				}
				$start = false;
				//start block
				echo '<div class="bt-block">';
			?>
				<div class="bt-category">
						<h3>
						<?php if($show_category_name_as_link) : ?>
							<a target="<?php echo $openTarget; ?>"
								title="<?php echo $row->category_title; ?>"
								href="<?php echo $row->categoryLink;?>"> <span><?php echo $row->category_title; ?></span>
							</a>
						<?php else :?>
							<span class="bt-category"> <?php echo $row->category_title; ?> </span>
						<?php endif; ?>
						</h3>
				</div>
				<div class="bt-img">
					<?php if($row->thumbnail): ?>	
					<a target="<?php echo $openTarget; ?>" href="<?php echo $row->link;?>">
						<img src="<?php echo $row->thumbnail; ?>" alt="<?php echo $row->title?>" />
					</a>
					<?php endif; ?>
					<div class="bt-title">
						<?php if( $showTitle ): ?>
							<a target="<?php echo $openTarget; ?>"	href="<?php echo $row->link;?>"> 
								<?php echo $row->title; ?>
							</a>
						<?php endif; ?>
						<div class="bt-title-bg"></div>
					</div>
				</div>
				<div class="bt-extra">
					<span class="bt-hits"><?php echo JText::sprintf('BT_HITS' , $row->hits); ?></span>
					<?php if( $showDate ): ?>
						<span class="bt-date"><?php echo JText::sprintf('BT_CREATEDON', $row->date); ?></span>
					<?php endif; ?>
					<?php if( $showAuthor ): ?>
						<span class="bt-author"><?php echo JText::sprintf('BT_CREATEDBY' ,JHtml::_('link',JRoute::_($row->authorLink),$row->author)); ?></span>
					<?php endif; ?>
				</div>
				<?php if( $show_intro ): ?>
				<div class="bt-introtext">
					<?php echo $row->description; ?>
					<?php if( $showReadmore ) : ?>
						<p class="bt-readmore">
							<a target="<?php echo $openTarget; ?>"
								title="<?php echo $row->title;?>"
								href="<?php echo $row->link;?>"> <?php echo JText::_('READ_MORE');?>
							</a>
						</p>
					<?php endif; ?>
				</div>
				<?php endif; ?>
			<?php 
				//start list
				echo '<div class="bt-list">';
			}else{  
				// show list item
				?>
				<div class="bt-item"><a target="<?php echo $openTarget; ?>" href="<?php echo $row->link;?>"><?php echo $row->title; ?></a></div>
			<?php }	?>
			<?php endforeach; ?>
			<?php 
				// end list,block
				echo '<p class="read-all"><a href="'.$list[count($list)-1]->categoryLink.'">'.JText::_('READ_ALL_NEWS').'</a></p>';
				echo '</div></div>';
			?>
		</div>
	</div>
</div>
<?php else : ?>
<div>No result...</div>
<?php endif; ?>
<div style="clear: both;"></div>
