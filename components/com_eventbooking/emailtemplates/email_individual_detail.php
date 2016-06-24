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
<table width="100%" class="os_table" cellspacing="2" cellpadding="2">				
	<tr>			
		<td class="title_cell">
			<?php echo  JText::_('EB_EVENT_TITLE') ?>
		</td>
		<td class="field_cell">
			<?php echo $rowEvent->title ; ?>
		</td>
	</tr>
	<?php
		if ($config->show_event_date) 
		{
		?>
		<tr>			
			<td class="title_cell">
				<?php echo  JText::_('EB_EVENT_DATE') ?>
			</td>
			<td class="field_cell">
				<?php 
				    if ($rowEvent->event_date == EB_TBC_DATE)
                    {
				        echo JText::_('EB_TBC');
				    }
                    else
                    {
				        echo JHTML::_('date', $rowEvent->event_date, $config->event_date_format, null) ;
				    }
				?>				
			</td>
		</tr>	
		<?php	
	}
	if ($config->show_event_location_in_email && $rowLocation) 
	{
		$location = $rowLocation ;
		$locationInformation = array();
		if ($location->address)
		{
			$locationInformation[] = $location->address;
		}
		if ($location->city)
		{
			$locationInformation[] = $location->city;
		}
		if ($location->state)
		{
			$locationInformation[] = $location->state;
		}
		if ($location->zip)
		{
			$locationInformation[] = $location->zip;
		}
		if ($location->country)
		{
			$locationInformation[] = $location->country;
		}
	?>
		<tr>			
			<td class="title_cell">
				<?php echo  JText::_('EB_LOCATION') ?>
			</td>
			<td class="field_cell">				
				<?php echo $location->name.' ('.implode(', ', $locationInformation).')' ; ?>
			</td>
		</tr>
	<?php	
	}
	$fields = $form->getFields();
	foreach ($fields as $field)
	{
		if ($field->hideOnDisplay)
		{
			continue;
		}
		echo $field->getOutput(false);						
	}
	if ($row->total_amount > 0)
	{
	?>
	<tr>
		<td class="title_cell">
			<?php echo JText::_('EB_AMOUNT'); ?>
		</td>
		<td class="field_cell">
			<?php echo EventbookingHelper::formatCurrency($row->total_amount, $config, $rowEvent->currency_symbol); ?>
		</td>
	</tr>
	<?php	
		if ($row->discount_amount > 0)
		{
		?>
			<tr>
				<td class="title_cell">
					<?php echo  JText::_('EB_DISCOUNT_AMOUNT'); ?>
				</td>
				<td class="field_cell">
					<?php echo EventbookingHelper::formatCurrency($row->discount_amount, $config, $rowEvent->currency_symbol); ?>
				</td>
			</tr>
		<?php
		}
		if ($row->late_fee > 0)
		{
		?>
			<tr>
				<td class="title_cell">
					<?php echo  JText::_('EB_LATE_FEE'); ?>
				</td>
				<td class="field_cell">
					<?php echo EventbookingHelper::formatCurrency($row->late_fee, $config, $rowEvent->currency_symbol); ?>
				</td>
			</tr>
		<?php
		}
		if ($row->tax_amount > 0)
		{
		?>
			<tr>
				<td class="title_cell">
					<?php echo  JText::_('EB_TAX'); ?>
				</td>
				<td class="field_cell">
					<?php echo EventbookingHelper::formatCurrency($row->tax_amount, $config, $rowEvent->currency_symbol); ?>
				</td>
			</tr>
		<?php
		}
		if ($row->payment_processing_fee > 0)
		{
		?>
			<tr>
				<td class="title_cell">
					<?php echo  JText::_('EB_PAYMENT_FEE'); ?>
				</td>
				<td class="field_cell">
					<?php echo EventbookingHelper::formatCurrency($row->payment_processing_fee, $config, $rowEvent->currency_symbol); ?>
				</td>
			</tr>
		<?php
		}
		if ($row->discount_amount > 0 || $row->tax_amount > 0 || $row->payment_processing_fee > 0 || $row->late_fee > 0)
		{
		?>                
			<tr>
				<td class="title_cell">
					<?php echo  JText::_('EB_GROSS_AMOUNT'); ?>
				</td>
				<td class="field_cell">
					<?php echo EventbookingHelper::formatCurrency($row->amount, $config, $rowEvent->currency_symbol) ; ?>
				</td>
			</tr>
		<?php
		}            
	}
	if ($row->deposit_amount > 0)
	{
	?>
	<tr>
		<td class="title_cell">
			<?php echo JText::_('EB_DEPOSIT_AMOUNT'); ?>
		</td>
		<td class="field_cell">
			<?php echo EventbookingHelper::formatCurrency($row->deposit_amount, $config, $rowEvent->currency_symbol); ?>
		</td>
	</tr>
	<tr>
		<td class="title_cell">
			<?php echo JText::_('EB_DUE_AMOUNT'); ?>
		</td>
		<td class="field_cell">
			<?php echo EventbookingHelper::formatCurrency($row->amount - $row->deposit_amount, $config, $rowEvent->currency_symbol); ?>
		</td>
	</tr>
	<?php
	}
	if ($row->amount > 0)
	{
	?>
	<tr>
		<td class="title_cell">
			<?php echo  JText::_('EB_PAYMEMNT_METHOD'); ?>
		</td>
		<td class="field_cell">
		<?php
			$method = os_payments::loadPaymentMethod($row->payment_method);
			if ($method)
			{
				echo JText::_($method->title) ;
			}
		?>
		</td>
	</tr>
	<?php
		if (!empty($last4Digits))
		{
		?>
			<tr>
				<td class="title_cell">
					<?php echo JText::_('EB_LAST_4DIGITS'); ?>
				</td>
				<td class="field_cell">
					<?php echo $last4Digits; ?>
				</td>
			</tr>
		<?php
		}
	?>
	<tr>
		<td class="title_cell">
			<?php echo JText::_('EB_TRANSACTION_ID'); ?>
		</td>
		<td class="field_cell">
			<?php echo $row->transaction_id ; ?>
		</td>
	</tr>
	<?php
	}       	
?>										
</table>	