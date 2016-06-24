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

JHtml::_('behavior.tooltip');
$editor = JFactory::getEditor() ;
$format = 'Y-m-d' ;
?>
<style>
	.calendar {
		vertical-align: bottom;
	}
</style>
<script type="text/javascript">
	function checkData(pressbutton)
	{
		var form = document.adminForm;
		if (pressbutton == 'cancel')
		{
			Joomla.submitform( pressbutton );
			return;
		}
		else
		{
			//Should have some validations rule here
			//Check something here
			if (form.title.value == '')
			{
				alert("<?php echo JText::_('EB_PLEASE_ENTER_TITLE'); ?>");
				form.title.focus();
				return ;
			}
			if (form.event_date.value == '') {
				alert("<?php echo JText::_('EB_ENTER_EVENT_DATE'); ?>");
				form.event_date.focus();
				return ;
			}
			if (form.main_category_id.value == 0)
			{
				alert("<?php echo JText::_("EB_CHOOSE_CATEGORY");  ?>");
				return ;
			}
			//Check the price

			if (form.recurring_type) {
				//Check the recurring setting
				if (form.recurring_type[1].checked) {
					if (form.number_days.value == '') {
						alert("<?php echo JText::_("EB_ENTER_NUMBER_OF_DAYS"); ?>");
						form.number_days.focus();
						return ;
					}
					if (!parseInt(form.number_days.value)) {
						alert("<?php echo JText::_("EB_NUMBER_DAY_INTEGER"); ?>");
						form.number_days.focus();
						return ;
					}
				}else if (form.recurring_type[2].checked) {
					if (form.number_weeks.value == '') {
						alert("<?php echo JText::_("EB_ENTER_NUMBER_OF_WEEKS"); ?>");
						form.number_weeks.focus();
						return ;
					}
					if (!parseInt(form.number_weeks.value)) {
						alert("<?php echo JText::_("EB_NUMBER_WEEKS_INTEGER"); ?>");
						form.number_weeks.focus();
						return ;
					}
					//Check whether any days in the week
					var checked = false ;
					for (var i = 0 ; i < form['weekdays[]'].length ; i++) {
						if (form['weekdays[]'][i].checked)
							checked = true ;
					}
					if (!checked) {
						alert("<?php echo JText::_("EB_CHOOSE_ONEDAY"); ?>");
						form['weekdays[]'][0].focus();
						return ;
					}
				} else if (form.recurring_type[3].checked) {
					if (form.number_months.value == '') {
						alert("<?php echo JText::_("EB_ENTER_NUMBER_MONTHS"); ?>");
						form.number_months.focus();
						return ;
					}
					if (!parseInt(form.number_months.value)) {
						alert("<?php echo JText::_("EB_NUMBER_MONTH_INTEGER"); ?>");
						form.number_months.focus();
						return ;
					}
					if (form.monthdays.value == '') {
						alert("<?php echo JText::_("EB_ENTER_DAY_IN_MONTH"); ?>");
						form.monthdays.focus();
						return ;
					}
				}
			}
			Joomla.submitform( pressbutton );
		}
	}
</script>

<div class="submit-events-page">
	<div class="eb_form_header row">
		<div class="col-md-6">
			<h1 class="eb-page-heading"><?php echo JText::_('EB_ADD_EDIT_EVENT'); ?></h1>
		</div>
		<div class="col-md-6 action-button">
			<input type="button" name="btnSave" value="<?php echo JText::_('EB_SAVE'); ?>" onclick="checkData('save');" class="btn btn-primary" />
			<input type="button" name="btnSave" value="<?php echo JText::_('EB_CANCEL_EVENT'); ?>" onclick="checkData('cancel');" class="btn btn-inverse" />
		</div>
	</div>

	<form action="index.php?option=com_eventbooking&view=event" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form form-horizontal">
	<div class="eb-container">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#basic-information-page" data-toggle="tab"><?php echo JText::_('EB_BASIC_INFORMATION');?></a></li>
			<?php
				if ($this->config->activate_recurring_event && (!$this->item->id || $this->item->event_type == 1)) {
				?>
					<li><a href="#recurring-settings-page" data-toggle="tab"><?php echo JText::_('EB_RECURRING_SETTINGS');?></a></li>
				<?php
				}
			?>
			<li><a href="#group-registration-rates-page" data-toggle="tab"><?php echo JText::_('EB_GROUP_REGISTRATION_RATES');?></a></li>
			<li><a href="#misc-page" data-toggle="tab"><?php echo JText::_('EB_MISC');?></a></li>
			<li><a href="#discount-page" data-toggle="tab"><?php echo JText::_('EB_DISCOUNT_SETTING');?></a></li>
			<?php
				if ($this->config->event_custom_field) {
				?>
					<li><a href="#extra-information-page" data-toggle="tab"><?php echo JText::_('EB_EXTRA_INFORMATION');?></a></li>
				<?php
				}
			?>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="basic-information-page">
				<table class="admintable" width="100%">
					<tr>
						<td class="key"><?php echo JText::_('EB_TITLE') ; ?></td>
						<td>
							<input type="text" name="title" value="<?php echo $this->item->title; ?>" class="input-xlarge" size="70" />
						</td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_('EB_ALIAS') ; ?></td>
						<td>
							<input type="text" name="alias" value="<?php echo $this->item->alias; ?>" class="input-xlarge" size="70" />
						</td>
					</tr>
					<tr>
						<td class="key" valign="top"><?php echo JText::_('EB_MAIN_EVENT_CATEGORY') ; ?></td>
						<td>
							<div><?php echo $this->lists['main_category_id'] ; ?></div>
						</td>
					</tr>
					<tr>
						<td class="key" valign="top"><?php echo JText::_('EB_ADDITIONAL_CATEGORIES') ; ?></td>
						<td>
							<div class="additional-cate"><?php echo $this->lists['category_id'] ; ?></div>
							<div class="additional-cate-sub">Press <strong>Ctrl</strong> to select multiple categories</div>
						</td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_('EB_THUMB_IMAGE') ; ?></td>
						<td>
							<input type="file" class="inputbox inputthumb" name="thumb_image" size="60" />
							<?php
								if ($this->item->thumb) {
								?>
									<a href="<?php echo JURI::root().'media/com_eventbooking/images/'.$this->item->thumb; ?>" class="modal"><img src="<?php echo JURI::root().'media/com_eventbooking/images/thumbs/'.$this->item->thumb; ?>" class="img_preview" /></a>
									<input type="checkbox" name="del_thumb" value="1" /><?php echo JText::_('EB_DELETE_CURRENT_THUMB'); ?>
								<?php
								}
							?>
						</td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_('EB_LOCATION') ; ?></td>
						<td>
							<?php echo $this->lists['location_id'] ; ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EB_EVENT_START_DATE'); ?>
						</td>
						<td>
							<?php echo JHtml::_('calendar', ($this->item->event_date == $this->nullDate) ? '' : JHtml::_('date', $this->item->event_date, $format, null), 'event_date', 'event_date') ; ?>
							<?php echo $this->lists['event_date_hour'].' '.$this->lists['event_date_minute']; ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EB_EVENT_END_DATE'); ?>
						</td>
						<td>
							<?php echo JHtml::_('calendar', ($this->item->event_end_date == $this->nullDate) ? '' : JHtml::_('date', $this->item->event_end_date, $format, null), 'event_end_date', 'event_end_date') ; ?>
							<?php echo $this->lists['event_end_date_hour'].' '.$this->lists['event_end_date_minute'] ; ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EB_REGISTRATION_START_DATE'); ?>
						</td>
						<td>
							<?php echo JHtml::_('calendar', ($this->item->registration_start_date == $this->nullDate) ? '' : JHtml::_('date', $this->item->registration_start_date, $format, null), 'registration_start_date', 'registration_start_date') ; ?>
							<?php echo $this->lists['registration_start_hour'].' '.$this->lists['registration_start_minute'] ; ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EB_PRICE'); ?>
						</td>
						<td>
							<input type="text" name="individual_price" id="individual_price" class="input-mini" size="10" value="<?php echo $this->item->individual_price; ?>" />
						</td>
					</tr>
					<tr>
						<td class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'EB_EVENT_CAPACITY' );?>::<?php echo JText::_('EB_CAPACITY_EXPLAIN'); ?>"><?php echo JText::_('EB_CAPACITY'); ?></span>
						</td>
						<td>
							<input type="text" name="event_capacity" id="event_capacity" class="input-mini" size="10" value="<?php echo $this->item->event_capacity; ?>" />
						</td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_('EB_REGISTRATION_TYPE'); ?></td>
						<td>
							<?php echo $this->lists['registration_type'] ; ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'EB_CUT_OFF_DATE' );?>::<?php echo JText::_('EB_CUT_OFF_DATE_EXPLAIN'); ?>"><?php echo JText::_('EB_CUT_OFF_DATE') ; ?></span>
						</td>
						<td>
							<?php echo JHtml::_('calendar', ($this->item->cut_off_date == $this->nullDate) ? '' : JHtml::_('date', $this->item->cut_off_date, $format, null), 'cut_off_date', 'cut_off_date') ; ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'EB_MAX_NUMBER_REGISTRANTS' );?>::<?php echo JText::_('EB_MAX_NUMBER_REGISTRANTS_EXPLAIN'); ?>"><?php echo JText::_('EB_MAX_NUMBER_REGISTRANTS'); ?></span>
						</td>
						<td>
							<input type="text" name="max_group_number" id="max_group_number" class="input-mini" size="10" value="<?php echo $this->item->max_group_number; ?>" />
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EB_PUBLISHED'); ?>
						</td>
						<td>
							<?php echo $this->lists['published']; ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo  JText::_('EB_SHORT_DESCRIPTION'); ?>
						</td>
						<td>
							<?php echo $editor->display( 'short_description',  $this->item->short_description , '100%', '180', '90', '6' ) ; ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo  JText::_('EB_DESCRIPTION'); ?>
						</td>
						<td>
							<?php echo $editor->display( 'description',  $this->item->description , '100%', '250', '90', '10' ) ; ?>
						</td>
					</tr>
				</table>
			</div>
			<?php
				if ($this->config->activate_recurring_event && (!$this->item->id || $this->item->event_type == 1)) {
				?>
					<div class="tab-pane" id="recurring-settings-page">
						<table class="admintable">
							<tr>
								<td width="30%" class="key" valign="top">
									<strong><?php echo JText::_('EB_REPEAT_TYPE'); ?></strong>
								</td>
								<td>
									<input type="radio" name="recurring_type" value="0" <?php if ($this->item->recurring_type == 0) echo ' checked="checked" ' ; ?> onclick="setDefaultDate();" /> <?php echo JText::_('EB_NO_REPEAT'); ?>
									<p>
									<input type="radio" name="recurring_type" value="1" <?php if ($this->item->recurring_type == 1) echo ' checked="checked" ' ; ?> onclick="setDefaultData();" /> <?php echo JText::_('EB_REPEAT_EVERY'); ?> <input type="text" name="number_days" size="5" class="input-mini" value="<?php echo $this->item->number_days ; ?>" /> <?php echo JText::_('EB_DAYS'); ?>
									</p>
									<p>
									<input type="radio" name="recurring_type" value="2" <?php if ($this->item->recurring_type == 2) echo ' checked="checked" ' ; ?> onclick="setDefaultData();" /> <?php echo JText::_('EB_REPEAT_EVERY'); ?> <input type="text" name="number_weeks" size="5" class="input-mini" value="<?php echo $this->item->number_weeks ; ?>" /> <?php echo JText::_('EB_WEEKS'); ?>
										<div style="padding-left:20px;">
											<strong><?php echo JText::_('EB_ON'); ?></strong>
											<?php
												$weekDays = explode(',', $this->item->weekdays) ;
												$daysOfWeek = array(0=> 'EB_SUN', 1 => 'EB_MON', 2=> 'EB_TUE', 3=>'EB_WED', 4 => 'EB_THUR', 5=>'EB_FRI', 6=> 'EB_SAT') ;
												foreach ($daysOfWeek as $key=>$value) {
												?>
													<input type="checkbox" class="inputbox" value="<?php echo $key; ?>" name="weekdays[]" <?php if (in_array($key, $weekDays)) echo ' checked="checked"' ; ?> /> <?php echo JText::_($value); ?>&nbsp;&nbsp;
												<?php
													if ($key == 4)
														echo '<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' ;
												}
											?>
										</div>
									</p>
									<p>
										<input type="radio" name="recurring_type" value="3" <?php if ($this->item->recurring_type == 3) echo ' checked="checked" ' ; ?> onclick="setDefaultData();" /> <?php echo JText::_('EB_REPEAT_EVERY'); ?> <input type="text" name="number_months" size="5" class="input-mini" value="<?php echo $this->item->number_months ; ?>" /> <?php echo JText::_('EB_MONTHS'); ?>
										 <strong><?php echo JText::_('EB_ON'); ?></strong>&nbsp;<input type="text" name="monthdays" class="input-small" size="10" value="<?php echo $this->item->monthdays; ?>" />
									</p>
								</td>
							</tr>
							<tr>
								<td class="key">
									<strong><?php echo JText::_('EB_RECURRING_ENDING'); ?></strong>
								</td>
								<td>
									<input type="radio" name="repeat_until" value="1"  <?php if ($this->item->recurring_occurrencies > 0 || $this->item->recurring_end_date == '') echo ' checked="checked" ' ; ?> /> <?php echo JText::_('EB_AFTER'); ?> <input type="text" name="recurring_occurrencies" size="5" class="inputbox" value="<?php echo $this->item->recurring_occurrencies ; ?>" /> <?php echo JText::_('EB_OCCURENCIES'); ?>
									<br />
									<input type="radio" name="repeat_until" value="2" <?php if ($this->item->recurring_end_date != '') echo ' checked="checked"' ; ?> /> <?php echo JText::_('EB_AFTER_DATE') ?> <?php echo JHtml::_('calendar', $this->item->recurring_end_date != '0000-00-00 00:00:00' ? JHtml::_('date', $this->item->recurring_end_date, '%Y-%m-%d', 0) : '', 'recurring_end_date', 'recurring_end_date'); ?>
									<br />
								</td>
							</tr>
							<?php
								if ($this->item->id) {
								?>
									<tr>
										<td class="key"><strong><?php echo JText::_('EB_UPDATE_CHILD_EVENT');?></strong></td>
										<td>
											<input type="checkbox" name="update_children_event" value="1" class="inputbox" />
										</td>
									</tr>
								<?php
								}
							?>
						</table>
					</div>
				<?php
				}
			?>
			<div class="tab-pane" id="group-registration-rates-page">
				<table  id="price_list">
					<tr>
						<th width="30%">
							<?php echo JText::_('EB_REGISTRANT_NUMBER'); ?>
						</th>
						<th>
							<?php echo JText::_('EB_RATE'); ?>
						</th>
					</tr>
					<?php
						$n = max(count($this->prices), 3);
						for ($i = 0 ; $i < $n ; $i++) {
								if (isset($this->prices[$i])) {
									$price = $this->prices[$i] ;
									$registrantNumber = $price->registrant_number ;
									$price = $price->price ;
								} else {
									$registrantNumber =  null ;
									$price =  null ;
								}
						?>
							<tr>
								<td>
									<input type="text" class="input-small" name="registrant_number[]" size="10" value="<?php echo $registrantNumber; ?>" />
								</td>
								<td>
									<input type="text" class="input-small" name="price[]" size="10" value="<?php echo $price; ?>" />
								</td>
							</tr>
						<?php
						}
					?>
					<tr>
						<td colspan="3">
							<input type="button" class="btn button btn-primary" value="<?php echo JText::_('EB_ADD'); ?>" onclick="addRow();" />
							&nbsp;
							<input type="button" class="btn button btn-default" value="<?php echo JText::_('EB_REMOVE'); ?>" onclick="removeRow();" />
						</td>
					</tr>
				</table>
			</div>
			<div class="tab-pane" id="misc-page">
				<table class="admintable">
					<tr>
						<td class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'EB_ACCESS' );?>::<?php echo JText::_('EB_ACCESS_EXPLAIN'); ?>"><?php echo JText::_('EB_ACCESS'); ?></span>
						</td>
						<td>
							<?php echo $this->lists['access']; ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'EB_REGISTRATION_ACCESS' );?>::<?php echo JText::_('EB_REGISTRATION_ACCESS_EXPLAIN'); ?>"><?php echo JText::_('EB_REGISTRATION_ACCESS'); ?></span>
						</td>
						<td>
							<?php echo $this->lists['registration_access']; ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EB_PAYPAL_EMAIL'); ?>
						</td>
						<td>
							<input type="text" name="paypal_email" class="inputbox" size="40" value="<?php echo $this->item->paypal_email ; ?>" />
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EB_NOTIFICATION_EMAILS'); ?>
						</td>
						<td>
							<input type="text" name="notification_emails" class="inputbox" size="40" value="<?php echo $this->item->notification_emails ; ?>" />
						</td>
					</tr>
					<tr>
						<td class="key" style="width: 160px;">
							<?php echo JText::_('EB_ENABLE_CANCEL'); ?>
						</td>
						<td>
							<?php echo $this->lists['enable_cancel_registration'] ; ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EB_CANCEL_BEFORE_DATE'); ?>
						</td>
						<td>
							<?php echo JHtml::_('calendar', $this->item->cancel_before_date != $this->nullDate ? JHtml::_('date', $this->item->cancel_before_date, $format, null) : '', 'cancel_before_date', 'cancel_before_date'); ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EB_AUTO_REMINDER'); ?>
						</td>
						<td>
							<?php echo $this->lists['enable_auto_reminder']; ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EB_REMIND_BEFORE'); ?>
						</td>
						<td>
							<input type="text" name="remind_before_x_days" class="input-mini" size="5" value="<?php echo $this->item->remind_before_x_days; ?>" /> days
						</td>
					</tr>
					<?php
						if ($this->config->term_condition_by_event) {
						?>
							<tr>
								<td class="key">
									<?php echo JText::_('EB_TERMS_CONDITIONS'); ?>
								</td>
								<td>
									<?php echo $this->lists['article_id'] ; ?>
								</td>
							</tr>
						<?php
						}
					?>

					<tr>
						<td width="100" class="key">
							<?php echo  JText::_('EB_META_KEYWORDS'); ?>
						</td>
						<td>
							<textarea rows="5" cols="30" class="input-lage" name="meta_keywords"><?php echo $this->item->meta_keywords; ?></textarea>
						</td>
					</tr>
					<tr>
						<td width="100" class="key">
							<?php echo  JText::_('EB_META_DESCRIPTION'); ?>
						</td>
						<td>
							<textarea rows="5" cols="30" class="input-lage" name="meta_description"><?php echo $this->item->meta_description; ?></textarea>
						</td>
					</tr>
				</table>
			</div>
			<div class="tab-pane" id="discount-page">
				<table class="admintable">
					<tr>
						<td class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'EB_MEMBER_DISCOUNT' );?>::<?php echo JText::_('EB_MEMBER_DISCOUNT_EXPLAIN'); ?>"><?php echo JText::_('EB_MEMBER_DISCOUNT'); ?></span>
						</td>
						<td>
							<input type="text" name="discount" id="discount" class="input-mini" size="5" value="<?php echo $this->item->discount; ?>" />&nbsp;&nbsp;<?php echo $this->lists['discount_type'] ; ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'EB_EARLY_BIRD_DISCOUNT' );?>::<?php echo JText::_('EB_EARLY_BIRD_DISCOUNT_EXPLAIN'); ?>"><?php echo JText::_('EB_EARLY_BIRD_DISCOUNT'); ?></span>
						</td>
						<td>
							<input type="text" name="early_bird_discount_amount" id="early_bird_discount_amount" class="input-mini" size="5" value="<?php echo $this->item->early_bird_discount_amount; ?>" />&nbsp;&nbsp;<?php echo $this->lists['early_bird_discount_type'] ; ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'EB_EARLY_BIRD_DISCOUNT_DATE' );?>::<?php echo JText::_('EB_EARLY_BIRD_DISCOUNT_DATE_EXPLAIN'); ?>"><?php echo JText::_('EB_EARLY_BIRD_DISCOUNT_DATE'); ?></span>
						</td>
						<td>
							<?php echo JHtml::_('calendar', $this->item->early_bird_discount_date != $this->nullDate ? JHtml::_('date', $this->item->early_bird_discount_date, $format, null) : '', 'early_bird_discount_date', 'early_bird_discount_date'); ?>
						</td>
					</tr>
				</table>
			</div>
			<?php
				if ($this->config->event_custom_field)
				{
				?>
					<div class="tab-pane" id="extra-information-page">
						<table class="admintable">
						<?php
							foreach ($this->form->getFieldset('basic') as $field)
							{
							?>
								<tr>
									<td class="key" width="30%">
										<?php echo $field->label ;?>
									</td>
									<td>
										<?php echo  $field->input ; ?>
									</td>
								</tr>
						<?php
							}
						?>
						</table>
					</div>
				<?php
				}
			?>
		</div>
	</div>
		<input type="hidden" name="option" value="com_eventbooking" />
		<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
		<input type="hidden" name="return" value="<?php echo $this->return; ?>" />
		<?php echo JHtml::_( 'form.token' ); ?>
		<script type="text/javascript">
			function addRow() {
				var table = document.getElementById('price_list');
				var newRowIndex = table.rows.length - 1 ;
				var row = table.insertRow(newRowIndex);
				var registrantNumber = row.insertCell(0);
				var price = row.insertCell(1);
				registrantNumber.innerHTML = '<input type="text" class="inputbox" name="registrant_number[]" size="10" />';
				price.innerHTML = '<input type="text" class="inputbox" name="price[]" size="10" />';

			}
			function removeRow() {
				var table = document.getElementById('price_list');
				var deletedRowIndex = table.rows.length - 2 ;
				if (deletedRowIndex >= 1) {
					table.deleteRow(deletedRowIndex);
				} else {
					alert("<?php echo JText::_('EB_NO_ROW_TO_DELETE'); ?>");
				}
			}

			function setDefaultData() {
				var form = document.adminForm ;
				if (form.recurring_type[1].checked) {
					if (form.number_days.value == '') {
						form.number_days.value =1 ;
					}
				} else if (form.recurring_type[2].checked) {
					if (form.number_weeks.value == '') {
						form.number_weeks.value = 1 ;
					}
				} else if (form.recurring_type[3].checked) {
					if (form.number_months.value == '') {
						form.number_months.value = 1 ;
					}
				}
			}
		</script>
	</form>
</div>