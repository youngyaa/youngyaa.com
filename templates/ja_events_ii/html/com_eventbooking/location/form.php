<?php
/**
 * @version            2.0.3
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2015 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die;
$bootstrapHelper   = $this->bootstrapHelper;
$controlGroupClass = $bootstrapHelper->getClassMapping('control-group');
$controlLabelClass = $bootstrapHelper->getClassMapping('control-label');
$controlsClass     = $bootstrapHelper->getClassMapping('controls');
?>
<script type="text/javascript">
	function checkData(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			form.task.value = pressbutton;
			form.submit();
			return;
		} else {
			if (form.name.value == '') {
				alert("<?php echo JText::_('EN_ENTER_LOCATION_NAME'); ?>");
				form.name.focus();
				return ;
			}
			if (form.address.value == '') {
				alert("<?php echo JText::_('EN_ENTER_LOCATION_ADDRESS'); ?>");
				form.address.focus();
				return ;
			}
			if (form.city.value == '') {
				alert("<?php echo JText::_('EN_ENTER_LOCATION_CITY'); ?>");
				form.city.focus();
				return ;
			}
			if (form.zip.value == '') {
				alert("<?php echo JText::_('EN_ENTER_LOCATION_ZIP'); ?>");
				form.zip.focus();
				return ;
			}
			form.task.value = pressbutton;
			form.submit();
		}
	}

	function deleteLocation() {
		if (confirm("<?php echo JText::_("EB_DELETE_LOCATION_CONFIRM"); ?>"))
		{
			var form = document.adminForm ;
			form.task.value = 'delete';
			form.submit();
		}
	}
</script>

<div class="submit-location-page">
	<h1 class="eb-page-heading"><?php echo JText::_('EB_ADD_EDIT_LOCATION'); ?></h1>
	<form action="index.php?option=com_eventbooking&view=location" method="post" name="adminForm" id="adminForm" class="form form-horizontal">
		<div class="<?php echo $controlGroupClass;  ?>">
			<label class="<?php echo $controlLabelClass; ?>">
				<?php echo JText::_('EB_NAME'); ?>
				<span class="required">*</span>
			</label>
			<div class="<?php echo $controlsClass; ?>">
				<input class="text_area" type="text" name="name" id="name" size="50" maxlength="250" value="<?php echo $this->item->name;?>" />
			</div>
		</div>

		<div class="<?php echo $controlGroupClass;  ?>">
			<label class="<?php echo $controlLabelClass; ?>">
				<?php echo JText::_('EB_ADDRESS'); ?>
				<span class="required">*</span>
			</label>
			<div class="<?php echo $controlsClass; ?>">
				<input class="text_area input-xlarge" type="text" name="address" id="address" size="70" maxlength="250" value="<?php echo $this->item->address;?>" />
			</div>
		</div>

		<div class="<?php echo $controlGroupClass;  ?>">
			<label class="<?php echo $controlLabelClass; ?>">
				<?php echo JText::_('EB_CITY'); ?>
				<span class="required">*</span>
			</label>
			<div class="<?php echo $controlsClass; ?>">
				<input class="text_area" type="text" name="city" id="city" size="30" maxlength="250" value="<?php echo $this->item->city;?>" />
			</div>
		</div>

		<div class="<?php echo $controlGroupClass;  ?>">
			<label class="<?php echo $controlLabelClass; ?>">
				<?php echo JText::_('EB_STATE'); ?>
				<span class="required">*</span>
			</label>
			<div class="<?php echo $controlsClass; ?>">
				<input class="text_area" type="text" name="state" id="state" size="30" maxlength="250" value="<?php echo $this->item->state;?>" />
			</div>
		</div>

		<div class="<?php echo $controlGroupClass;  ?>">
			<label class="<?php echo $controlLabelClass; ?>">
				<?php echo JText::_('EB_ZIP'); ?>
				<span class="required">*</span>
			</label>
			<div class="<?php echo $controlsClass; ?>">
				<input class="text_area" type="text" name="zip" id="zip" size="20" maxlength="250" value="<?php echo $this->item->zip;?>" />
			</div>
		</div>

		<div class="<?php echo $controlGroupClass;  ?>">
			<label class="<?php echo $controlLabelClass; ?>">
				<?php echo JText::_('EB_COUNTRY'); ?>
				<span class="required">*</span>
			</label>
			<div class="<?php echo $controlsClass; ?>">
				<?php echo $this->lists['country'] ; ?>
			</div>
		</div>

		<div class="<?php echo $controlGroupClass;  ?>">
			<label class="<?php echo $controlLabelClass; ?>">
				<?php echo JText::_('EB_LATITUDE'); ?>
			</label>
			<div class="<?php echo $controlsClass; ?>">
				<input class="text_area" type="text" name="lat" id="lat" size="20" maxlength="250" value="<?php echo $this->item->lat;?>" />
			</div>
		</div>

		<div class="<?php echo $controlGroupClass;  ?>">
			<label class="<?php echo $controlLabelClass; ?>">
				<?php echo JText::_('EB_LONGITUDE'); ?>
			</label>
			<div class="<?php echo $controlsClass; ?>">
				<input class="text_area" type="text" name="long" id="long" size="20" maxlength="250" value="<?php echo $this->item->long;?>" />
			</div>
		</div>

		<div class="<?php echo $controlGroupClass;  ?>">
			<label class="<?php echo $controlLabelClass; ?>">
				<?php echo JText::_('EB_PUBLISHED') ; ?>
			</label>
			<?php
				if (version_compare(JVERSION, '3.0', 'ge'))
				{
					echo $this->lists['published'];
				}
				else
				{
				?>
					<div class="<?php echo $controlsClass; ?>">
						<?php echo $this->lists['published']; ?>
					</div>
				<?php
				}
			?>
		</div>

		<div class="form-actions">
			<input type="button" class="btn btn-primary" name="btnSave" value="<?php echo JText::_('EB_SAVE'); ?>" onclick="checkData('save');" />
			<?php
				if ($this->item->id)
				{
				?>
					<input type="button" class="btn btn-primary" name="btnSave" value="<?php echo JText::_('EB_DELETE_LOCATION'); ?>" onclick="deleteLocation();" />
				<?php
				}
			?>
			<input type="button" class="btn btn-primary" name="btnCancel" value="<?php echo JText::_('EB_CANCEL_LOCATION'); ?>" onclick="checkData('cancel');" />
		</div>

		<div class="clearfix"></div>
		<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
		<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_( 'form.token' ); ?>

	</form>
</div>