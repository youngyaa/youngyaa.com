<?php
/**
 * @version        	2.0.0
 * @package        	Joomla
 * @subpackage		Event Booking
 * @author  		Tuan Pham Ngoc
 * @copyright    	Copyright (C) 2010 - 2015 Ossolution Team
 * @license        	GNU/GPL, see LICENSE.php
 */

$bootstrapHelper   = $this->bootstrapHelper;
$controlGroupClass = $bootstrapHelper->getClassMapping('control-group');
$controlLabelClass = $bootstrapHelper->getClassMapping('control-label');
$controlsClass     = $bootstrapHelper->getClassMapping('controls');
$btnClass          = $bootstrapHelper->getClassMapping('btn');
?>
<form name="eb-form-group-members" id="eb-form-group-members" action="<?php echo JRoute::_('index.php?option=com_eventbooking&Itemid='.$this->Itemid); ?>" autocomplete="off" class="form form-horizontal" method="post">
<?php
$dateFields = array();
for ($i = 1 ; $i <= $this->numberRegistrants; $i++)
{
	$headerText = JText::_('EB_MEMBER_REGISTRATION') ;
	$headerText = str_replace('[ATTENDER_NUMBER]', $i, $headerText) ;
?>
	<h3 class="eb-heading">
		<?php echo $headerText; ?>
	</h3>
<?php
	$form = new RADForm($this->rowFields);
	$form->setFieldSuffix($i);
	//Bill form data
	if (count($this->membersData))
	{
		$form->bind($this->membersData);
	}
	else
	{
		$form->bind(array('country_'.$i => $this->defaultCountry), true);
	}
	$form->buildFieldsDependency();
	if (!$this->waitingList)
	{
		$form->setEventId($this->event->id);
	}
	$fields = $form->getFields();
	//We don't need to use ajax validation for email field for group members
	if (isset($fields['email']))
	{
		$emailField = $fields['email'];
		$cssClass = $emailField->getAttribute('class');
		$cssClass = str_replace(',ajax[ajaxEmailCall]', '', $cssClass);
		$emailField->setAttribute('class', $cssClass);
	}
	foreach ($fields as $field)
	{
		if ($i > 1 && $field->row->only_show_for_first_member)
		{
			continue;
		}

		if ($i > 1 && $field->row->only_require_for_first_member)
		{
			$field->makeFieldOptional();
		}

		echo $field->getControlGroup($bootstrapHelper);

		if ($field->type == 'Date')
		{
			$dateFields[] = $field->name;
		}
	}
}
if ($this->showCaptcha)
{
?>
	<div class="<?php echo $controlGroupClass; ?>">
		<label class="<?php echo $controlLabelClass; ?>">
			<?php echo JText::_('EB_CAPTCHA'); ?><span class="required">*</span>
		</label>
		<div class="<?php echo $controlsClass; ?>">
			<?php echo $this->captcha; ?>
		</div>
	</div>
<?php
}
?>
	<div class="form-actions">
		<input type="button" id="btn-group-members-back" name="btn-group-members-back" class="<?php echo $btnClass; ?> btn-primary" value="<?php echo JText::_('EB_BACK'); ?>"/>
		<input type="<?php echo $this->showBillingStep ? "button" : "submit";?>" id="btn-process-group-members" name="btn-process-group-members" class="<?php echo $btnClass; ?> btn-primary" value="<?php echo JText::_('EB_NEXT'); ?>" />
	</div>
	<input type="hidden" name="task" value="register.store_group_members_data" />
	<input type="hidden" name="event_id" value="<?php echo $this->eventId; ?>" />
	<script type="text/javascript">
			Eb.jQuery(document).ready(function($){
				<?php
					if (count($dateFields))
					{
						echo EventbookingHelperHtml::getCalendarSetupJs($dateFields);
					}
				?>
				$("#eb-form-group-members").validationEngine();
				<?php
					for($i = 1; $i <= $this->numberRegistrants; $i++)
					{
					?>
						buildStateField('state_<?php echo $i; ?>', 'country_<?php echo $i; ?>', '');
					<?php
					}
					if ($this->showCaptcha && $this->captchaPlugin == 'recaptcha')
					{
						$captchaPlugin = JPluginHelper::getPlugin('captcha', 'recaptcha');
						$params = $captchaPlugin->params;
						$version    = $params->get('version', '1.0');
						$pubkey = $params->get('public_key', '');
						if ($version == '1.0')
						{
							$theme  = $params->get('theme', 'clean');
						?>
							Recaptcha.create("<?php echo $pubkey; ?>", "dynamic_recaptcha_1", {theme: "<?php echo $theme; ?>"});
						<?php
						}
						else
						{
							$theme = $params->get('theme2', 'light');
							$langTag = JFactory::getLanguage()->getTag();
							if (JFactory::getApplication()->isSSLConnection())
							{
								$file = 'https://www.google.com/recaptcha/api.js?hl=' . $langTag . '&onload=onloadCallback&render=explicit';
							}
							else
							{
								$file = 'http://www.google.com/recaptcha/api.js?hl=' . $langTag . '&onload=onloadCallback&render=explicit';
							}
							JHtml::_('script', $file, true, true);
						?>
							grecaptcha.render("dynamic_recaptcha_1", {sitekey: "' . <?php echo $pubkey;?> . '", theme: "' . <?php echo $theme; ?> . '"});
						<?php
						}
					}
					if ($this->showBillingStep)
					{
					?>
						$('#btn-process-group-members').click(function(){
							var formValid = $('#eb-form-group-members').validationEngine('validate');
							if (formValid)
							{
								$.ajax({
									url: siteUrl + 'index.php?option=com_eventbooking&task=register.store_group_members_data&event_id=<?php echo $this->event->id; ?>&Itemid=<?php echo $this->Itemid; ?>&format=raw' + langLinkForAjax,
									type: 'post',
									data: $('#eb-form-group-members').serialize(),
									dataType: 'html',
									beforeSend: function() {
										$('#btn-process-group-members').attr('disabled', true);
										$('#btn-process-group-members').after('<span class="wait">&nbsp;<img src="<?php echo JUri::base(true);?>/media/com_eventbooking/ajax-loadding-animation.gif" alt="" /></span>');
									},
									complete: function() {
										$('#btn-process-group-members').attr('disabled', false);
										$('.wait').remove();
									},
									success: function(html) {
										$('#eb-group-billing .eb-form-content').html(html);
										$('#eb-group-members-information .eb-form-content').slideUp('slow');
										$('#eb-group-billing .eb-form-content').slideDown('slow');
										if ($('#email').val())
										{
											$('#email').validationEngine('validate');
										}
										$('#return_url').val(returnUrl);
									},
									error: function(xhr, ajaxOptions, thrownError) {
										alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
									}
								});
							}
						});
					<?php
					}
				?>

				$('#btn-group-members-back').click(function(){
					$.ajax({
						url: siteUrl + 'index.php?option=com_eventbooking&view=register&layout=number_members&event_id=<?php echo $this->event->id; ?>&Itemid=<?php echo $this->Itemid; ?>&format=raw' + langLinkForAjax,
						type: 'post',
						dataType: 'html',
						beforeSend: function() {
							$('#btn-group-members-back').attr('disabled', true);
						},
						complete: function() {
							$('#btn-group-members-back').attr('disabled', false);
						},
						success: function(html) {
							$('#eb-number-group-members .eb-form-content').html(html);
							$('#eb-group-members-information .eb-form-content').slideUp('slow');
							$('#eb-number-group-members .eb-form-content').slideDown('slow');
						},
						error: function(xhr, ajaxOptions, thrownError) {
							alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
						}
					});
				});

			})
	</script>
</form>