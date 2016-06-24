/****
 * Payme method class
 * @param id
 * @param name
 * @param title
 * @param creditCard
 * @param cardType
 * @param cardCvv
 * @param cardHolderName
 * @return
 */
function PaymentMethod(name, creditCard, cardType, cardCvv, cardHolderName) {	
	this.name = name ;	
	this.creditCard = creditCard ;
	this.cardType = cardType ;
	this.cardCvv = cardCvv ;
	this.cardHolderName = cardHolderName ;
}
/***
 * Get name of the payment method
 * @return string
 */
PaymentMethod.prototype.getName = function() {
	return this.name ;
}
/***
 * This is creditcard payment method or not	
 * @return int
 */
PaymentMethod.prototype.getCreditCard = function() {
	return this.creditCard ;
}
/****
 * Show creditcard type or not
 * @return string
 */
PaymentMethod.prototype.getCardType = function() {
	return this.cardType ;
}
/***
 * Check to see whether card cvv code is required
 * @return string
 */
PaymentMethod.prototype.getCardCvv = function() {
	return this.cardCvv ;
}
/***
 * Check to see whether this payment method require entering card holder name
 * @return
 */
PaymentMethod.prototype.getCardHolderName = function() {
	return this.cardHolderName ;
}
/***
 * Payment method class, hold all the payment methods
 */
function PaymentMethods() {
	this.length = 0 ;
	this.methods = new Array();
}
/***
 * Add a payment method to array
 * @param paymentMethod
 * @return
 */
 PaymentMethods.prototype.Add = function(paymentMethod) {	
	this.methods[this.length] = paymentMethod ;
	this.length = this.length + 1 ;
}
/***
 * Find a payment method based on it's name
 * @param name
 * @return {@link PaymentMethod}
 */
 PaymentMethods.prototype.Find = function(name) {
	for (var i = 0 ; i < this.length ; i++) {
		if (this.methods[i].name == name) {
			return this.methods[i] ;			
		}
	}
	return null ;
}
/***
 * Process event when someone change a payment method
 */ 

function updatePaymentMethod()
{
    Eb.jQuery(function($) {
        if($('input:radio[name^=payment_method]').length)
        {
            var paymentMethod = $('input:radio[name^=payment_method]:checked').val();
        }
        else
        {
            var paymentMethod = $('input[name^=payment_method]').val();
        }
        method = methods.Find(paymentMethod);
        if (!method)
        {
            return;
        }
        if (method.getCreditCard())
        {
            $('#tr_card_number').show();
            $('#tr_exp_date').show();
            $('#tr_cvv_code').show();
            if (method.getCardType())
            {
                $('#tr_card_type').show();
            }
            else
            {
                $('#tr_card_type').hide();
            }
            if (method.getCardHolderName())
            {
                $('#tr_card_holder_name').show();
            }
            else
            {
                $('#tr_card_holder_name').hide();
            }
        }
        else
        {
            $('#tr_card_number').hide();
            $('#tr_exp_date').hide();
            $('#tr_cvv_code').hide();
            $('#tr_card_type').hide();
            $('#tr_card_holder_name').hide();
        }
        if (paymentMethod == 'os_ideal')
        {
            $('#tr_bank_list').show();
        }
        else
        {
            $('#tr_bank_list').hide();
        }
    });
}

function changePaymentMethod(registrationType)
{
    updatePaymentMethod();
    if (document.adminForm.show_payment_fee.value == 1)
    {
        // Re-calculate subscription fee in case there is payment fee associated with payment method
        if (registrationType == 'individual')
        {
            calculateIndividualRegistrationFee();
        }
        else if (registrationType == 'group')
        {
            calculateGroupRegistrationFee();
        }
        else
        {
            calculateCartRegistrationFee();
        }
    }
}

function calculateIndividualRegistrationFee() 
{
	Eb.jQuery(function($) {						
		$('#btn-submit').attr('disabled', 'disabled');
		$('#ajax-loading-animation').show();
        if($('input:radio[name^=payment_method]').length)
        {
            var paymentMethod = $('input:radio[name^=payment_method]:checked').val();
        }
        else
        {
            var paymentMethod = $('input[name^=payment_method]').val();
        }
		$.ajax({
			type: 'POST',
			url: siteUrl + 'index.php?option=com_eventbooking&task=register.calculate_individual_registration_fee&payment_method=' + paymentMethod + langLinkForAjax,
			data: $('#adminForm input[name=\'event_id\'], #adminForm input[name=\'coupon_code\'], #adminForm .payment-calculation input[type=\'text\'], #adminForm .payment-calculation input[type=\'checkbox\']:checked, #adminForm .payment-calculation input[type=\'radio\']:checked, #adminForm .payment-calculation select'),
			dataType: 'json',
			success: function(msg, textStatus, xhr) {
				$('#btn-submit').removeAttr('disabled');
				$('#ajax-loading-animation').hide();
				if ($('#total_amount')) 
				{
					$('#total_amount').val(msg.total_amount);
				}
				if ($('#discount_amount')) 
				{
					$('#discount_amount').val(msg.discount_amount);
				}
				if ($('#tax_amount')) 
				{
					$('#tax_amount').val(msg.tax_amount);
				}
                if ($('#payment_processing_fee'))
                {
                    $('#payment_processing_fee').val(msg.payment_processing_fee);
                }
				if ($('#amount')) 
				{								
					$('#amount').val(msg.amount);
				}
                if ($('#deposit_amount'))
                {
                    $('#deposit_amount').val(msg.deposit_amount);
                }
                if (($('#amount').length || $('#total_amount').length) && msg.amount == 0)
                {
                    $('.payment_information').css('display', 'none');
                }
                else
                {
                    $('.payment_information').css('display', '');
                    updatePaymentMethod();
                }

                if (msg.coupon_valid == 1)
                {
                    $('#coupon_validate_msg').hide();
                }
                else
                {
                    $('#coupon_validate_msg').show();
                }
            },
			error: function(jqXHR, textStatus, errorThrown) {						
				alert(textStatus);
			}
		});		
		
	});
}

function calculateGroupRegistrationFee() 
{
	Eb.jQuery(function($) {						
		$('#btn-process-group-billing').attr('disabled', 'disabled');
		$('#ajax-loading-animation').show();
        if($('input:radio[name^=payment_method]').length)
        {
            var paymentMethod = $('input:radio[name^=payment_method]:checked').val();
        }
        else
        {
            var paymentMethod = $('input[name^=payment_method]').val();
        }
		$.ajax({
			type: 'POST',
			url: siteUrl + 'index.php?option=com_eventbooking&task=register.calculate_group_registration_fee&payment_method=' + paymentMethod + langLinkForAjax,
			data: $('#adminForm input[name=\'event_id\'], #adminForm input[name=\'coupon_code\'], #adminForm .payment-calculation input[type=\'text\'], #adminForm .payment-calculation input[type=\'checkbox\']:checked, #adminForm .payment-calculation input[type=\'radio\']:checked, #adminForm .payment-calculation select'),
			dataType: 'json',
			success: function(msg, textStatus, xhr) {
				$('#btn-process-group-billing').removeAttr('disabled');
				$('#ajax-loading-animation').hide();
				if ($('#total_amount')) 
				{
					$('#total_amount').val(msg.total_amount);
				}
				if ($('#discount_amount')) 
				{
					$('#discount_amount').val(msg.discount_amount);
				}
				if ($('#tax_amount')) 
				{
					$('#tax_amount').val(msg.tax_amount);
				}
                if ($('#payment_processing_fee'))
                {
                    $('#payment_processing_fee').val(msg.payment_processing_fee);
                }
				if ($('#amount')) 
				{								
					$('#amount').val(msg.amount);
				}
                if ($('#deposit_amount'))
                {
                    $('#deposit_amount').val(msg.deposit_amount);
                }
                if (($('#amount').length || $('#total_amount').length) && msg.amount == 0)
                {
                    $('.payment_information').css('display', 'none');
                }
                else
                {
                    $('.payment_information').css('display', '');
                    updatePaymentMethod();
                }
                if (msg.coupon_valid == 1)
                {
                    $('#coupon_validate_msg').hide();
                }
                else
                {
                    $('#coupon_validate_msg').show();
                }
            },
			error: function(jqXHR, textStatus, errorThrown) {						
				alert(textStatus);
			}
		});		
		
	});
}

function calculateCartRegistrationFee() 
{
	Eb.jQuery(function($) {						
		$('#btn-submit').attr('disabled', 'disabled');
		$('#ajax-loading-animation').show();
        if($('input:radio[name^=payment_method]').length)
        {
            var paymentMethod = $('input:radio[name^=payment_method]:checked').val();
        }
        else
        {
            var paymentMethod = $('input[name^=payment_method]').val();
        }
		$.ajax({
			type: 'POST',
			url: siteUrl + 'index.php?option=com_eventbooking&task=cart.calculate_cart_registration_fee&payment_method=' + paymentMethod + langLinkForAjax,
			data: $('#adminForm input[name=\'coupon_code\'], #adminForm .payment-calculation input[type=\'text\'], #adminForm .payment-calculation input[type=\'checkbox\']:checked, #adminForm .payment-calculation input[type=\'radio\']:checked, #adminForm .payment-calculation select'),
			dataType: 'json',
			success: function(msg, textStatus, xhr) {
				$('#btn-submit').removeAttr('disabled');
				$('#ajax-loading-animation').hide();
				if ($('#total_amount')) 
				{
					$('#total_amount').val(msg.total_amount);
				}
				if ($('#discount_amount')) 
				{
					$('#discount_amount').val(msg.discount_amount);
				}
				if ($('#tax_amount')) 
				{
					$('#tax_amount').val(msg.tax_amount);
				}
                if ($('#payment_processing_fee'))
                {
                    $('#payment_processing_fee').val(msg.payment_processing_fee);
                }
				if ($('#amount')) 
				{								
					$('#amount').val(msg.amount);
				}
                if ($('#deposit_amount'))
                {
                    $('#deposit_amount').val(msg.deposit_amount);
                }
                if (($('#amount').length || $('#total_amount').length) && msg.amount == 0)
                {
                    $('.payment_information').css('display', 'none');
                }
                else
                {
                    $('.payment_information').css('display', '');
                    updatePaymentMethod();
                }

                if (msg.coupon_valid == 1)
                {
                    $('#coupon_validate_msg').hide();
                }
                else
                {
                    $('#coupon_validate_msg').show();
                }
            },
			error: function(jqXHR, textStatus, errorThrown) {						
				alert(textStatus);
			}
		});		
		
	});
}

function showHideDependFields(fieldId, fieldName, fieldType, fieldSuffix)
{
	Eb.jQuery(function($) {
        $('#ajax-loading-animation').show();
        var masterFieldsSelector;
        if (fieldSuffix)
        {
            masterFieldsSelector = '.master-field-' + fieldSuffix + ' input[type=\'checkbox\']:checked,' + ' .master-field-' + fieldSuffix + ' input[type=\'radio\']:checked,' + ' .master-field-' + fieldSuffix + ' select';
        }
        else
        {
            masterFieldsSelector = '.master-field input[type=\'checkbox\']:checked, .master-field input[type=\'radio\']:checked, .master-field select';
        }
        $.ajax({
            type: 'POST',
            url: siteUrl + 'index.php?option=com_eventbooking&task=get_depend_fields_status&field_id=' + fieldId + '&field_suffix=' + fieldSuffix + langLinkForAjax,
            data: $(masterFieldsSelector),
            dataType: 'json',
            success: function(msg, textStatus, xhr) {
                $('#ajax-loading-animation').hide();
                var hideFields = msg.hide_fields.split(',');
                var showFields = msg.show_fields.split(',');
                for (var i = 0; i < hideFields.length ; i++)
                {
                    $('#' + hideFields[i]).hide();
                }
                for (var i = 0; i < showFields.length ; i++)
                {
                    $('#' + showFields[i]).show();
                }
                if (typeof eb_current_page === 'undefined')
                {
                	
                }
                else 
                {
                	if (eb_current_page == 'default')
                	{
                		calculateIndividualRegistrationFee();	
                	}
                	else if (eb_current_page == 'group_billing')
                	{
                		calculateGroupRegistrationFee();
                	}
                	else if (eb_current_page == 'cart')
                	{
                		calculateCartRegistrationFee();
                	}
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert(textStatus);
            }
        });
    });
}
function buildStateField(stateFieldId, countryFieldId, defaultState)
{
	Eb.jQuery(function($) {
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
				url: siteUrl + 'index.php?option=com_eventbooking&task=get_states&country_name='+ countryName+'&field_name='+stateFieldId + '&state_name=' + defaultState + langLinkForAjax,
				success: function(data) {
                    if ($('#field_' + stateFieldId + ' .controls').length)
                    {
                        $('#field_' + stateFieldId + ' .controls').html(data);
                    }
                    else
                    {
                        $('#field_' + stateFieldId + ' .col-sm-9').html(data);
                    }
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
						url: siteUrl + 'index.php?option=com_eventbooking&task=get_states&country_name='+ $(this).val()+'&field_name=' + stateFieldId + '&state_name=' + defaultState + langLinkForAjax,
						success: function(data) {
                            if ($('#field_' + stateFieldId + ' .controls').length)
                            {
                                $('#field_' + stateFieldId + ' .controls').html(data);
                            }
                            else
                            {
                                $('#field_' + stateFieldId + ' .col-sm-9').html(data);
                            }
						},
						error: function(jqXHR, textStatus, errorThrown) {						
							alert(textStatus);
						}
					});
					
				});
			}						
		}//end check exits state
				
	});		
}

function showDepositAmount(paymentTypeSelect)
{
    Eb.jQuery(function($) {
        if ($(paymentTypeSelect).val() == 1)
        {
            $('#deposit_amount_container').show();
        }
        else
        {
            $('#deposit_amount_container').hide();
        }
    });
}
