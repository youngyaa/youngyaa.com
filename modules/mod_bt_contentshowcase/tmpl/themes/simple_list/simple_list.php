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

?>
<?php if(count($list)>0) :?>
<div class="simpleLayout">
	<div id="btcontentshowcase<?php echo $module->id; ?>"  class="bt-contentshowcase<?php echo $moduleclass_sfx ? ' bt-contentshowcase'.$params->get('moduleclass_sfx'):'';?>">	
		<?php 
			$class="";
			if( trim($params->get('content_title')) ){
			$class='class="borderWrap"';
		?>
		<h3>
			<?php if($params->get('content_title_link')) {?>
				<a href="<?php echo $params->get('content_title_link');?>"><span><?php echo $params->get('content_title') ?> </span></a>
			<?php } else { ?>
				<span><?php echo $params->get('content_title') ?> </span>                    
			<?php   }?>                
		</h3>
		<?php } ?>
		<div <?php echo $class;?>>
                    <?php foreach( $list as $i => $row ): ?>
                            
                    <div class="bt-row">
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
							<img <?php echo $imgClass ?> src="<?php echo $row->thumbnail; ?>" alt="<?php echo $row->title?>" title="<?php echo $row->title?>" />
						</a>
						</div>
                        <?php } ?>
						<?php endif ; ?>
						<?php if( $showAuthor || $showDate ): ?>
						<div class="bt-extra">
						<?php if( $showAuthor ): ?>
							<span class="bt-author"><?php 	echo JText::sprintf('BT_CREATEDBY' ,
							JHtml::_('link',JRoute::_($row->authorLink),$row->author)); ?>
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
                <?php endforeach; ?>              
		</div>
	</div>
	<!-- bt-container -->
</div>
<?php else : ?>
<div>No result...</div>
<?php endif; ?>
<div style="clear: both;"></div>