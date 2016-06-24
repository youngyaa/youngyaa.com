<?php
/**
 * @version            2.0.3
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2015 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die;
?>
<div id="eb-categories-page" class="eb-container">
	<?php
		if (!$this->categoryId)
		{
			$pageHeading = $this->params->get('page_heading') ? $this->params->get('page_heading') : JText::_('EB_CATEGORIES');
		?>
			<h1 class="eb-page-heading"><?php echo $pageHeading;?></h1>
		<?php
		}
		else
		{
		?>
			<div id="eb-category">
				<h1 class="eb-page-heading"><?php echo $this->category->name;?></h1>
				<?php
					if($this->category->description != '')
					{
					?>
						<div class="eb-description"><?php echo $this->category->description;?></div>
					<?php
					}
				?>
			</div>
		<?php
		}
		echo EventbookingHelperHtml::loadCommonLayout('common/categories.php', array('categories' => $this->items, 'categoryId' => $this->categoryId, 'config' => $this->config, 'Itemid' => $this->Itemid));
		if ($this->pagination->total > $this->pagination->limit)
		{
		?>
			<div class="pagination">
				<?php echo $this->pagination->getPagesLinks(); ?>
			</div>
		<?php
		}
	?>
</div>

<script>
	
	(function($){
  jQuery(document).ready(function($) {
    $("#eb-categories .owl-carousel").owlCarousel({
      items: 3,
      itemsScaleUp : true,
      navigation : true,
      navigationText : ["prev", "next"],
      pagination: false,
      merge: false,
      mergeFit: true,
      slideBy: 3,
      autoplay: true
    });
  });
})(jQuery);
</script>