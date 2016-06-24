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
JHtml::_('behavior.calendar');
EventbookingHelperJquery::validateForm();
if ($this->config->accept_term ==1 && !$this->config->fix_term_and_condition_popup)
{
	EventbookingHelperJquery::colorbox();
}
if (strlen(strip_tags($this->message->{'registration_form_message_group'.$this->fieldSuffix})))
{
	$msg = $this->message->{'registration_form_message_group'.$this->fieldSuffix};
}
else
{
	$msg = $this->message->registration_form_message_group;
}

if ($this->waitingList)
{
	$headerText = JText::_('EB_JOIN_WAITINGLIST');
	if (strlen(strip_tags($this->message->{'waitinglist_form_message'.$this->fieldSuffix})))
	{
		$msg = $this->message->{'waitinglist_form_message'.$this->fieldSuffix};
	}
	else
	{
		$msg = $this->message->waitinglist_form_message;
	}
	$msg = str_replace('[EVENT_TITLE]', $this->event->title, $msg) ;
}
else
{
	$headerText = JText::_('EB_GROUP_REGISTRATION') ;
	if (strlen(strip_tags($this->message->{'registration_form_message_group'.$this->fieldSuffix})))
	{
		$msg = $this->message->{'registration_form_message_group'.$this->fieldSuffix};
	}
	else
	{
		$msg = $this->message->registration_form_message_group;
	}
	$msg = str_replace('[EVENT_TITLE]', $this->event->title, $msg) ;
	$msg = str_replace('[EVENT_DATE]', JHtml::_('date', $this->event->event_date, $this->config->event_date_format, null), $msg) ;
}
$headerText = str_replace('[EVENT_TITLE]', $this->event->title, $headerText);
?>
<div id="eb-group-registration-form" class="eb-container">
	<h1 class="eb-page-title"><?php echo $headerText; ?></h1>
	<?php
	if (strlen($msg))
	{
	?>
		<div class="eb-message"><?php echo $msg ; ?></div>
	<?php
	}
	?>
	<div id="eb-number-group-members">
		<div class="eb-form-heading">
			<?php echo JText::_('EB_NUMBER_MEMBERS'); ?>
		</div>
		<div class="eb-form-content">

		</div>
	</div>
	<?php
		if ($this->config->collect_member_information)
		{
		?>
			<div id="eb-group-members-information">
				<div class="eb-form-heading">
					<?php echo JText::_('EB_MEMBERS_INFORMATION'); ?>
				</div>
				<div class="eb-form-content"></div>
			</div>
		<?php
		}
		if($this->showBillingStep)
		{
		?>
			<div id="eb-group-billing">
				<div class="eb-form-heading">
					<?php echo JText::_('EB_BILLING_INFORMATION'); ?>
				</div>
				<div class="eb-form-content">

				</div>
			</div>
		<?php
		}
	?>
	<script type="text/javascript">
		<?php
			if ($this->captchaInvalid && $this->showBillingStep)
			{
			?>
				var step = 'group_billing';
			<?php
			}
			elseif ($this->captchaInvalid && !$this->showBillingStep)
			{
			?>
				var step = 'group_members';
			<?php
			}
			else
			{
			?>
				var step = window.location.hash.substr(1);
			<?php
			}
		?>
		var returnUrl = "<?php echo base64_encode(JFactory::getURI()->toString().'#group_billing'); ?>";
		Eb.jQuery(document).ready(function($)
		{
			if (step == 'group_billing')
			{
				$.ajax({
					url: siteUrl + 'index.php?option=com_eventbooking&view=register&layout=group_billing&event_id=<?php echo $this->event->id; ?>&Itemid=<?php echo $this->Itemid; ?>&format=raw' + langLinkForAjax,
					dataType: 'html',
					success: function(html) {
						$('#eb-group-billing .eb-form-content').html(html);
						$('#eb-group-billing .eb-form-content').slideDown('slow');
						if ($('#email').val())
						{
							$('#email').validationEngine('validate');
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			}
			else if (step == 'group_members')
			{
				$.ajax({
					url: siteUrl + 'index.php?option=com_eventbooking&view=register&layout=group_members&event_id=<?php echo $this->event->id; ?>&Itemid=<?php echo $this->Itemid; ?>&format=raw' + langLinkForAjax,
					dataType: 'html',
					success: function(html) {
						$('#eb-group-members-information .eb-form-content').html(html);
						$('#eb-group-members-information .eb-form-content').slideDown('slow');
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			}
			else
			{
				$.ajax({
					url: siteUrl + 'index.php?option=com_eventbooking&view=register&layout=number_members&event_id=<?php echo $this->event->id; ?>&Itemid=<?php echo $this->Itemid; ?>&format=raw' + langLinkForAjax,
					dataType: 'html',
					success: function(html) {
						$('#eb-number-group-members .eb-form-content').html(html);
						$('#eb-number-group-members .eb-form-content').slideDown('slow');
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			}

		});
	</script>
</div>