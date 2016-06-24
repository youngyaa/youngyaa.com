/****
 * Payment method class
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

var stripeResponseHandler = function(status, response) {
	OSM.jQuery(function($) {
		var $form = $('#os_form');
		if (response.error) {
			// Show the errors on the form
			//$form.find('.payment-errors').text(response.error.message);
			alert(response.error.message);
			$form.find('#btn-submit').prop('disabled', false);
		} else {
			// token contains id, last4, and card type
			var token = response.id;
			// Empty card data since we now have token
			$('#x_card_num').val('');
			$('#x_card_code').val('');
			$('#card_holder_name').val('');
			// Insert the token into the form so it gets submitted to the server
			$form.append($('<input type="hidden" name="stripeToken" />').val(token));
			// and re-submit
			$form.get(0).submit();
		}
	});
};
OSM.jQuery(function($){
	/**
	 * JD validate form
	 */
	OSMVALIDATEFORM = (function(formId){
		$(formId).validationEngine('attach', {
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
	})
	/***
	 * Process event when someone change a payment method
	 */
	changePaymentMethod = (function(){
		updatePaymentMethod();
		if (document.os_form.show_payment_fee.value == 1)
		{
			// Re-calculate subscription fee in case there is payment fee associated with payment method
			calculateSubscriptionFee();
		}
	});


	/***
	 * Process event when someone change a payment method (no recalculate fee)
	 */
	updatePaymentMethod = (function(){
		var form = document.os_form;
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
			if (method.getCardHolderName())
			{
				$('#tr_card_holder_name').show();
			}
			else
			{
				$('#tr_card_holder_name').show();
			}
		}
		else
		{
			$('#tr_card_number').hide();
			$('#tr_exp_date').hide();
			$('#tr_cvv_code').hide();
			$('#tr_card_holder_name').hide();
		}
	});
	/**
	 * calculate subcription free
	 */
	calculateSubscriptionFee = (function(){
		if($('input:radio[name^=payment_method]').length)
		{
			var paymentMethod = $('input:radio[name^=payment_method]:checked').val();
		}
		else
		{
			var paymentMethod = $('input[name^=payment_method]').val();
		}
		if (typeof paymentMethod == 'undefined')
		{
			return;
		}
		$('#btn-submit').attr('disabled', 'disabled');
		$('#ajax-loading-animation').show();
		$.ajax({
			type: 'POST',
			url: siteUrl + 'index.php?option=com_osmembership&task=register.calculate_subscription_fee&payment_method=' + paymentMethod + langLinkForAjax,
			data: $('#os_form input[name=\'plan_id\'], #os_form input[name=\'coupon_code\'], #os_form select[name=\'country\'], #os_form select[name=\'state\'], #os_form input.taxable[type=\'text\'], #os_form input[name=\'coupon_code\'], #os_form input[name=\'act\'], #os_form input[name=\'renew_option_id\'], #os_form input[name=\'upgrade_option_id\'], #os_form .payment-calculation input[type=\'text\'], #os_form .payment-calculation input[type=\'checkbox\']:checked, #os_form .payment-calculation input[type=\'radio\']:checked, #os_form .payment-calculation select'),
			dataType: 'json',
			success: function(msg, textStatus, xhr) {
				$('#btn-submit').removeAttr('disabled');
				$('#ajax-loading-animation').hide();
				if ($('#amount'))
				{
					$('#amount').val(msg.amount);
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
				if ($('#gross_amount'))
				{
					$('#gross_amount').val(msg.gross_amount);
				}

				if ($('#trial_amount'))
				{
					$('#trial_amount').val(msg.trial_amount);
				}
				if ($('#trial_discount_amount'))
				{
					$('#trial_discount_amount').val(msg.trial_discount_amount);
					if (msg.show_trial_discount_amount)
					{
						$('#trial_discount_amount_container').show();
					}
					else
					{
						$('#trial_discount_amount_container').hide();
					}
				}
				if ($('#trial_tax_amount'))
				{
					$('#trial_tax_amount').val(msg.trial_tax_amount);
					if (msg.show_trial_tax_amount)
					{
						$('#trial_tax_amount_container').show();
					}
					else
					{
						$('#trial_tax_amount_container').hide();
					}
				}
				if ($('#trial_payment_processing_fee'))
				{
					$('#trial_payment_processing_fee').val(msg.trial_payment_processing_fee);
					if (msg.show_trial_payment_processing_fee)
					{
						$('#trial_payment_processing_fee_container').show();
					}
					else
					{
						$('#trial_payment_processing_fee_container').hide();
					}
				}
				if ($('#trial_gross_amount'))
				{
					$('#trial_gross_amount').val(msg.trial_gross_amount);
					if (msg.show_trial_gross_amount)
					{
						$('#trial_gross_amount_container').show();
					}
					else
					{
						$('#trial_gross_amount_container').hide();
					}
				}

				if ($('#regular_amount'))
				{
					$('#regular_amount').val(msg.regular_amount);
				}
				if ($('#regular_discount_amount'))
				{
					$('#regular_discount_amount').val(msg.regular_discount_amount);
				}
				if ($('#regular_tax_amount'))
				{
					$('#regular_tax_amount').val(msg.regular_tax_amount);
				}
				if ($('#regular_payment_processing_fee'))
				{
					$('#regular_payment_processing_fee').val(msg.regular_payment_processing_fee);
				}
				if ($('#regular_gross_amount'))
				{
					$('#regular_gross_amount').val(msg.regular_gross_amount);
				}
				if ($('#vat_country_code'))
				{
					// Dealing with Greece country
					if (msg.country_code == 'GR')
					{
						$('#vat_country_code').text('EL');
					}
					else
					{
						$('#vat_country_code').text(msg.country_code);
					}
				}

				// Show or Hide the VAT Number field depend on country
				var vatNumberField = $('input[name^=vat_number_field]').val();
				if (vatNumberField)
				{
					if (msg.show_vat_number_field == 1)
					{
						$('#field_' + vatNumberField).show();
					}
					else
					{
						$('#field_' + vatNumberField).hide();
					}
				}

				if (($('#gross_amount').val() != undefined && msg.gross_amount == 0) || ($('#regular_gross_amount').val() != undefined && msg.regular_gross_amount == 0))
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

				if (msg.vatnumber_valid == 1)
				{
					$('#vatnumber_validate_msg').hide();
				}
				else
				{
					$('#vatnumber_validate_msg').show();
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert(textStatus);
			}
		});
	});


	showHideDependFields = (function(fieldId, fieldName, fieldType){
		if (fieldType == 'Checkboxes')
		{
			var fieldValues = '';
			$('input[name="'+ fieldName +'[]"]:checked').each(function() {
				if (fieldValues)
				{
					fieldValues += ',' + $(this).val();
				}
				else
				{
					fieldValues += $(this).val();
				}
			});
		}
		else if (fieldType == 'Radio')
		{
			var fieldValues = $('input:radio[name="'+ fieldName +'"]:checked').val();
		}
		else
		{
			var fieldValues = $('#' + fieldName).val();
		}
		var data = {
			'task'	:	'register.get_depend_fields_status',
			'field_id' : fieldId,
			'field_values': fieldValues
		};
		$('#btn-submit').attr('disabled', 'disabled');
		$('#ajax-loading-animation').show();
		$.ajax({
			type: 'POST',
			url: siteUrl + 'index.php?option=com_osmembership' + langLinkForAjax,
			data: data,
			dataType: 'json',
			success: function(msg, textStatus, xhr) {
				$('#btn-submit').removeAttr('disabled');
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
				//Recalculate form field
				calculateSubscriptionFee();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert(textStatus);
			}
		});
	});

	/**
	 * function build state field
	 */
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
				url: siteUrl + 'index.php?option=com_osmembership&task=register.get_states&country_name='+ countryName+'&field_name='+stateFieldId + '&state_name=' + defaultState + langLinkForAjax,
				success: function(data) {
					if ($('#field_' + stateFieldId + ' .controls').length)
					{
						$('#field_' + stateFieldId + ' .controls').html(data);
					}
					else
					{
						$('#field_' + stateFieldId + ' .col-md-9').html(data);
					}
					if (typeof taxStateCountries != 'undefined')
					{
						if (stateFieldId == 'state' && taxStateCountries[0])
						{
							$('#state').change(function(){
								if ($('#' + countryFieldId).length)
								{
									var countryName = $('#country').val();
								}
								else
								{
									var countryName = $('#default_country').val();;
								}
								if (countryName && ($.inArray(countryName, taxStateCountries) != -1))
								{
									calculateSubscriptionFee();
								}
							});
						}
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
						url: siteUrl + 'index.php?option=com_osmembership&task=register.get_states&country_name='+ $(this).val()+'&field_name=' + stateFieldId + '&state_name=' + defaultState + langLinkForAjax,
						success: function(data) {
							if ($('#field_' + stateFieldId + ' .controls').length)
							{
								$('#field_' + stateFieldId + ' .controls').html(data);
							}
							else
							{
								$('#field_' + stateFieldId + ' .col-md-9').html(data);
							}

							//$('.wait').remove();
							if (typeof taxStateCountries != 'undefined')
							{
								if (stateFieldId == 'state' && taxStateCountries[0])
								{
									$('#state').change(function()
									{
										if ($('#country').length)
										{
											var countryName = $('#country').val();
										}
										else
										{
											var countryName = '';
										}
										if (countryName && ($.inArray(countryName, taxStateCountries) != -1))
										{
											calculateSubscriptionFee();
										}
									});
								}
							}
						},
						error: function(jqXHR, textStatus, errorThrown) {
							alert(textStatus);
						}
					});
					var countryBaseTax = paymentMethod = $('input[name^=country_base_tax]').val();
					if (countryBaseTax != 0)
					{
						calculateSubscriptionFee();
					}
				});
			}
		}//end check exits state
		else
		{
			if ($('#' + countryFieldId).length)
			{
				$('#' + countryFieldId).change(function(){
					var countryBaseTax = paymentMethod = $('input[name^=country_base_tax]').val();
					if (countryBaseTax != 0)
					{
						calculateSubscriptionFee();
					}
				});
			}
		}
	});
})
