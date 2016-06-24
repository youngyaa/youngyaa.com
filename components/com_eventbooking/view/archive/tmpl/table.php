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
//Load greybox lib
JHtml::_('behavior.modal', 'a.eb-modal');
if ($this->config->use_https)
{
	$ssl = 1;
}
else
{
	$ssl = 0;
}
?>
<div id="eb-event-archive-page-table" class="eb-container row-fluid">
<h1 class="eb-page-heading"><?php echo JText::_('EB_EVENTS_ARCHIVE'); ?></h1>
<?php
if ($this->config->show_cat_decription_in_calendar_layout && $this->category)
{
?>
	<div id="eb-category">
		<h2 class="eb-page-heading"><?php echo $this->category->name;?></h2>
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
if (count($this->items))
{
?>
	<table class="table table-striped table-bordered table-condensed">
	<thead>
	<tr>
		<?php
		if ($this->config->show_image_in_table_layout)
		{
		?>
			<th class="hidden-phone">
				<?php echo JText::_('EB_EVENT_IMAGE'); ?>
			</th>
		<?php
		}
		?>
		<th>
			<?php echo JText::_('EB_EVENT_TITLE'); ?>
		</th>
		<th class="date_col">
			<?php echo JText::_('EB_EVENT_DATE'); ?>
		</th>
		<?php
		if ($this->config->show_location_in_category_view)
		{
		?>
			<th class="location_col hidden-phone">
				<?php echo JText::_('EB_LOCATION'); ?>
			</th>
		<?php
		}
		if ($this->config->show_price_in_table_layout)
		{
		?>
			<th class="table_price_col hidden-phone">
				<?php echo JText::_('EB_INDIVIDUAL_PRICE'); ?>
			</th>
		<?php
		}
		if ($this->config->show_capacity)
		{
		?>
			<th class="capacity_col hidden-phone">
				<?php echo JText::_('EB_CAPACITY'); ?>
			</th>
		<?php
		}
		if ($this->config->show_registered)
		{
		?>
			<th class="registered_col hidden-phone">
				<?php echo JText::_('EB_REGISTERED'); ?>
			</th>
		<?php
		}
		?>
	</tr>
	</thead>
	<tbody>
	<?php
	for ($i = 0 , $n = count($this->items) ; $i < $n; $i++)
	{
		$item = $this->items[$i] ;
	?>
		<tr>
			<?php
			if ($this->config->show_image_in_table_layout)
			{
				?>
				<td class="eb-image-column hidden-phone">
					<?php
					if ($item->thumb)
					{
					?>
						<a href="<?php echo JUri::base(true).'/media/com_eventbooking/images/'.$item->thumb; ?>" class="eb-modal"><img src="<?php echo JUri::base(true).'/media/com_eventbooking/images/thumbs/'.$item->thumb; ?>" class="eb_thumb-left"/></a>
					<?php
					}
					else
					{
						echo ' ';
					}
					?>
				</td>
			<?php
			}
			?>
			<td>
				<a href="<?php echo JRoute::_(EventbookingHelperRoute::getEventRoute($item->id, $categoryId, $this->Itemid));?>" class="eb-event-link"><?php echo $item->title ; ?></a>
			</td>
			<td>
				<?php
				if ($item->event_date == EB_TBC_DATE)
				{
					echo JText::_('EB_TBC');
				}
				else
				{
					echo JHtml::_('date', $item->event_date, $this->config->event_date_format, null);
				}
				?>
			</td>
			<?php
			if ($this->config->show_location_in_category_view)
			{
				?>
				<td class="hidden-phone">
					<?php
					if ($item->location_address)
					{
					?>
						<a href="<?php echo JRoute::_('index.php?option=com_eventbooking&view=map&location_id='.$item->location_id.'&Itemid='.$this->Itemid); ?>" class="eb-colorbox-map"><?php echo $item->location_name ; ?></a>
					<?php
					}
					else
					{
						echo $item->location_name;
					}
					?>
				</td>
			<?php
			}
			if ($this->config->show_price_in_table_layout)
			{
				if ($this->config->show_discounted_price)
				{
					$price = $item->discounted_price ;
				}
				else
				{
					$price = $item->individual_price ;
				}
				?>
				<td class="hidden-phone">
					<?php echo EventbookingHelper::formatCurrency($price, $config, $item->currency_symbol); ?>
				</td>
			<?php
			}
			if ($this->config->show_capacity)
			{
			?>
				<td class="center hidden-phone">
					<?php
					if ($item->event_capacity)
					{
						echo $item->event_capacity ;
					}
					else
					{
						echo JText::_('EB_UNLIMITED') ;
					}
					?>
				</td>
			<?php
			}
			if ($this->config->show_registered)
			{
			?>
				<td class="center hidden-phone">
					<?php	echo $item->total_registrants ; ?>
				</td>
			<?php
			}
			?>
		</tr>
	<?php
	}
	?>
	</tbody>
	</table>
<?php
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
</div>