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
EventbookingHelperJquery::validateForm();
JHtml::_('behavior.modal', 'a.eb-modal');
$popup = 'class="eb-modal" rel="{handler: \'iframe\', size: {x: 800, y: 500}}"';
if ($this->config->use_https) 
{
	$formUrl = JRoute::_('index.php?option=com_eventbooking&Itemid='.$this->Itemid, false, 1);
} 
else 
{
    $formUrl = JRoute::_('index.php?option=com_eventbooking&Itemid='.$this->Itemid, 0);
}
$selectedState = '';

$bootstrapHelper   = $this->bootstrapHelper;
$controlGroupClass = $bootstrapHelper->getClassMapping('control-group');
$inputPrependClass = $bootstrapHelper->getClassMapping('input-prepend');
$inputAppendClass  = $bootstrapHelper->getClassMapping('input-append');
$addOnClass        = $bootstrapHelper->getClassMapping('add-on');
$controlLabelClass = $bootstrapHelper->getClassMapping('control-label');
$controlsClass     = $bootstrapHelper->getClassMapping('controls');

?>
<div id="eb-cart-registration-page" class="eb-container row-fluid">
<h1 class="eb-page-heading"><?php echo JText::_('EB_CHECKOUT'); ?></h1>
<?php	 
	if (strlen(strip_tags($this->message->{'registration_form_message'.$this->fieldSuffix})))
	{
		$msg = $this->message->{'registration_form_message'.$this->fieldSuffix};
	}
	else 
	{
		$msg = $this->message->registration_form_message;
	}			
	if (strlen($msg)) 
	{
		$msg = str_replace('[EVENT_TITLE]', $this->eventTitle, $msg) ;
		$msg = str_replace('[AMOUNT]', EventBookingHelper::formatCurrency($this->amount, $this->config), $msg) ;			
	?>								
		<div class="eb-message"><?php echo $msg ; ?></div>							 															
	<?php	
	}
?>
<div class="eb-cart-content">		
	<table class="table table-striped table-bordered table-condensed">
		<thead>
			<tr>			
				<th class="col_event">
					<?php echo JText::_('EB_EVENT'); ?>
				</th>
				<?php
					if ($this->config->show_event_date) {
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
				<th class="col_subtotal">
					<?php echo JText::_('EB_SUB_TOTAL'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php			
			$total = 0 ;
			$k = 0 ;			
			for ($i = 0 , $n = count($this->items) ; $i < $n; $i++) 
			{
				$item = $this->items[$i] ;				
				$total += $item->quantity*$item->rate ;				
	        	$url = JRoute::_('index.php?option=com_eventbooking&view=event&id='.$item->id.'&tmpl=component&Itemid='.$this->Itemid);
			?>
				<tr>								
					<td class="col_event">
						<a href="<?php echo $url; ?>" <?php echo $popup; ?>><?php echo $item->title; ?></a>								
					</td>	
					<?php
						if ($this->config->show_event_date) 
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
                                        echo JHtml::_('date', $item->event_date, $this->config->event_date_format, null); 
                                    }
								?>								
							</td>
						<?php	
						}
					?>																				
					<td class="col_price">
						<?php echo EventbookingHelper::formatAmount($item->rate, $this->config); ?>
					</td>
					<td class="col_quantity">
						<?php echo $item->quantity ; ?>
					</td>	
					<td class="col_price">
						<?php echo EventbookingHelper::formatAmount($item->rate*$item->quantity, $this->config); ?>
					</td>					
				</tr>
			<?php				
				$k = 1 - $k ;				
			}				
			if ($this->config->show_event_date) 
			{
				$cols = 5 ;
			} 
			else 
			{
				$cols = 4 ;
			}										
			?>		
			<tr>				
				<td colspan="<?php echo $cols ; ?>" style="text-align: right;">										
					<input type="button" class="btn btn-inverse" value="<?php echo JText::_('EB_MODIFY_CART'); ?>" onclick="updateCart();" />																									
				</td>								
			</tr>		
		</tbody>		
	</table>				
</div>
<div class="clearfix"></div>	
<?php	
if (!$this->userId && $this->config->user_registration)
{
	$validateLoginForm = true;
	?>
	<form method="post" action="index.php" name="eb-login-form" id="eb-login-form" autocomplete="off" class="form form-horizontal">			
		<h3 class="eb-heading"><?php echo JText::_('EB_EXISTING_USER_LOGIN'); ?></h3>			
		<div class="<?php echo $controlGroupClass;  ?>">
			<label class="<?php echo $controlLabelClass; ?>" for="username">
				<?php echo  JText::_('EB_USERNAME') ?><span class="required">*</span>
			</label>
			<div class="<?php echo $controlsClass; ?>">      				
				<input type="text" name="username" id="username" class="input-large validate[required]" value=""/>		
			</div>	
		</div>		
		<div class="<?php echo $controlGroupClass;  ?>">
			<label class="<?php echo $controlLabelClass; ?>" for="password">
				<?php echo  JText::_('EB_PASSWORD') ?><span class="required">*</span>
			</label>
			<div class="<?php echo $controlsClass; ?>">      				
				<input type="password" id="password" name="password" class="input-large validate[required]" value="" />		
			</div>	
		</div>
		<div class="<?php echo $controlGroupClass;  ?>">    			
			<div class="<?php echo $controlsClass; ?>">      				
				<input type="submit" value="<?php echo JText::_('EB_LOGIN'); ?>" class="button btn btn-primary" />		
			</div>	
		</div>    								
		<h3 class="eb-heading"><?php echo JText::_('EB_NEW_USER_REGISTER'); ?></h3>
		<?php 
			if (JPluginHelper::isEnabled('system', 'remember'))
			{
			?>
				<input type="hidden" name="remember" value="1" />
			<?php	
			}
		?>		
		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="user.login" />
		<input type="hidden" name="return" value="<?php echo base64_encode(JFactory::getURI()->toString()); ?>" />
		<?php echo JHtml::_( 'form.token' ); ?>			
	</form>	
	<?php	
	}
	else 
	{
		$validateLoginForm = false;
	}
	?>		
	<form method="post" name="adminForm" id="adminForm" action="<?php echo $formUrl; ?>" autocomplete="off" class="form form-horizontal" enctype="multipart/form-data">
	<?php	 									
		if (!$this->userId && $this->config->user_registration) 
		{
			$params = JComponentHelper::getParams('com_users');
			$minimumLength = $params->get('minimum_length', 4);
			($minimumLength) ? $minSize = "minSize[4]" : $minSize = "";
		?>						
		<div class="<?php echo $controlGroupClass;  ?>">
			<label class="<?php echo $controlLabelClass; ?>" for="username1">
				<?php echo  JText::_('EB_USERNAME') ?><span class="required">*</span>
			</label>
			<div class="<?php echo $controlsClass; ?>">      				
				<input type="text" name="username" id="username1" class="input-large validate[required,ajax[ajaxUserCall],<?php echo $minSize;?>]" value="<?php echo JRequest::getVar('username'); ?>" />
				<span class="invalid" id="validate_username_msg" style="display: none;"><?php echo JText::_('EB_INVALID_USERNAME'); ?></span>		
			</div>	
		</div>					
		<div class="<?php echo $controlGroupClass;  ?>">			
			<label class="<?php echo $controlLabelClass; ?>" for="password1">					
				<?php echo  JText::_('EB_PASSWORD') ?><span class="required">*</span>						
			</label>				
			<div class="<?php echo $controlsClass; ?>">
				<input type="password" name="password1" id="password1" class="input-large validate[required,<?php echo $minSize;?>]" value=""/>					
			</div>
		</div>
		<div class="<?php echo $controlGroupClass;  ?>">			
			<label class="<?php echo $controlLabelClass; ?>" for="password2">					
				<?php echo  JText::_('EB_RETYPE_PASSWORD') ?><span class="required">*</span>					
			</label>				
			<div class="<?php echo $controlsClass; ?>">
				<input type="password" name="password2" id="password2" class="input-large validate[required,equals[password1]]" value="" />					
			</div>
		</div>											
		<?php	
		}		
		$fields = $this->form->getFields();
		if (isset($fields['state']))
		{
			$selectedState = $fields['state']->value;
		}
		foreach ($fields as $field)
		{
			echo $field->getControlGroup($bootstrapHelper);
		}
		if (($this->totalAmount > 0) || $this->form->containFeeFields()) 
		{
            $showPaymentInformation = true;
		?>
		<h3 class="eb-heading"><?php echo JText::_('EB_PAYMENT_INFORMATION'); ?></h3>
		<div class="<?php echo $controlGroupClass;  ?>">
			<label class="<?php echo $controlLabelClass; ?>">
				<?php echo JText::_('EB_AMOUNT'); ?>		
			</label>
			<div class="<?php echo $controlsClass; ?>">
				<?php 
					if ($this->config->currency_position == 0) 
					{
					?>
						<div class="<?php echo $inputPrependClass; ?> inline-display">
							<span class="<?php echo $addOnClass; ?>"><?php echo $this->config->currency_symbol;?></span>
							<input id="total_amount" type="text" readonly="readonly" class="input-small" value="<?php echo EventbookingHelper::formatAmount($this->totalAmount, $this->config); ?>" />
						</div>
					<?php		
					} 
					else 
					{
					?>
						<div class="<?php echo $inputAppendClass; ?> inline-display">										
							<input id="total_amount" type="text" readonly="readonly" class="input-small" value="<?php echo EventbookingHelper::formatAmount($this->totalAmount, $this->config); ?>" />
							<span class="<?php echo $addOnClass; ?>"><?php echo $this->config->currency_symbol;?></span>
						</div>
					<?php										
					}
				?>						
			</div>
		</div>			
		<?php	
		if ($this->enableCoupon || $this->discountAmount > 0)
		{
		?>
		<div class="<?php echo $controlGroupClass;  ?>">
			<label class="<?php echo $controlLabelClass; ?>">
				<?php echo JText::_('EB_DISCOUNT_AMOUNT'); ?>		
			</label>
			<div class="<?php echo $controlsClass; ?>">
				<?php 
					if ($this->config->currency_position == 0) 
					{
					?>
						<div class="<?php echo $inputPrependClass; ?> inline-display">
							<span class="<?php echo $addOnClass; ?>"><?php echo $this->config->currency_symbol;?></span>
							<input id="discount_amount" type="text" readonly="readonly" class="input-small" value="<?php echo EventbookingHelper::formatAmount($this->discountAmount, $this->config); ?>" />
						</div>
					<?php		
					} 
					else 
					{
					?>
						<div class="<?php echo $inputAppendClass; ?> inline-display">										
							<input id="discount_amount" type="text" readonly="readonly" class="input-small" value="<?php echo EventbookingHelper::formatAmount($this->discountAmount, $this->config); ?>" />
							<span class="<?php echo $addOnClass; ?>"><?php echo $this->config->currency_symbol;?></span>
						</div>
					<?php										
					}
				?>						
			</div>
		</div>	
		<?php	
		}

		if($this->lateFee > 0)
		{
		?>
			<div class="<?php echo $controlGroupClass;  ?>">
				<label class="<?php echo $controlLabelClass; ?>">
					<?php echo JText::_('EB_LATE_FEE'); ?>
				</label>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					if ($this->config->currency_position == 0)
					{
					?>
						<div class="<?php echo $inputPrependClass;  ?> inline-display">
							<span class="<?php echo $addOnClass;?>"><?php echo $this->config->currency_symbol;?></span>
							<input id="late_fee" type="text" readonly="readonly" class="input-small" value="<?php echo EventbookingHelper::formatAmount($this->lateFee, $this->config); ?>" />
						</div>
					<?php
					}
					else
					{
					?>
						<div class="<?php echo $inputAppendClass;  ?> inline-display">
							<input id="late_fee" type="text" readonly="readonly" class="input-small" value="<?php echo EventbookingHelper::formatAmount($this->lateFee, $this->config); ?>" />
							<span class="<?php echo $addOnClass;?>"><?php echo $this->config->currency_symbol;?></span>
						</div>
					<?php
					}
					?>
				</div>
			</div>
		<?php
		}
		if($this->taxAmount > 0)
		{
		?>
		<div class="<?php echo $controlGroupClass;  ?>">
			<label class="<?php echo $controlLabelClass; ?>">
				<?php echo JText::_('EB_TAX_AMOUNT'); ?>		
			</label>
			<div class="<?php echo $controlsClass; ?>">
				<?php 
					if ($this->config->currency_position == 0) 
					{
					?>
					<div class="<?php echo $inputPrependClass; ?> inline-display">
						<span class="<?php echo $addOnClass; ?>"><?php echo $this->config->currency_symbol;?></span>
						<input id="tax_amount" type="text" readonly="readonly" class="input-small" value="<?php echo EventbookingHelper::formatAmount($this->taxAmount, $this->config); ?>" />
					</div>
					<?php		
					} 
					else 
					{
					?>
					<div class="<?php echo $inputAppendClass; ?> inline-display">										
						<input id="tax_amount" type="text" readonly="readonly" class="input-small" value="<?php echo EventbookingHelper::formatAmount($this->taxAmount, $this->config); ?>" />
						<span class="<?php echo $addOnClass; ?>"><?php echo $this->config->currency_symbol;?></span>
					</div>
					<?php										
					}
				?>						
			</div>
		</div>	
		<?php	
		}
		if ($this->showPaymentFee)
		{
			?>
			<div class="<?php echo $controlGroupClass;  ?>">
				<label class="<?php echo $controlLabelClass; ?>">
					<?php echo JText::_('EB_PAYMENT_FEE'); ?>
				</label>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					if ($this->config->currency_position == 0)
					{
					?>
						<div class="<?php echo $inputPrependClass; ?>">
							<span class="<?php echo $addOnClass; ?>"><?php echo $this->config->currency_symbol;?></span>
							<input id="payment_processing_fee" type="text" readonly="readonly" class="input-small" value="<?php echo EventbookingHelper::formatAmount($this->paymentProcessingFee, $this->config); ?>" />
						</div>
					<?php
					}
					else
					{
					?>
						<div class="<?php echo $inputAppendClass; ?>">
							<input id="payment_processing_fee" type="text" readonly="readonly" class="input-small" value="<?php echo EventbookingHelper::formatAmount($this->paymentProcessingFee, $this->config); ?>" />
							<span class="<?php echo $addOnClass; ?>"><?php echo $this->config->currency_symbol;?></span>
						</div>
					<?php
					}
					?>
				</div>
			</div>
		<?php
		}

		if ($this->enableCoupon || $this->discountAmount > 0 || $this->taxAmount > 0 || $this->showPaymentFee)
		{
		?>
		<div class="<?php echo $controlGroupClass;  ?>">
			<label class="<?php echo $controlLabelClass; ?>">
				<?php echo JText::_('EB_GROSS_AMOUNT'); ?>		
			</label>
			<div class="<?php echo $controlsClass; ?>">
				<?php 
				if ($this->config->currency_position == 0) 
				{
				?>
					<div class="<?php echo $inputPrependClass; ?> inline-display">
						<span class="<?php echo $addOnClass; ?>"><?php echo $this->config->currency_symbol;?></span>
						<input id="amount" type="text" readonly="readonly" class="input-small" value="<?php echo EventbookingHelper::formatAmount($this->amount, $this->config); ?>" />
					</div>
				<?php		
				} 
				else 
				{
				?>
					<div class="<?php echo $inputAppendClass; ?> inline-display">										
						<input id="amount" type="text" readonly="readonly" class="input-small" value="<?php echo EventbookingHelper::formatAmount($this->amount, $this->config); ?>" />
						<span class="<?php echo $addOnClass; ?>"><?php echo $this->config->currency_symbol;?></span>
					</div>
				<?php										
				}
				?>						
			</div>
		</div>	
		<?php	
		}
		if ($this->depositPayment) 
		{
			if ($this->paymentType == 1)
			{
				$style = '';
			}
			else
			{
				$style = 'style = "display:none"';
			}
		?>
		<div id="deposit_amount_container" class="<?php echo $controlGroupClass;  ?>"<?php echo $style; ?>>
			<label class="<?php echo $controlLabelClass; ?>" for="payment_type">
				<?php echo JText::_('EB_DEPOSIT_AMOUNT') ;?>
			</label>
			<div class="<?php echo $controlsClass; ?>">
				<?php
				if ($this->config->currency_position == 0)
				{
					?>
					<div class="<?php echo $inputPrependClass; ?> inline-display">
						<span class="<?php echo $addOnClass; ?>"><?php echo $this->config->currency_symbol;?></span>
						<input id="deposit_amount" type="text" readonly="readonly" class="input-small" value="<?php echo EventbookingHelper::formatAmount($this->depositAmount, $this->config); ?>" />
					</div>
				<?php
				}
				else
				{
					?>
					<div class="<?php echo $inputAppendClass; ?> inline-display">
						<input id="deposit_amount" type="text" readonly="readonly" class="input-small" value="<?php echo EventbookingHelper::formatAmount($this->depositAmount, $this->config); ?>" />
						<span class="<?php echo $addOnClass; ?>"><?php echo $this->config->currency_symbol;?></span>
					</div>
				<?php
				}
				?>
			</div>
		</div>
		<div class="<?php echo $controlGroupClass;  ?>">
			<label class="<?php echo $controlLabelClass; ?>" for="payment_type">
				<?php echo JText::_('EB_PAYMENT_TYPE') ;?>				
			</label>
			<div class="<?php echo $controlsClass; ?>">      				
				<?php echo $this->lists['payment_type'] ;?>
			</div>	
		</div>			    									
		<?php    
		}	
		if ($this->enableCoupon)
		{
		?>
		<div class="<?php echo $controlGroupClass;  ?>">
			<label class="<?php echo $controlLabelClass; ?>" for="coupon_code"><?php echo  JText::_('EB_COUPON') ?></label>
			<div class="<?php echo $controlsClass; ?>">
				<input type="text" class="input-medium" name="coupon_code" id="coupon_code" value="<?php echo JRequest::getVar('coupon_code'); ?>" onchange="calculateCartRegistrationFee();" />
				<span class="invalid" id="coupon_validate_msg" style="display: none;"><?php echo JText::_('EB_INVALID_COUPON'); ?></span>	      				      		
			</div>	
		</div>				
		<?php	
		}		
		if (count($this->methods) > 1) 
		{
		?>
		<div class="<?php echo $controlGroupClass;  ?> payment_information" id="payment_method_container">
			<label class="<?php echo $controlLabelClass; ?>" for="payment_method">
				<?php echo JText::_('EB_PAYMENT_OPTION'); ?>
				<span class="required">*</span>				
			</label>
			<div class="<?php echo $controlsClass; ?>">      				
				<?php
					$method = null ;
					for ($i = 0 , $n = count($this->methods); $i < $n; $i++) 
					{
						$paymentMethod = $this->methods[$i];
						if ($paymentMethod->getName() == $this->paymentMethod) 
						{
							$checked = ' checked="checked" ';
							$method = $paymentMethod ;
						}										
						else
						{									 
							$checked = '';
						}		
					?>
						<label class="checkbox">
							<input onclick="changePaymentMethod('cart');" class="validate[required] radio" type="radio" name="payment_method" value="<?php echo $paymentMethod->getName(); ?>" <?php echo $checked; ?> /><?php echo JText::_($paymentMethod->getTitle()); ?>
						</label>
					<?php		
					}	
				?>
			</div>	
		</div>												
		<?php					
		} 
		else 
		{
			$method = $this->methods[0] ;
		?>				
		<div class="<?php echo $controlGroupClass;  ?> payment_information" id="payment_method_container">
			<label class="<?php echo $controlLabelClass; ?>">
				<?php echo JText::_('EB_PAYMENT_OPTION'); ?>				
			</label>
			<div class="<?php echo $controlsClass; ?>">      				
				<?php echo JText::_($method->getTitle()); ?>
			</div>	
		</div>														
		<?php	
		}																			
		if ($method->getCreditCard()) 
		{
			$style = '' ;	
		} 
		else 
		{
			$style = 'style = "display:none"';
		}			
		?>							
		<div class="<?php echo $controlGroupClass;  ?> payment_information" id="tr_card_number" <?php echo $style; ?>>
			<label class="<?php echo $controlLabelClass; ?>" for="x_card_num">
				<?php echo  JText::_('AUTH_CARD_NUMBER'); ?><span class="required">*</span>				
			</label>
			<div class="<?php echo $controlsClass; ?>">      				
				<input type="text" id="x_card_num" name="x_card_num" class="input-large validate[required,creditCard]" value="<?php echo JRequest::getVar('x_card_num'); ?>" />
			</div>	
		</div>								
		<div class="<?php echo $controlGroupClass;  ?> payment_information" id="tr_exp_date" <?php echo $style; ?>>
			<label class="<?php echo $controlLabelClass; ?>">
				<?php echo JText::_('AUTH_CARD_EXPIRY_DATE'); ?><span class="required">*</span>				
			</label>
			<div class="<?php echo $controlsClass; ?>">      				
				<?php echo $this->lists['exp_month'] .'  /  '.$this->lists['exp_year'] ; ?>
			</div>	
		</div>	    		
		<div class="<?php echo $controlGroupClass;  ?> payment_information" id="tr_cvv_code" <?php echo $style; ?>>
			<label class="<?php echo $controlLabelClass; ?>" for="x_card_code">
				<?php echo JText::_('AUTH_CVV_CODE'); ?><span class="required">*</span>				
			</label>
			<div class="<?php echo $controlsClass; ?>">      				
				<input type="text" id="x_card_code" name="x_card_code" class="input-large validate[required,custom[number]]" value="<?php echo JRequest::getVar('x_card_code'); ?>" />
			</div>	
		</div>								
		<?php
			if ($method->getCardType()) 
			{
				$style = '' ;
			} 
			else 
			{
				$style = ' style = "display:none;" ' ;										
			}															
		?>				
		<div class="<?php echo $controlGroupClass;  ?> payment_information" id="tr_card_type" <?php echo $style; ?>>
			<label class="<?php echo $controlLabelClass; ?>" for="card_type">
				<?php echo JText::_('EB_CARD_TYPE'); ?><span class="required">*</span>				
			</label>
			<div class="<?php echo $controlsClass; ?>">      				
				<?php echo $this->lists['card_type'] ; ?>
			</div>	
		</div>											
		<?php
			if ($method->getCardHolderName()) 
			{
				$style = '' ;
			} 
			else 
			{
				$style = ' style = "display:none;" ' ;										
			}
		?>				
		<div class="<?php echo $controlGroupClass;  ?> payment_information" id="tr_card_holder_name" <?php echo $style; ?>>
			<label class="<?php echo $controlLabelClass; ?>" for="card_holder_name">
				<?php echo JText::_('EB_CARD_HOLDER_NAME'); ?><span class="required">*</span>				
			</label>
			<div class="<?php echo $controlsClass; ?>">      				
				<input type="text" id="card_holder_name" name="card_holder_name" class="input-large validate[required]"  value="<?php echo JRequest::getVar('card_holder_name'); ?>" />
			</div>	
		</div>					
		<?php
			if ($method->getName() == 'os_ideal') 
			{
				$style = '' ;
			} 
			else 
			{
				$style = ' style = "display:none;" ' ;
			}					
		?>				
		<div class="<?php echo $controlGroupClass;  ?> payment_information" id="tr_bank_list" <?php echo $style; ?>>
			<label class="<?php echo $controlLabelClass; ?>" for="bank_id">
				<?php echo JText::_('EB_BANK_LIST'); ?><span class="required">*</span>				
			</label>
			<div class="<?php echo $controlsClass; ?>">      				
				<?php echo $this->lists['bank_id'] ; ?>
			</div>	
		</div>											
		<?php		
	}						
	if ($this->showCaptcha)
	{
	?>
	<div class="<?php echo $controlGroupClass;  ?>">
		<label class="<?php echo $controlLabelClass; ?>">
			<?php echo JText::_('EB_CAPTCHA'); ?><span class="required">*</span>
		</label>
		<div class="<?php echo $controlsClass; ?>">
			<?php echo $this->captcha; ?>	
		</div>
	</div>
	<?php
	}
	if ($this->config->accept_term ==1)
	{
		$articleId  = $this->config->article_id ;
		if (version_compare(JVERSION, '3.1', 'ge') && JLanguageMultilang::isEnabled())
		{
			$associations = JLanguageAssociations::getAssociations('com_content', '#__content', 'com_content.item', $articleId);
			$langCode     = JFactory::getLanguage()->getTag();
			if (isset($associations[$langCode]))
			{
				$article = $associations[$langCode];
			}
		}

		if (!isset($article))
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id, catid')
				->from('#__content')
				->where('id = ' . (int) $articleId);
			$db->setQuery($query);
			$article = $db->loadObject();
		}

		require_once JPATH_ROOT . '/components/com_content/helpers/route.php';
		EventbookingHelperJquery::colorbox('eb-colorbox-term');
		$termLink = ContentHelperRoute::getArticleRoute($article->id, $article->catid) . '&tmpl=component&format=html';
		?>
		<div class="<?php echo $controlGroupClass;  ?>">			
			<label class="checkbox">
				<input type="checkbox" name="accept_term" value="1" class="validate[required]" data-errormessage="<?php echo JText::_('EB_ACCEPT_TERMS');?>" />
				<?php echo JText::_('EB_ACCEPT'); ?>&nbsp;
				<?php
					echo "<a class=\"eb-colorbox-term\" title=\"".JText::_('EB_TERM_AND_CONDITION')."\" href=\"".JRoute::_($termLink)."\" rel=\"{handler: 'iframe', size: {x: 700, y: 500}}\">"."<strong>".JText::_('EB_TERM_AND_CONDITION')."</strong>"."</a>\n";
				?>
			</label>
		</div>
	<?php
	}            
	?>								
	<div class="form-actions">
		<input type="button" class="btn btn-default" name="btnBack" value="<?php echo  JText::_('EB_BACK') ;?>" onclick="window.history.go(-1);">
		<input type="submit" class="btn btn-primary" name="btn-submit" id="btn-submit" value="<?php echo JText::_('EB_PROCESS_REGISTRATION');?>">
		<img id="ajax-loading-animation" src="<?php echo JUri::base(true);?>/media/com_eventbooking/ajax-loadding-animation.gif" style="display: none;"/>
	</div>																					
	<?php
		if (count($this->methods) == 1) 
		{
		?>
		<input type="hidden" name="payment_method" value="<?php echo $this->methods[0]->getName(); ?>" />
		<?php	
		}		
	?>
	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />	
	<input type="hidden" name="option" value="com_eventbooking" />	
	<input type="hidden" name="task" value="cart.process_checkout" />
	<input type="hidden" name="show_payment_fee" value="<?php echo (int)$this->showPaymentFee ; ?>" />
		<script type="text/javascript">		
			var eb_current_page = 'cart';
			Eb.jQuery(function($){
				$(document).ready(function(){
					$("#adminForm").validationEngine('attach', { 
					    onValidationComplete: function(form, status){
					        if (status == true) {						        
					            form.on('submit', function(e) {
					                e.preventDefault();
					            });
					            return true;
					        }
					        return false;
					    }
					});
					buildStateField('state', 'country', '<?php echo $selectedState; ?>');
					if ($('#email').val())
					{
						$('#email').validationEngine('validate'); 
					}
                    <?php
                    if ($this->amount == 0 && !empty($showPaymentInformation))
                    {
                    //The event is free because of discount, so we need to hide payment information
                    ?>
                    $('.payment_information').css('display', 'none');
                    <?php
                    }
                    ?>
				})
			});		
			var siteUrl = "<?php echo EventbookingHelper::getSiteUrl(); ?>";			
			<?php
				echo os_payments::writeJavascriptObjects();					 		 
			?>				

			function updateCart(){
				location.href = '<?php echo JRoute::_(EventbookingHelperRoute::getViewRoute('cart', $this->Itemid)); ?>' ;
			}																
		</script>	
		<?php echo JHtml::_( 'form.token' ); ?>
	</form>					
</div>