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

?>
<h1 class="eb_title"><?php echo JText::_('EB_LOCATIONS_MANAGEMENT'); ?>
	<span class="add_location_link" style="float: right; font-size:14px;"><a
			href="<?php echo JRoute::_('index.php?option=com_eventbooking&view=location&layout=form&Itemid=' . $this->Itemid); ?>"><i class="icon-plus"></i></i><?php echo JText::_('EB_SUBMIT_LOCATION'); ?></a></span>
</h1>
<form method="post" name="adminForm" id="adminForm"
      action="<?php echo JRoute::_('index.php?option=com_eventbooking&view=locations&Itemid=' . $this->Itemid);; ?>">
	<table class="table table-striped table-bordered table-condensed" style="margin-top: 10px;">
		<thead>
		<tr>
			<th>
				<?php echo JText::_('EB_NAME'); ?>
			</th>
			<th>
				<?php echo JText::_('EB_ADDRESS'); ?>
			</th>
			<th>
				<?php echo JText::_('EB_CITY'); ?>
			</th>
			<th>
				<?php echo JText::_('EB_STATE'); ?>
			</th>
			<th>
				<?php echo JText::_('EB_ZIP'); ?>
			</th>
			<th>
				<?php echo JText::_('EB_COUNTRY'); ?>
			</th>
			<th>
				<?php echo JText::_('EB_LATITUDE'); ?>
			</th>
			<th>
				<?php echo JText::_('EB_LONGITUDE'); ?>
			</th>
		</tr>
		</thead>
		<tbody>
		<?php
		$k = 0;
		for ($i = 0, $n = count($this->items); $i < $n; $i++)
		{
			$item = $this->items[$i];
			$url  = JRoute::_('index.php?option=com_eventbooking&view=location&layout=form&id=' . $item->id . '&Itemid=' . $this->Itemid);
			?>
			<tr>
				<td>
					<a href="<?php echo $url; ?>" title="<?php echo $item->name; ?>">
						<?php echo $item->name; ?>
					</a>
				</td>
				<td>
					<?php echo $item->address; ?>
				</td>
				<td>
					<?php echo $item->city; ?>
				</td>
				<td>
					<?php echo $item->state; ?>
				</td>
				<td>
					<?php echo $item->zip; ?>
				</td>
				<td>
					<?php echo $item->country; ?>
				</td>
				<td>
					<?php echo $item->lat; ?>
				</td>
				<td>
					<?php echo $item->long; ?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		if (count($this->items) == 0)
		{
			?>
			<tr>
				<td colspan="8" style="text-align: center;">
					<div class="info"><?php echo JText::_('EB_NO_LOCATION_RECORDS');?></div>
				</td>
			</tr>
		<?php
		}
		?>
		</tbody>
		<?php
		if ($this->pagination->total > $this->pagination->limit)
		{
			?>
			<tfoot>
			<tr>
				<td colspan="8">
					<div class="pagination">
						<?php echo $this->pagination->getListFooter(); ?>
					</div>
				</td>
			</tr>
			</tfoot>
		<?php
		}
		?>
	</table>
</form>