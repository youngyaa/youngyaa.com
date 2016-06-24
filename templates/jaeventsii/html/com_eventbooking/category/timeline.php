<?php
/**
 * @version        	2.0.0
 * @package        	Joomla
 * @subpackage		Event Booking
 * @author  		Tuan Pham Ngoc
 * @copyright    	Copyright (C) 2010 - 2015 Ossolution Team
 * @license        	GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die ;
if ($this->config->use_https)
{
	$ssl = 1 ;
}
else
{
	$ssl = 0 ;
}
JHtml::_('behavior.modal', 'a.eb-modal');
?>
<div id="eb-category-page-timeline" class="eb-container">
	<form method="post" name="adminForm" id="adminForm" action="<?php echo JRoute::_('index.php?option=com_eventbooking&view=category&layout=timeline&id='.$this->category->id.'&Itemid='.$this->Itemid); ?>">
		<?php
		if ($this->category)
		{
			if ($this->params->get('page_heading'))
			{
				$pageHeading = $this->params->get('page_heading');
			}
			else
			{
				$pageHeading = $this->category->name;
			}
		?>
			<div id="eb-category">
				<h1 class="eb-page-heading"><?php echo $pageHeading;?></h1>
				<?php
				if($this->category->description != '')
				{
				?>
					<div class="eb-description"><?php echo $this->category->description;?></div>
				<?php
				}
				?>
			</div>
			<div class="clearfix"></div>
		<?php
		}

		if (count($this->categories))
		{
			echo EventbookingHelperHtml::loadCommonLayout('common/categories.php', array('categories' => $this->categories, 'categoryId' => $this->category->id, 'config' => $this->config, 'Itemid' => $this->Itemid));
		}
		if (count($this->items))
		{
			echo EventbookingHelperHtml::loadCommonLayout('common/events_timeline.php', array('events' => $this->items, 'config' => $this->config, 'Itemid' => $this->Itemid, 'nullDate' => $this->nullDate , 'ssl' => $ssl, 'viewLevels' => $this->viewLevels, 'category' => $this->category, 'Itemid' => $this->Itemid, 'bootstrapHelper' => $this->bootstrapHelper));
		}
		else
		{
			if (count($this->categories) == 0)
			{
			?>
				<p class="text-info"><?php echo JText::_('EB_NO_EVENTS') ?></p>
			<?php
			}
		}
		if ($this->pagination->total > $this->pagination->limit)
		{
			?>
			<div class="pagination">
				<?php echo $this->pagination->getPagesLinks(); ?>
			</div>
		<?php
		}
		?>
		<input type="hidden" name="category_id" value="<?php echo $this->category->id; ?>" />
		<input type="hidden" name="view" value="category" />
		<input type="hidden" name="Itemid" value="<?php echo $this->Itemid ; ?>" />
		<input type="hidden" name="option" value="com_eventbooking" />
		<input type="hidden" name="id" value="0" />
		<input type="hidden" name="task" value="" />
		<script type="text/javascript">
			function cancelRegistration(registrantId) {
				var form = document.adminForm ;
				if (confirm("<?php echo JText::_('EB_CANCEL_REGISTRATION_CONFIRM'); ?>")) {
					form.task.value = 'registrant.cancel' ;
					form.id.value = registrantId ;
					form.submit() ;
				}
			}
		</script>
	</form>
</div>

<script>
	
	(function($){
  jQuery(document).ready(function($) {
    $("#eb-categories .owl-carousel").owlCarousel({
      items: 2,
      itemsScaleUp : true,
      navigation : true,
      navigationText : ["prev", "next"],
      pagination: false,
      merge: false,
      mergeFit: true,
      slideBy: 2,
      autoplay: true
    });
  });
})(jQuery);
</script>