<?php
/**
 * @version		1.6.2
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die ;

$ordering = ($this->state->filter_order == 'a.ordering');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');
?>
<form action="index.php?option=com_osmembership&view=rules" method="post" name="adminForm" id="adminForm">
<table width="100%">
<tr>
	<td align="left">
		<?php echo JText::_( 'OSM_FILTER' ); ?>:
		<input type="text" name="search" id="search" value="<?php echo $this->state->search;?>" class="text_area search-query" onchange="document.adminForm.submit();" />		
		<button onclick="this.form.submit();" class="btn"><?php echo JText::_( 'OSM_GO' ); ?></button>
		<button onclick="document.getElementById('search').value='';this.form.submit();" class="btn"><?php echo JText::_( 'OSM_RESET' ); ?></button>		
	</td>
	<td align="right" style="text-align: right;">
		<?php echo $this->lists['from_plan_id']; ?>
		<?php echo $this->lists['to_plan_id']; ?>
		<?php echo $this->lists['filter_state']; ?>
	</td>	
</tr>
</table>
<div id="editcell">
	<table class="adminlist table table-striped">
	<thead>
		<tr>
			<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
			</th>
			<th class="title" style="text-align: left;">
				<?php echo JHtml::_('grid.sort',  JText::_('OSM_FROM_PLAN'), 'b.title', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
			</th>											
			<th class="title" style="text-align: left;">
				<?php echo JHtml::_('grid.sort',  JText::_('OSM_TO_PLAN'), 'c.title', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
			</th>
			<th class="title" style="text-align: left;">
				<?php echo JHtml::_('grid.sort',  JText::_('OSM_PRICE'), 'a.price', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
			</th>							
			<th class="title" style="text-align: left;">
				<?php echo JHtml::_('grid.sort',  JText::_('OSM_MAX_PRESENCE'), 'a.max_presence', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
			</th>
			<th class="title" style="text-align: left;">
				<?php echo JHtml::_('grid.sort',  JText::_('OSM_MIN_PRESENCE'), 'a.min_presence', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
			</th>
			<th width="5%">
				<?php echo JHtml::_('grid.sort',  JText::_('OSM_PUBLISHED'), 'a.published', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
			</th>
			<th width="2%">
				<?php echo JHtml::_('grid.sort',  JText::_('OSM_ID'), 'a.id', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
			</th>													
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="8">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
	<tbody>
	<?php
	$k = 0;	
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)
	{
		$row = &$this->items[$i];
		$link 	= JRoute::_( 'index.php?option=com_osmembership&task=rule.edit&cid[]='. $row->id);
		$checked 	= JHtml::_('grid.id',   $i, $row->id );				
		$published 	= JHtml::_('grid.published', $row, $i, 'tick.png', 'publish_x.png', 'rule.' );			
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php echo $checked; ?>
			</td>	
			<td>																			
				<a href="<?php echo $link; ?>"><?php echo $row->from_plan_title ; ?></a>				
			</td>
			<td>
				<?php echo $row->to_plan_title; ?>
			</td>
			<td>
				<?php echo number_format($row->price, 2) ; ?>
			</td>		
			<td>
				<?php echo $row->max_presence ; ?>
			</td>
			<td>
				<?php echo $row->min_presence ; ?>
			</td>
			<td class="text_center">
				<?php echo $published; ?>
			</td>
			<td class="text_center">
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
	<input type="hidden" name="option" value="com_osmembership" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->state->filter_order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->filter_order_Dir; ?>" />
	<?php echo JHtml::_( 'form.token' ); ?>			
</form>