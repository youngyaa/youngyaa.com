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
defined( '_JEXEC' ) or die;
?>
<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th class="title"><?php echo JText::_('EB_FIRST_NAME')?></th>
			<th class="title"><?php echo JText::_('EB_LAST_NAME')?></th>
			<th class="title"><?php echo JText::_('EB_EVENT')?></th>
			<th class="title"><?php echo JText::_('EB_EMAIL')?></th>
			<th class="center title"><?php echo JText::_('EB_NUMBER_REGISTRANTS')?></th>
		</tr>
	</thead>
	<tbody>	
		<?php
			if (count($this->latestRegistrants))
			{
				foreach ($this->latestRegistrants as $row)
				{
				?>
					<tr>
						<td><a href="<?php echo JRoute::_('index.php?option=com_eventbooking&view=registrant&id='.(int)$row->id); ?>"><?php echo $row->first_name; ?></a></td>
						<td><?php echo $row->last_name; ?></td>
						<td><a href="<?php echo JRoute::_('index.php?option=com_eventbooking&view=event&id='.(int)$row->event_id); ?>"> <?php echo $row->title; ?></a></td>
						<td><?php echo $row->email; ?></td>
						<td class="center"><?php echo $row->number_registrants; ?></td>
					</tr>
				<?php
				}
			}
		?>
	</tbody>
</table>