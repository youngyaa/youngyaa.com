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

EventbookingHelperJquery::validateForm();
$bootstrapHelper   = $this->bootstrapHelper;
$controlGroupClass = $bootstrapHelper->getClassMapping('control-group');
$controlLabelClass = $bootstrapHelper->getClassMapping('control-label');
$controlsClass     = $bootstrapHelper->getClassMapping('controls');
?>
<div id="eb-event-password-page" class="eb-container">
<h1 class="eb-page-heading"><?php echo JText::_('EB_PRIVATE_EVENT_VALIDATION'); ?></h1>
<form method="post" name="adminForm" id="adminForm" action="<?php echo JRoute::_('index.php?option=com_eventbooking&task=register.check_event_password&Itemid='.$this->Itemid); ?>" autocomplete="off" class="form form-horizontal">
	<p><?php echo JText::_('EB_PRIVATE_EVENT_VALIDATION_MSG'); ?></p>
	<div class="<?php echo $controlGroupClass; ?>">
		<label class="<?php echo $controlLabelClass; ?>" for="password">
			<?php echo  JText::_('EB_PASSWORD') ?><span class="required">*</span>
		</label>
		<div class="<?php echo $controlsClass; ?>">
			<input type="text" id="password" name="password" class="input-large validate[required]" value="" />
		</div>
	</div>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlsClass; ?>">
			<input type="button" value="<?php echo JText::_('EB_CANCEL'); ?>" class="button btn" onclick="cancel();" />
			<input type="submit" value="<?php echo JText::_('EB_CONTINUE'); ?>" class="button btn btn-primary" />
		</div>
	</div>
	<script type="text/javascript">
		Eb.jQuery(document).ready(function($){
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
		})

		function cancel()
		{
			location.href = "<?php echo $this->eventUrl; ?>";
		}
	</script>
	<input type="hidden" name="return" value="<?php echo $this->return; ?>" />
	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
	<input type="hidden" name="event_id" value="<?php echo $this->eventId; ?>" />
</form>
</div>