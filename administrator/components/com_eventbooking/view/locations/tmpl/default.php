<?php
/**
 * @version            2.0.4
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2015 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die;
if (!function_exists('curl_init')) 
{
	JFactory::getApplication()->enqueueMessage(JText::_('EB_CURL_NOT_INSTALLED'), 'warning');
}
?>
<form action="index.php?option=com_eventbooking&view=locations" method="post" name="adminForm" id="adminForm">
<table style="width:100%;">
<tr>
	<td align="left">
		<?php echo JText::_( 'Filter' ); ?>:
		<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->state->filter_search;?>" class="text_area search-query" onchange="document.adminForm.submit();" />		
		<button onclick="this.form.submit();" class="btn"><?php echo JText::_( 'Go' ); ?></button>
		<button onclick="document.getElementById('filter_search').value='';this.form.submit();" class="btn"><?php echo JText::_( 'Reset' ); ?></button>
	</td>
	<?php
		if (JLanguageMultilang::isEnabled())
		{
		?>
			<td style="text-align: right;">
				<?php echo $this->lists['filter_language'];?>
			</td>
		<?php
		}
	?>
</tr>
</table>
<div id="editcell">
	<table class="adminlist table table-striped">
	<thead>
		<tr>
			<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this)" />
			</th>
			<th class="text_left">
				<?php echo JHtml::_('grid.sort',  JText::_('EB_NAME'), 'tbl.name', $this->state->filter_order_Dir, $this->state->filter_order); ?>
			</th>
			<th class="text_left">
				<?php echo JHtml::_('grid.sort',  JText::_('EB_ADDRESS'), 'tbl.address', $this->state->filter_order_Dir, $this->state->filter_order); ?>
			</th>
			<th class="text_left">
				<?php echo JHtml::_('grid.sort',  JText::_('EB_CITY'), 'tbl.city', $this->state->filter_order_Dir, $this->state->filter_order); ?>
			</th>
			<th class="text_left">
				<?php echo JHtml::_('grid.sort',  JText::_('EB_STATE'), 'tbl.state', $this->state->filter_order_Dir, $this->state->filter_order); ?>
			</th>
			<th class="title text_left">
				<?php echo JHtml::_('grid.sort',  JText::_('EB_ZIP'), 'tbl.zip', $this->state->filter_order_Dir, $this->state->filter_order); ?>
			</th>
			<th class="title text_left">
				<?php echo JHtml::_('grid.sort',  JText::_('EB_COUNTRY'), 'tbl.country', $this->state->filter_order_Dir, $this->state->filter_order); ?>
			</th>
			<th class="title">
				<?php echo JHtml::_('grid.sort',  JText::_('EB_LATITUDE'), 'tbl.lat', $this->state->filter_order_Dir, $this->state->filter_order); ?>
			</th>
			<th class="title">
				<?php echo JHtml::_('grid.sort',  JText::_('EB_LONGITUDE'), 'tbl.long', $this->state->filter_order_Dir, $this->state->filter_order); ?>
			</th>
			<th class="title">
				<?php echo JHtml::_('grid.sort',  JText::_('EB_PUBLISHED'), 'tbl.published', $this->state->filter_order_Dir, $this->state->filter_order); ?>
			</th>								
			<th width="1%" nowrap="nowrap">
				<?php echo JHtml::_('grid.sort',  JText::_('EB_ID'), 'tbl.id', $this->state->filter_order_Dir, $this->state->filter_order); ?>
			</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="11">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
	<tbody>
	<?php
	$k = 0;	
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)
	{
		$row = $this->items[$i];
		$link 	= JRoute::_( 'index.php?option=com_eventbooking&view=location&id='. $row->id );
		$checked 	= JHtml::_('grid.id',   $i, $row->id );
		$published = JHtml::_('grid.published', $row, $i, 'tick.png', 'publish_x.png');
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php echo $checked; ?>
			</td>
			<td>
				<a href="<?php echo $link; ?>">
					<?php echo $row->name; ?>
				</a>
			</td>				
			<td>
				<?php echo $row->address ; ?>
			</td>						
			<td>
				<?php echo $row->city ; ?>
			</td>
			<td>
				<?php echo $row->state ; ?>
			</td>
			<td>
				<?php echo $row->zip ; ?>
			</td>
			<td>
				<?php echo $row->country ; ?>
			</td>
			<td class="center">
				<?php echo $row->lat ; ?>
			</td>
			<td class="center">
				<?php echo $row->long ; ?>
			</td>
			<td class="center">
				<?php echo $published ; ?>
			</td>			
			<td class="center">			
				<?php echo $row->id; ?>
			</td>
		</tr>
		<?php
		$k = 1 - $k;
	}
	?>
	</tbody>
	</table>
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->state->filter_order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->filter_order_Dir;?>" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>