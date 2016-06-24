<?php 
/**
 * @version        	2.0.0
 * @package        	Joomla
 * @subpackage		Event Booking
 * @author  		Tuan Pham Ngoc
 * @copyright    	Copyright (C) 2010 - 2015 Ossolution Team
 * @license        	GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
	<thead>
		<tr>
			<th align="left" valign="top" width="10%">#</th>
			<th align="left" valign="top" width="60%"><?php echo JText::_('EB_ITEM_NAME'); ?></th>
			<th align="right" valign="top" width="20%"><?php echo JText::_('EB_PRICE'); ?></th>
			<th align="right" valign="top" width="10%"><?php echo JText::_('EB_SUB_TOTAL'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
			$i = 1;
			foreach($rowEvents as $rowEvent)
			{
			?>
				<tr>
					<td>
						<?php echo $i++; ?>
					</td>
					<td>
						<?php echo $rowEvent->title; ?>
					</td>
					<td align="right">
						<?php echo EventbookingHelper::formatCurrency($rowEvent->total_amount, $config); ?>
					</td>
					<td align="right">
						<?php echo EventbookingHelper::formatCurrency($rowEvent->total_amount, $config); ?>
					</td>
				</tr>
			<?php	
			}
		?>		
		<tr>
			<td colspan="3" align="right" valign="top" width="90%"><?php echo JText::_('EB_AMOUNT'); ?> :</td>
			<td align="right" valign="top" width="10%"><?php echo EventbookingHelper::formatCurrency($subTotal, $config);  ?></td>
		</tr>
		<tr>
			<td colspan="3" align="right" valign="top" width="90%"><?php echo JText::_('EB_DISCOUNT_AMOUNT'); ?> :</td>
			<td align="right" valign="top" width="10%"><?php echo EventbookingHelper::formatCurrency($discountAmount, $config); ?></td>
		</tr>		
		<tr>
			<td colspan="3" align="right" valign="top" width="90%"><?php echo JText::_('EB_TAX');?> :</td>
			<td align="right" valign="top" width="10%"><?php echo EventbookingHelper::formatCurrency($taxAmount, $config); ?></td>
		</tr>
		<tr>
			<td colspan="3" align="right" valign="top" width="90%"><?php echo JText::_('EB_GROSS_AMOUNT');?></td>
			<td align="right" valign="top" width="10%"><?php echo EventbookingHelper::formatCurrency($total, $config);?></td>
		</tr>
	</tbody>
</table>