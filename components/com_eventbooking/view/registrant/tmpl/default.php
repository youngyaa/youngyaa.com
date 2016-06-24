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
$format = 'Y-m-d';
EventbookingHelperJquery::validateForm();;
$selectedState = '';
?>
<h1 class="eb_title"><?php echo JText::_('EB_EDIT_REGISTRANT'); ?></h1>
<form action="<?php echo JRoute::_('index.php?option=com_eventbooking&view=registrant&Itemid=' . $this->Itemid); ?>"
      method="post" name="adminForm" id="adminForm">
	<table width="100%">
		<tr>
			<td width="100" class="title_cell">
				<?php echo JText::_('EB_EVENT'); ?>
			</td>
			<td class="field_cell">
				<?php
				if (!$this->item->id)
				{
					echo $this->lists['event_id'];
				}
				else
				{
					echo $this->event->title;
				}
				?>
			</td>
		</tr>
		<tr>
			<td class="title_cell">
				<?php echo JText::_('EB_NUMBER_REGISTRANTS'); ?>
			</td>
			<td>
				<?php
				if ($this->item->number_registrants)
				{
					echo $this->item->number_registrants;
				}
				else
				{
					?>
					<input class="text_area validate[required,custom[number]]" type="text" name="number_registrants"
					       id="number_registrants" size="40" maxlength="250" value=""/>
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
		if (isset($fields['email']))
		{
			$emailField = $fields['email'];
			$cssClass   = $emailField->getAttribute('class');
			$cssClass   = str_replace(',ajax[ajaxEmailCall]', '', $cssClass);
			$emailField->setAttribute('class', $cssClass);
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
						<td width="100" class="title_cell">
							<?php echo $field->title; ?>
						</td>
						<td class="controls">
							<?php echo $field->input; ?>
						</td>
					</tr>
				<?php
			}
		}
		if ($this->canChangeStatus)
		{
			?>
			<tr>
				<td class="title_cell">
					<?php echo JText::_('EB_REGISTRATION_STATUS'); ?>
				</td>
				<td>
					<?php echo $this->lists['published']; ?>
				</td>
			</tr>
		<?php
		}
		?>
		<tr>
			<td class="title_cell">
				<?php echo JText::_('EB_REGISTRATION_DATE'); ?>
			</td>
			<td>
				<?php echo JHtml::_('date', $this->item->register_date, $format, null); ?>
			</td>
		</tr>
		<tr>
			<td class="title_cell">
				<?php echo JText::_('EB_TOTAL_AMOUNT'); ?>
			</td>
			<td>
				<?php echo $this->config->currency_symbol ?><input type="text" name="total_amount" class="input-medium"
				                                                   value="<?php echo $this->item->total_amount > 0 ? round($this->item->total_amount, 2) : null; ?>"/>
			</td>
		</tr>

		<?php
		if ($this->item->discount_amount > 0 || $this->item->late_fee > 0 || $this->item->tax_amount > 0 || empty($this->item->id))
		{
			if ($this->item->discount_amount > 0 || empty($this->item->id))
			{
			?>
				<tr>
					<td class="title_cell">
						<?php echo JText::_('EB_DISCOUNT_AMOUNT'); ?>
					</td>
					<td>
						<?php echo $this->config->currency_symbol?><input type="text" name="discount_amount"
						                                                  class="input-medium"
						                                                  value="<?php echo $this->item->discount_amount > 0 ? round($this->item->discount_amount, 2) : null;?>"/>
					</td>
				</tr>
			<?php
			}

			if ($this->item->late_fee > 0 || empty($this->item->id))
			{
			?>
				<tr>
					<td class="title_cell">
						<?php echo JText::_('EB_LATE_FEE'); ?>
					</td>
					<td>
						<?php echo $this->config->currency_symbol?><input type="text" name="late_fee"
						                                                  class="input-medium"
						                                                  value="<?php echo $this->item->late_fee > 0 ? round($this->item->late_fee, 2) : null;?>"/>
					</td>
				</tr>
			<?php
			}

			if ($this->item->tax_amount > 0 || empty($this->item->id))
			{
				?>
				<tr>
					<td class="title_cell">
						<?php echo JText::_('EB_TAX'); ?>
					</td>
					<td>
						<?php echo $this->config->currency_symbol?><input type="text" name="tax_amount"
						                                                  class="input-medium"
						                                                  value="<?php echo $this->item->tax_amount > 0 ? round($this->item->tax_amount, 2) : null;?>"/>
					</td>
				</tr>
			<?php
			}
			?>
			<tr>
				<td class="title_cell">
					<?php echo JText::_('EB_GROSS_AMOUNT'); ?>
				</td>
				<td>
					<?php echo $this->config->currency_symbol?><input type="text" name="amount" class="input-medium"
					                                                  value="<?php echo $this->item->amount > 0 ? round($this->item->amount, 2) : null;?>"/>
				</td>
			</tr>
		<?php
		}
		if ($this->item->deposit_amount > 0)
		{
			?>
			<tr>
				<td class="title_cell">
					<?php echo JText::_('EB_DEPOSIT_AMOUNT'); ?>
				</td>
				<td class="field_cell">
					<?php echo EventbookingHelper::formatCurrency($this->item->deposit_amount, $this->config); ?>
				</td>
			</tr>
			<tr>
				<td class="title_cell">
					<?php echo JText::_('EB_DUE_AMOUNT'); ?>
				</td>
				<td class="field_cell">
					<?php echo EventbookingHelper::formatCurrency($this->item->amount - $this->item->deposit_amount, $this->config); ?>
				</td>
			</tr>
		<?php
		}
		?>
	</table>
	<?php
	if ($this->config->collect_member_information && count($this->rowMembers))
	{
		?>
		<!-- Member information -->
		<table width="100%">
			<?php
			for ($i = 0, $n = count($this->rowMembers); $i < $n; $i++)
			{
				$rowMember  = $this->rowMembers[$i];
				$memberId   = $rowMember->id;
				$rowMember  = $this->rowMembers[$i];
				$memberId   = $rowMember->id;
				$form       = new RADForm($this->memberFormFields);
				$memberData = EventBookingHelper::getRegistrantData($rowMember, $this->memberFormFields);
				$form->bind($memberData);
				$form->setFieldSuffix($i + 1);
				if ($i % 2 == 0)
				{
					echo "<tr>\n";
				}
				?>
				<td>
					<table class="admintable">
						<tr>
							<td colspan="2"
							    class="eb_row_heading"><?php echo JText::sprintf('EB_MEMBER_INFORMATION', $i + 1);;?></td>
						</tr>
						<?php
						$fields = $form->getFields();
						if (isset($fields['email']))
						{
							$emailField = $fields['email'];
							$cssClass   = $emailField->getAttribute('class');
							$cssClass   = str_replace(',ajax[ajaxEmailCall]', '', $cssClass);
							$emailField->setAttribute('class', $cssClass);
						}
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
					<input type="hidden" name="ids[]" value="<?php echo $rowMember->id; ?>"/>
				</td>
				<?php
				if (($i + 1) % 2 == 0)
				{
					echo "</tr>";
				}
			}
			if ($i % 2 != 0)
			{
				echo "<td>&nbsp;</td></tr>\n";
			}
			?>
		</table>
	<?php
	}
	?>
	<table width="100%" cellspacing="5" cellpadding="5">
		<tr>
			<td colspan="2">
				<input type="button" class="btn btn-primary" name="btnCancel" onclick="registrantList();"
				       value="<?php echo JText::_('EB_BACK');?> "/>
				<input type="submit" class="btn btn-primary" name="btnSave"
				       value="<?php echo JText::_('EB_SAVE'); ?>"/>
				<?php
				if (EventbookingHelper::canCancelRegistration($this->item->event_id) && $this->item->published != 2)
				{
					?>
					<input type="button" class="btn btn-primary" name="btnCancelRegistration"
					       onclick="cancelRegistration();" value="<?php echo JText::_('EB_CANCEL_REGISTRATION'); ?>"/>
				<?php
				}
				?>
			</td>
		</tr>
	</table>
	<!-- End members information -->
	<input type="hidden" name="option" value="com_eventbooking"/>
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>"/>
	<input type="hidden" name="task" value="save"/>
	<input type="hidden" name="event_id" value="<?php echo $this->item->event_id; ?>"/>
	<input type="hidden" name="return" value="<?php echo $this->return; ?>"/>
	<?php echo JHtml::_('form.token'); ?>
	<script type="text/javascript">
		var siteUrl = "<?php echo EventbookingHelper::getSiteUrl(); ?>";
		(function ($) {
			$(document).ready(function () {
				$("#adminForm").validationEngine();
				buildStateField('state', 'country', '<?php echo $selectedState; ?>');
			})
		})(jQuery);

		function registrantList()
		{
			var form = document.adminForm;
			form.task.value = 'cancel_edit';
			form.submit();
		}
		function cancelRegistration() {
			var form = document.adminForm;
			if (confirm("<?php echo JText::_('EB_CANCEL_REGISTRATION_CONFIRM'); ?>")) {
				form.task.value = 'cancel';
				form.submit();
			}
		}
	</script>
</form>