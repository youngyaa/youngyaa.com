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
JHtml::_('behavior.modal', 'a.eb-modal');
?>
<div id="eb-category-page-table" class="eb-container">
<?php
if ($this->config->show_cat_decription_in_calendar_layout && $this->category)
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
} else
{
?>
	<h1 class="eb-page-heading"><?php echo $this->params->get('page_heading')? $this->params->get('page_heading') : JText::_('EB_EVENT_LIST') ; ?></h1>
	<p class="eb-message">
		<?php echo JText::_('EB_EVENT_GUIDE') ; ?>
	</p>
<?php
}
if ($this->config->use_https)
	$ssl = 1 ;
else
	$ssl = 0 ;

if (count($this->categories))
{
	echo EventbookingHelperHtml::loadCommonLayout('common/categories.php', array('categories' => $this->categories, 'categoryId' => $this->category->id, 'config' => $this->config, 'Itemid' => $this->Itemid));
}
?>
<form method="post" name="adminForm" id="adminForm" action="index.php">
	<?php
		if (count($this->items))
		{
			echo EventbookingHelperHtml::loadCommonLayout('common/events_table.php', array('items' => $this->items, 'config' => $this->config, 'Itemid' => $this->Itemid, 'nullDate' => $this->nullDate, 'ssl' => $ssl, 'viewLevels' => $this->viewLevels, 'categoryId' => $this->category->id, 'bootstrapHelper' => $this->bootstrapHelper));
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
				<?php echo $this->pagination->getPagesLinks();?>
			</div>
		<?php
		}
	?>
	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
	<input type="hidden" name="option" value="com_eventbooking" />
	<input type="hidden" name="view" value="category" />
	<input type="hidden" name="layout" value="table" />
	<input type="hidden" name="category_id" value="<?php echo $this->category->id; ?>" />
</form>
</div>