<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die ;

OSMembershipHelper::loadLanguage();
OSMembershipHelperJquery::validateForm();

$selectedState = '';
?>
<script type="text/javascript">
	Joomla.submitbutton = function(pressbutton)
	{
		var form = document.adminForm;
		if (pressbutton == 'cancel')
		{
			jQuery("#adminForm").validationEngine('detach');
			Joomla.submitform(pressbutton, form);
		}
		else
		{
			//Validate the entered data before submitting
			Joomla.submitform(pressbutton, form);
		}
	}
</script>
<div class="row-fluid" style="float:left">
<form action="index.php?option=com_osmembership&view=subscription" method="post" name="adminForm" id="adminForm" autocomplete="off" enctype="multipart/form-data" class="form form-horizontal">
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('OSM_PLAN'); ?><span class="required">&nbsp;*</span>
		</label>
		<div class="controls">
			<?php echo $this->lists['plan_id'] ; ?>
		</div>
	</div>
<?php
if (!$this->item->id)
{
?>
	<div class="control-group" id="username_container">
		<label class="control-label">
			<?php echo JText::_('OSM_USERNAME'); ?><span class="required">*</span>
		</label>
		<div class="controls">
			<input type="text" name="username" size="20" class="validate[required,ajax[ajaxUserCall]]" value="" />
			<?php echo JText::_('OSM_USERNAME_EXPLAIN'); ?>
		</div>
	</div>
	<div class="control-group" id="password_container">
		<label class="control-label">
			<?php echo JText::_('OSM_PASSWORD'); ?><span class="required">*</span>
		</label>
		<div class="controls">
			<?php
				$params = JComponentHelper::getParams('com_users');
				$minimumLength = $params->get('minimum_length', 4);
				($minimumLength) ? $minSize = ",minSize[$minimumLength]" : $minSize = "";
				if(version_compare(JVERSION, '3.1.2', 'ge'))
				{
					$passwordValidation = ',ajax[ajaxValidatePassword]';
				}
				else
				{
					$passwordValidation = '';
				}
			?>
			<input type="password" name="password" size="20" value="" class="validate[required<?php echo $minSize.$passwordValidation;?>]" />
		</div>
	</div>
<?php
}
?>
<div class="control-group">
	<label class="control-label">
		<?php echo JText::_('OSM_USER'); ?>
	</label>
	<div class="controls">
		<?php echo OSMembershipHelper::getUserInput($this->item->user_id, (int) $this->item->id) ; ?>
	</div>
</div>
<?php
if ($this->config->auto_generate_membership_id)
{
?>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('OSM_MEMBERSHIP_ID'); ?>
		</label>
		<div class="controls">
			<input type="text" name="membership_id" value="<?php echo $this->item->membership_id > 0 ? $this->item->membership_id : ''; ?>" class="inputbox" size="20" />
		</div>
	</div>
<?php
}
$fields = $this->form->getFields();
if (isset($fields['state']))
{
	if ($fields['state']->type == 'State')
	{
		$stateType = 1;
	}
	else
	{
		$stateType = 0;
	}
	$selectedState = $fields['state']->value;
}

if (isset($fields['email']))
{
	$fields['email']->setAttribute('class', 'validate[required,custom[email]]');
}

foreach ($fields as $field)
{
	echo $field->getControlGroup();
}
?>
<div class="control-group">
	<label class="control-label">
		<?php echo  JText::_('OSM_CREATED_DATE'); ?>
	</label>
	<div class="controls">
		<?php echo JHtml::_('calendar', $this->item->created_date, 'created_date', 'created_date', '%Y-%m-%d %H:%M:%S') ; ?>
	</div>
</div>
<div class="control-group">
	<label class="control-label">
		<?php echo  JText::_('OSM_SUBSCRIPTION_START_DATE'); ?>
	</label>
	<div class="controls">
		<?php echo JHtml::_('calendar', $this->item->from_date, 'from_date', 'from_date', '%Y-%m-%d %H:%M:%S') ; ?>
	</div>
</div>
<div class="control-group">
	<label class="control-label">
		<?php echo  JText::_('OSM_SUBSCRIPTION_END_DATE'); ?>
	</label>
	<div class="controls">
		<?php
		if ($this->item->lifetime_membership || $this->item->to_date == '2099-12-31 23:59:59')
		{
			echo JText::_('OSM_LIFETIME');
		}
		else
		{
			echo JHtml::_('calendar', $this->item->to_date, 'to_date', 'to_date', '%Y-%m-%d %H:%M:%S') ;
		}
		?>
	</div>
</div>
<div class="control-group">
	<label class="control-label">
		<?php echo  JText::_('OSM_NET_AMOUNT'); ?>
	</label>
	<div class="controls">
		<?php echo $this->config->currency_symbol ;  ?><input type="text" class="inputbox" name="amount" value="<?php echo $this->item->amount > 0 ? round($this->item->amount, 2) : ""; ?>" size="7" />
	</div>
</div>
<?php
if ($this->item->discount_amount > 0 || !$this->item->id)
{
?>
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('OSM_DISCOUNT_AMOUNT'); ?>
		</label>
		<div class="controls">
			<?php echo $this->config->currency_symbol ;  ?><input type="text" class="inputbox" name="discount_amount" value="<?php echo $this->item->discount_amount > 0 ? round($this->item->discount_amount, 2) : ""; ?>" size="7" />
		</div>
	</div>
<?php
}

if ($this->item->tax_amount > 0 || !$this->item->id)
{
?>
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('OSM_TAX_AMOUNT'); ?>
		</label>
		<div class="controls">
			<?php echo $this->config->currency_symbol ;  ?><input type="text" class="inputbox" name="tax_amount" value="<?php echo $this->item->tax_amount > 0 ? round($this->item->tax_amount, 2) : ""; ?>" size="7" />
		</div>
	</div>
<?php
}
if ($this->item->payment_processing_fee > 0 || !$this->item->id)
{
?>
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('OSM_PAYMENT_FEE'); ?>
		</label>
		<div class="controls">
			<?php echo $this->config->currency_symbol ;  ?><input type="text" class="inputbox" name="payment_processing_fee" value="<?php echo $this->item->payment_processing_fee > 0 ? round($this->item->payment_processing_fee, 2) : ""; ?>" size="7" />
		</div>
	</div>
<?php
}
?>
<div class="control-group">
	<label class="control-label">
		<?php echo  JText::_('OSM_GROSS_AMOUNT'); ?>
	</label>
	<div class="controls">
		<?php echo $this->config->currency_symbol ;  ?><input type="text" class="inputbox" name="gross_amount" value="<?php echo $this->item->gross_amount > 0 ? round($this->item->gross_amount, 2) : ""; ?>" size="7" />
	</div>
</div>
<div class="control-group">
	<label class="control-label">
		<?php echo JText::_('OSM_PAYMENT_METHOD') ?>
	</label>
	<div class="controls">
		<?php echo $this->lists['payment_method'] ; ?>
	</div>
</div>
<div class="control-group">
	<label class="control-label">
		<?php echo JText::_('OSM_TRANSACTION_ID'); ?>
	</label>
	<div class="controls">
		<input type="text" class="inputbox" size="50" name="transaction_id" id="transaction_id" value="<?php echo $this->item->transaction_id ; ?>" />
	</div>
</div>
<div class="control-group">
	<label class="control-label">
		<?php echo JText::_('OSM_SUBSCRIPTION_STATUS'); ?>
	</label>
	<div class="controls">
		<?php echo $this->lists['published'] ; ?>
	</div>
</div>
<?php
if ($this->item->payment_method == "os_creditcard")
{
	$params = new JRegistry($this->item->params);
?>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('OSM_FIRST_12_DIGITS_CREDITCARD_NUMBER'); ?>
		</label><label class="control-label">
		<div class="controls">
			<?php echo $params->get('card_number'); ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('AUTH_CARD_EXPIRY_DATE'); ?>
		</label>
		<div class="controls">
			<?php echo $params->get('exp_date'); ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('AUTH_CVV_CODE'); ?>
		</label>
		<div class="controls">
			<?php echo $params->get('cvv'); ?>
		</div>
	</div>
<?php
}
?>
<div class="clr"></div>
<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="task" value="" />
<?php echo JHtml::_( 'form.token' ); ?>
<script type="text/javascript">
	var siteUrl = "<?php echo JUri::root(); ?>";
	(function($){
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
				},
				error: function(jqXHR, textStatus, errorThrown) {
					alert(textStatus);
				}
			});
		});
		populateSubscriberData = (function(id, planId, title){
			$('#username_container').hide();
			$('#password_container').hide();
			$.ajax({
				type : 'POST',
				url : 'index.php?option=com_osmembership&task=get_profile_data&user_id=' + id + '&plan_id=' +planId,
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


		<?php
			if ($stateType)
			{
			?>
				function buildStateField(stateFieldId, countryFieldId, defaultState)
				{
					if($('#' + stateFieldId).length)
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
							url: siteUrl + 'index.php?option=com_osmembership&task=register.get_states&country_name='+ countryName+'&field_name='+stateFieldId + '&state_name=' + defaultState,
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
									url: siteUrl + 'index.php?option=com_osmembership&task=register.get_states&country_name='+ $(this).val()+'&field_name=' + stateFieldId + '&state_name=' + defaultState,
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
				}
			<?php
			}
		?>

		$(document).ready(function(){
			$('#adminForm').validationEngine('attach', {
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
			<?php
				if ($stateType)
				{
				?>
					buildStateField('state', 'country', '<?php echo $selectedState; ?>');
				<?php
				}
			?>
		});
	})(jQuery);
</script>
</form>
</div>