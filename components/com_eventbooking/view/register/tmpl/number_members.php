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
if (strlen(strip_tags($this->message->{'number_members_form_message'.$this->fieldSuffix})))
{
	$msg = $this->message->{'number_members_form_message'.$this->fieldSuffix};
}
else
{
	$msg = $this->message->number_members_form_message;
}
$bootstrapHelper   = $this->bootstrapHelper;
$controlGroupClass = $bootstrapHelper->getClassMapping('control-group');
$controlLabelClass = $bootstrapHelper->getClassMapping('control-label');
$controlsClass     = $bootstrapHelper->getClassMapping('controls');
$btnClass          = $bootstrapHelper->getClassMapping('btn');
if (strlen($msg))
{
?>
	<div class="eb-message"><?php echo $msg ; ?></div>
<?php
}
?>
<form name="eb-form-number-group-members" id="eb-form-number-group-members" autocomplete="off" class="form form-horizontal">
	<div class="<?php echo $controlGroupClass; ?>">
		<label class="<?php echo $controlLabelClass; ?>" for="number_registrants">
			<?php echo  JText::_('EB_NUMBER_REGISTRANTS') ?><span class="required">*</span>
		</label>
		<div class="<?php echo $controlsClass; ?>">
			<input type="text" class="input-mini validate[required,custom[number],min[<?php echo $this->minNumberRegistrants; ?>],max[<?php echo $this->maxRegistrants; ?>]" id="number_registrants" name="number_registrants" data-errormessage-range-underflow="<?php echo JText::sprintf('EB_NUMBER_REGISTRANTS_IN_VALID', $this->minNumberRegistrants); ?>" data-errormessage-range-overflow="<?php echo JText::sprintf('EB_MAX_REGISTRANTS_REACH', $this->maxRegistrants);?>" value="<?php echo $this->numberRegistrants;?>" />
		</div>
	</div>
	<div class="form-actions">
		<input type="button" name="btn-number-members-back" id="btn-number-members-back" class="<?php echo $btnClass; ?> btn-primary" value="<?php echo JText::_('EB_BACK'); ?>" onclick="window.history.go(-1) ;" />
		<input type="button" name="btn-process-number-members" id="btn-process-number-members" class="<?php echo $btnClass; ?> btn-primary" value="<?php echo JText::_('EB_NEXT'); ?>" />
	</div>
</form>
<script type="text/javascript">
	Eb.jQuery(document).ready(function($){
		$("#eb-form-number-group-members").validationEngine();
		$('#btn-process-number-members').click(function(){
			var formValid = $('#eb-form-number-group-members').validationEngine('validate');
			if (formValid)
			{
				$.ajax({
					url: siteUrl + 'index.php?option=com_eventbooking&view=register&task=register.store_number_registrants&number_registrants=' + $('input[name=\'number_registrants\']').val() + '&event_id=<?php echo $this->event->id; ?>&Itemid=<?php echo $this->Itemid; ?>&format=raw' + langLinkForAjax,
					dataType: 'html',
					beforeSend: function() {
						$('#btn-process-number-members').attr('disabled', true);
						$('#btn-process-number-members').after('<span class="wait">&nbsp;<img src="<?php echo JUri::base(true);?>/media/com_eventbooking/ajax-loadding-animation.gif" alt="" /></span>');
					},
					complete: function() {
						$('#btn-process-number-members').attr('disabled', false);
						$('.wait').remove();
					},
					success: function(html) {
						<?php
							if ($this->config->collect_member_information)
							{
							?>
								$('#eb-group-members-information .eb-form-content').html(html);
								$('#eb-number-group-members .eb-form-content').slideUp('slow');
								$('#eb-group-members-information .eb-form-content').slideDown('slow');
							<?php
							}
							else
							{
							?>
								$('#eb-group-billing .eb-form-content').html(html);
								$('#eb-number-group-members .eb-form-content').slideUp('slow');
								$('#eb-group-billing .eb-form-content').slideDown('slow');
								if ($('#email').val())
								{
									$('#email').validationEngine('validate');
								}
								$('#return_url').val(returnUrl);
							<?php
							}
						?>
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			}
		});

	})
</script>
