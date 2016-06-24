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

$selectedState = '';
?>
<script type="text/javascript">
	Joomla.submitbutton = function(pressbutton) 
	{
		var form = document.adminForm;
		if (pressbutton == 'cancel') 
		{
			Joomla.submitform( pressbutton );
			return;				
		} 
		else 
		{
			Joomla.submitform( pressbutton );
		}
	}
</script>
<form action="index.php?option=com_eventbooking&view=registrant" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<div class="row-fluid">			
	<table class="admintable adminform">
		<tr>
			<td width="180" class="key">
				<?php echo  JText::_('EB_EVENT'); ?>
			</td>
			<td>
				<?php echo $this->lists['event_id']; ?>
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo  JText::_('EB_USER'); ?>
			</td>
			<td>
				<?php echo EventbookingHelper::getUserInput($this->item->user_id,'user_id',(int) $this->item->id) ; ?>
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo  JText::_('EB_NB_REGISTRANTS'); ?>
			</td>
			<td>
				<?php
					if ($this->item->number_registrants > 0) 
					{
						echo $this->item->number_registrants ;	
					} 
					else 
					{
					?>
						<input class="text_area" type="text" name="number_registrants" id="number_registrants" size="40" maxlength="250" value="" />						
						<small><?php echo JText::_('EB_NUMBER_REGISTRANTS_EXPLAIN'); ?></small>							
					<?php	
					}
				?>				
			</td>
		</tr>
		<?php 
			$fields = $this->form->getFields();
			if (isset($fields['state']))
			{
				$selectedState = $fields['state']->value;
			}
			foreach ($fields as $field)
			{
				$fieldType = strtolower($field->type);
				switch ($fieldType)
				{
					case 'heading':
					case 'message':
						break;
					default:
				?>
				<tr id="field_<?php echo $field->name; ?>">
					<td width="100" class="key">
						<?php echo $field->title; ?>
					</td>
					<td class="controls">
						<?php echo $field->input; ?>
					</td>
				</tr>	
				<?php							
				}	
			}
		?>
		<tr>
			<td width="100" class="key">
				<?php echo  JText::_('EB_REGISTRATION_DATE'); ?>
			</td>
			<td>
				<?php echo  JHtml::_('date', $this->item->register_date, $this->config->date_format, null);?>
			</td>
		</tr>		
		<tr>
			<td width="100" class="key">
				<?php echo  JText::_('EB_TOTAL_AMOUNT'); ?>
			</td>
			<td>
				<?php echo $this->config->currency_symbol?><input type="text" name="total_amount" class="input-medium" value="<?php echo $this->item->total_amount > 0 ? round($this->item->total_amount , 2) : null;?>" />				
			</td>
		</tr>	
		<?php
			if ($this->item->discount_amount > 0 || $this->item->tax_amount > 0 || $this->item->payment_processing_fee || $this->item->late_fee > 0)
			{
			    if ($this->item->discount_amount > 0) 
				{
			    ?>
			  	<tr>
    				<td width="100" class="key">
    					<?php echo  JText::_('EB_DISCOUNT_AMOUNT'); ?>
    				</td>
    				<td>
    					<?php echo $this->config->currency_symbol?><input type="text" name="discount_amount" class="input-medium" value="<?php echo $this->item->discount_amount > 0 ? round($this->item->discount_amount , 2) : null;?>" />    					
    				</td>
    			</tr>  	
			    <?php    
			    }
				if ($this->item->late_fee > 0)
				{
				?>
					<tr>
						<td width="100" class="key">
							<?php echo  JText::_('EB_LATE_FEE'); ?>
						</td>
						<td>
							<?php echo $this->config->currency_symbol?><input type="text" name="late_fee" class="input-medium" value="<?php echo $this->item->late_fee > 0 ? round($this->item->late_fee , 2) : null;?>" />
						</td>
					</tr>
				<?php
				}
				if ($this->item->tax_amount > 0)
			    {
			    ?>
    			    <tr>
        				<td width="100" class="key">
        					<?php echo  JText::_('EB_TAX'); ?>
        				</td>
        				<td>
        					<?php echo $this->config->currency_symbol?><input type="text" name="tax_amount" class="input-medium" value="<?php echo $this->item->tax_amount > 0 ? round($this->item->tax_amount , 2) : null;?>" />        				
        				</td>
        			</tr> 
    			<?php    
    			}

				if ($this->item->payment_processing_fee > 0)
				{
					?>
					<tr>
						<td width="100" class="key">
							<?php echo  JText::_('EB_PAYMENT_FEE'); ?>
						</td>
						<td>
							<?php echo $this->config->currency_symbol?><input type="text" name="payment_processing_fee" class="input-medium" value="<?php echo $this->item->payment_processing_fee > 0 ? round($this->item->payment_processing_fee , 2) : null;?>" />
						</td>
					</tr>
				<?php
				}
				?>
			<tr>
				<td width="100" class="key">
					<?php echo  JText::_('EB_GROSS_AMOUNT'); ?>
				</td>
				<td>
					<?php echo $this->config->currency_symbol?><input type="text" name="amount" class="input-medium" value="<?php echo $this->item->amount > 0 ? round($this->item->amount , 2) : null;?>" />					
				</td>
			</tr>					
			<?php			    
			}					
			if ($this->item->deposit_amount > 0) 
			{
			?>
				<tr>
					<td class="key">
						<?php echo JText::_('EB_DEPOSIT_AMOUNT'); ?>
					</td>
					<td>
						<?php echo $this->config->currency_symbol?><input type="text" name="deposit_amount" value="<?php echo $this->item->deposit_amount > 0 ? round($this->item->deposit_amount , 2) : null;?>" />						
					</td>
				</tr>
			<?php			   
    			if($this->item->payment_status == 0) 
				{
    			?>
    				<tr>
    					<td class="key">
    						<?php echo JText::_('EB_DUE_AMOUNT'); ?>
    					</td>
    					<td>
    						<?php
    							$dueAmount = $this->item->amount - $this->item->deposit_amount;
    							echo $this->config->currency_symbol?><input type="text" name="due_amount" class="input-medium" value="<?php echo $dueAmount > 0 ? round($dueAmount , 2) : null;?>" />
    					</td>
    				</tr>
    			<?php    			    			       
    			}
    			?>
    				<tr>
    					<td class="key">
    						<?php echo JText::_('EB_PAYMENT_STATUS'); ?>
    					</td>
    					<td>    						
    						<?php echo $this->lists['payment_status'];?>
    					</td>
    				</tr>
    			<?php			            
			}
			if ($this->item->payment_method == "os_offline_creditcard")
			{
				$params = new JRegistry($this->item->params);
			?>
				<tr>
					<td class="key">
						<?php echo JText::_('EB_FIRST_12_DIGITS_CREDITCARD_NUMBER'); ?>
					</td>
					<td>
						<?php echo $params->get('card_number'); ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_('AUTH_CARD_EXPIRY_DATE'); ?>
					</td>
					<td>
						<?php echo $params->get('exp_date'); ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_('AUTH_CVV_CODE'); ?>
					</td>
					<td>
						<?php echo $params->get('cvv'); ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_('EB_CARD_HOLDER_NAME'); ?>
					</td>
					<td>
						<?php echo $params->get('card_holder_name'); ?>
					</td>
				</tr>
			<?php
			}
		?>
		<tr>
			<td width="100" class="key">
				<?php echo  JText::_('EB_REGISTRATION_STATUS'); ?>
			</td>
			<td>
				<?php echo $this->lists['published'] ; ?>
			</td>
		</tr>
	</table>	
	
	<?php
	if ($this->config->collect_member_information && count($this->rowMembers)) 
	{
	?>		
	<table class="table">
	<?php			
		for ($i = 0 , $n = count($this->rowMembers) ; $i < $n ; $i++) 
		{
			$rowMember = $this->rowMembers[$i] ;			
			$memberId = $rowMember->id ;
			$form = new RADForm($this->memberFormFields);
			$memberData = EventBookingHelper::getRegistrantData($rowMember, $this->memberFormFields);
			$form->bind($memberData);	
			$form->setFieldSuffix($i+1);
			if ($i%2 == 0)
			{
				echo "<tr>\n";
			}					
			?>
				<td>
					<table class="admintable">
						<tr>
							<td colspan="2" class="key eb_row_heading"><?php echo JText::sprintf('EB_MEMBER_INFORMATION', $i + 1); ;?></td>
						</tr>		
						<?php
							$fields = $form->getFields();									
							foreach ($fields as $field)
							{
								if ($i > 0 && $field->row->only_show_for_first_member)
								{
									continue;
								}
								$fieldType = strtolower($field->type);
								switch ($fieldType)
								{
									case 'heading':
									case 'message':
										break;
									default:
									?>
									<tr>
										<td width="100" class="key">
											<?php echo $field->title; ?>
										</td>
										<td>
											<?php echo $field->input; ?>
										</td>
									</tr>	
								<?php							
								}	
							}
						?>											
					</table>
					<input type="hidden" name="ids[]" value="<?php echo $rowMember->id; ?>" />			
				</td>
			<?php	
			if (($i + 1) %2 == 0)
			{
				echo "</tr>" ;
			}							
		}
		if ($i %2 != 0)
		{
			echo "<td>&nbsp;</td></tr>\n" ;
		}	
	?>				
	</table>	
	<?php	
	}
	?>				
</div>		
<div class="clearfix"></div>	
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="task" value="" />			
	<?php echo JHtml::_( 'form.token' ); ?>
	<script type="text/javascript">
		var siteUrl = "<?php echo JUri::root(); ?>";
		(function($){
			buildStateField = (function(stateFieldId, countryFieldId, defaultState){
				if($('#' + stateFieldId).length && $('#' + stateFieldId).is('select'))
				{
					//set state
					if ($('#' + countryFieldId).length)
					{
						var countryName = $('#' + countryFieldId).val();
					}
					else 
					{
						var countryName = '';
					}			
					$.ajax({
						type: 'POST',
						url: siteUrl + 'index.php?option=com_eventbooking&task=get_states&country_name='+ countryName+'&field_name='+stateFieldId + '&state_name=' + defaultState,
						success: function(data) {
							$('#field_' + stateFieldId + ' .controls').html(data);
						},
						error: function(jqXHR, textStatus, errorThrown) {						
							alert(textStatus);
						}
					});			
					//Bind onchange event to the country 
					if ($('#' + countryFieldId).length)
					{
						$('#' + countryFieldId).change(function(){
							$.ajax({
								type: 'POST',
								url: siteUrl + 'index.php?option=com_eventbooking&task=get_states&country_name='+ $(this).val()+'&field_name=' + stateFieldId + '&state_name=' + defaultState,
								success: function(data) {
									$('#field_' + stateFieldId + ' .controls').html(data);
								},
								error: function(jqXHR, textStatus, errorThrown) {						
									alert(textStatus);
								}
							});
							
						});
					}						
				}//end check exits state
							
			});
			$(document).ready(function(){							
				buildStateField('state', 'country', '<?php echo $selectedState; ?>');										
			})
			populateRegisterData = (function(id, registerId, title){
				$.ajax({
					type : 'POST',
					url : 'index.php?option=com_eventbooking&task=get_profile_data&user_id=' + id + '&event_id=' +registerId,
					dataType: 'json',
					success : function(json){
						var selecteds = [];
						for (var field in json)
						{
							value = json[field];
							if ($("input[name='" + field + "[]']").length)
							{
								//This is a checkbox or multiple select
								if ($.isArray(value))
								{
									selecteds = value;
								}
								else
								{
									selecteds.push(value);
								}
								$("input[name='" + field + "[]']").val(selecteds);
							}
							else if ($("input[type='radio'][name='" + field + "']").length)
							{
								$("input[name="+field+"][value=" + value + "]").attr('checked', 'checked');
							}
							else
							{
								$('#' + field).val(value);
							}
						}
						$('#user_id').val(id);
						$('#user_id_name').val(title);
					}
				})
			});		
		})(jQuery);
	</script>
</form>