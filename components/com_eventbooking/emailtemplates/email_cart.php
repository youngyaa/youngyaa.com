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
<style type="text/css">
<?php echo file_get_contents(JPATH_ROOT.'/media/com_eventbooking/assets/bootstrap/css/bootstrap.css') ; ?>
.price_col {
	width : 10%;
	text-align: right ;
}
.order_col {
	width : 13%;
	text-align: center ;
}
table.item_list {	
	margin-top: 10px;
}
table.doc_list {
	
}
.no_col {
	width: 5%;
}
.date_col {
	width: 20% ;
}
.capacity_col {
	width: 8%;
}
.registered_col {
	width: 8% ;
}
.list_first_name {
	width: 9% ;
}
.list_last_name {
	width: 9% ;
}
.list_event {
	
}
.list_event_date {
	width: 10% ;
}
.list_email {
	width: 10% ;
}
.list_registrant_number {
	width: 8% ;
}
.list_amount {
	text-align: right ;
	width: 6% ;
}
.list_id {
	text-align: center ;
	width: 0% ;
}
/**CSS for cart page**/
.col_no {
	width: 5% ;
}
.col_action {
	width : 10% ;
	text-align: center ;
}
.col_quantity {
	width : 12% ;
	text-align: center ;
}
.col_price {
	text-align: right ;
	width: 10% ;
}
.quantity_box {
	text-align: center ;
}
span.total_amount {
	font-weight: bold ;
}
.col_subtotal {
	text-align: right ;
}
.qty_title, .eb_rate {
	font-weight: bold ;	
} 
span.error {
	color : red ;
	font-size: 150% ;
}
.col_event_date {
	width: 17% ;
	text-align: center ;
}
span.view_list {
	font-weight: bold ;
}
.col_event {
	text-align: left ;
}
</style>

<table class="item_list table table-striped table-bordered table-condensed">
	<thead>
	<tr>		
		<th class="sectiontableheader col_event">
			<?php echo JText::_('EB_EVENT'); ?>
		</th>		
		<?php
			if ($config->show_event_date) 
			{
			?>
				<th class="col_event_date">
					<?php echo JText::_('EB_EVENT_DATE'); ?>
				</th>
			<?php		
			}
		?>
		<th class="col_price">
			<?php echo JText::_('EB_PRICE'); ?>
		</th>									
		<th class="col_quantity">
			<?php echo JText::_('EB_QUANTITY'); ?>
		</th>																
		<th class="col_quantity">
			<?php echo JText::_('EB_SUB_TOTAL'); ?>
		</th>
	</tr>
	</thead>
	<tbody>
	<?php
		$total = 0 ;
		$k = 0 ;					
		for ($i = 0 , $n = count($items) ; $i < $n; $i++) 
		{
			$item = $items[$i] ;			
			$rate = EventBookingHelper::getRegistrationRate($item->event_id, $item->number_registrants);
			$total += $item->number_registrants*$rate ;
            $url = JUri::getInstance()->toString(array('scheme', 'user', 'pass', 'host')).JRoute::_(EventbookingHelperRoute::getEventRoute($item->event_id, 0, $Itemid));
		?>
			<tr>								
				<td class="col_event">
					<a href="<?php echo $url; ?>"><?php echo $item->title; ?></a>								
				</td>				
				<?php
					if ($config->show_event_date) 
					{
					?>
						<td class="col_event_date">
							<?php 
							    if ($item->event_date == EB_TBC_DATE) 
								{
							        echo JText::_('EB_TBC');
							    } 
							    else 
								{
							        echo JHTML::_('date', $item->event_date,  $config->event_date_format, null);
							    }    
							?>							
						</td>	
					<?php	
					}
				?>
				<td class="col_price">
					<?php echo EventbookingHelper::formatAmount($rate, $config); ?>
				</td>
				<td class="col_quantity">
					<?php echo $item->number_registrants ; ?>
				</td>																										
				<td class="col_price">
					<?php echo EventbookingHelper::formatAmount($rate*$item->number_registrants, $config); ?>
				</td>						
			</tr>
		<?php				
			$k = 1 - $k ;				
		}
	?>			
	</tbody>					
</table>	
<table width="100%" class="os_table" cellspacing="2" cellpadding="2">	
<?php				
	$fields = $form->getFields();
	foreach ($fields as $field)
	{
		if ($field->hideOnDisplay)
		{
			continue;
		}
		echo $field->getOutput(false);						
	}
	if ($totalAmount > 0)
	{
	?>
	<tr>
		<td class="title_cell">
			<?php echo JText::_('EB_AMOUNT'); ?>
		</td>
		<td class="field_cell">
			<?php echo EventbookingHelper::formatCurrency($totalAmount, $config); ?>
		</td>
	</tr>
	<?php	
		if ($discountAmount > 0)
		{
		?>
			<tr>
				<td class="title_cell">
					<?php echo  JText::_('EB_DISCOUNT_AMOUNT'); ?>
				</td>
				<td class="field_cell">
					<?php echo EventbookingHelper::formatCurrency($discountAmount, $config); ?>
				</td>
			</tr>
		<?php
		}

		if ($lateFee > 0)
		{
		?>
			<tr>
				<td class="title_cell">
					<?php echo  JText::_('EB_LATE_FEE'); ?>
				</td>
				<td class="field_cell">
					<?php echo EventbookingHelper::formatCurrency($lateFee, $config); ?>
				</td>
			</tr>
		<?php
		}

		if ($taxAmount > 0)
		{
		?>
			<tr>
				<td class="title_cell">
					<?php echo  JText::_('EB_TAX'); ?>
				</td>
				<td class="field_cell">
					<?php echo EventbookingHelper::formatCurrency($taxAmount, $config); ?>
				</td>
			</tr>
		<?php
		}

		if ($paymentProcessingFee > 0)
		{
		?>
			<tr>
				<td class="title_cell">
					<?php echo  JText::_('EB_PAYMENT_FEE'); ?>
				</td>
				<td class="field_cell">
					<?php echo EventbookingHelper::formatCurrency($paymentProcessingFee, $config); ?>
				</td>
			</tr>
		<?php
		}
		if ($discountAmount > 0 || $taxAmount > 0 || $paymentProcessingFee > 0)
		{
		?>                
			<tr>
				<td class="title_cell">
					<?php echo  JText::_('EB_GROSS_AMOUNT'); ?>
				</td>
				<td class="field_cell">
					<?php echo EventbookingHelper::formatCurrency($amount, $config);?>
				</td>
			</tr>
		<?php
		}            
	}
	if ($depositAmount > 0)
	{
	?>
	<tr>
		<td class="title_cell">
			<?php echo JText::_('EB_DEPOSIT_AMOUNT'); ?>
		</td>
		<td class="field_cell">
			<?php echo EventbookingHelper::formatCurrency($depositAmount, $config); ?>
		</td>
	</tr>
	<tr>
		<td class="title_cell">
			<?php echo JText::_('EB_DUE_AMOUNT'); ?>
		</td>
		<td class="field_cell">
			<?php echo EventbookingHelper::formatCurrency($amount - $depositAmount, $config); ?>
		</td>
	</tr>
	<?php
	}
	if ($amount > 0)
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