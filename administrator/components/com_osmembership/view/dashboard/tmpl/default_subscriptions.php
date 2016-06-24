<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright	Copyright (C) 2011 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined( '_JEXEC' ) or die;
?>
<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th class="title"><?php echo JText::_('OSM_FIRSTNAME')?></th>
			<th class="title"><?php echo JText::_('OSM_LASTNAME')?></th>
			<th class="title"><?php echo JText::_('OSM_PLAN')?></th>
			<th class="title center"><?php echo JText::_('OSM_CREATED_DATE')?></th>
		</tr>
		<?php
			foreach ($this->subscriptions as $row)
			{ 							
				?>
				<tr>
					<td><a href="<?php echo JRoute::_('index.php?option=com_osmembership&task=subscriber.edit&cid[]='.(int)$row->id); ?>"><?php echo $row->first_name; ?></a></td>
					<td><?php echo $row->last_name; ?></td>
					<td><a href="<?php echo JRoute::_('index.php?option=com_osmembership&task=plan.edit&cid[]='.(int)$row->plan_id); ?>"> <?php echo $row->plan_title; ?></a></td>
					<td class="center"><?php echo JHtml::_('date', $row->created_date, $this->config->date_format.' H:i:s'); ?></td>
				</tr>
				<?php
			} 
		?>
	</thead>
</table>