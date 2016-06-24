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

JHtml::_('behavior.tooltip');
$editor = JFactory::getEditor() ;
$format = 'Y-m-d' ;
EventbookingHelperJquery::validateForm();
$bootstrapHelper = new EventbookingHelperBootstrap($this->config->twitter_bootstrap_version);
$controlGroupClass = $bootstrapHelper->getClassMapping('control-group');
$controlLabelClass = $bootstrapHelper->getClassMapping('control-label');
$controlsClass     = $bootstrapHelper->getClassMapping('controls');
?>
<style>
	.calendar {
		vertical-align: bottom;
	}
</style>
<form action="index.php?option=com_eventbooking&view=event" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form form-horizontal">
<div id="eb-submit-event-simple" class="row-fluid eb-container">
		<div class="eb_form_header" style="width:100%;">
			<div style="float: left; width: 40%;"><?php echo JText::_('EB_ADD_EDIT_EVENT'); ?></div>
			<div style="float: right; width: 50%; text-align: right;">
				<input type="submit" name="btnSave" value="<?php echo JText::_('EB_SAVE'); ?>" class="btn btn-primary" />
				<input type="button" name="btnCancel" value="<?php echo JText::_('EB_CANCEL_EVENT'); ?>" onclick="cancelEvent();" class="btn btn-primary" />
			</div>
		</div>
		<div class="clearfix"></div>
		<div class="<?php echo $controlGroupClass;  ?>">
			<label class="<?php echo $controlLabelClass; ?>"><?php echo JText::_('EB_TITLE') ; ?></label>
			<div class="<?php echo $controlsClass; ?>">
				<input type="text" name="title" value="<?php echo $this->item->title; ?>" class="validate[required] input-xlarge" size="70" />
			</div>
		</div>
		<div class="<?php echo $controlGroupClass;  ?>">
			<label class="<?php echo $controlLabelClass; ?>"><?php echo JText::_('EB_ALIAS') ; ?></label>
			<div class="<?php echo $controlsClass; ?>">
				<input type="text" name="alias" value="<?php echo $this->item->alias; ?>" class="input-xlarge" size="70" />
			</div>
		</div>
		<div class="<?php echo $controlGroupClass;  ?>">
			<label class="<?php echo $controlLabelClass; ?>"><?php echo JText::_('EB_MAIN_EVENT_CATEGORY') ; ?></label>
			<div class="<?php echo $controlsClass; ?>">
				<div style="float: left;"><?php echo $this->lists['main_category_id'] ; ?></div>
			</div>
		</div>
		<div class="<?php echo $controlGroupClass;  ?>">
			<label class="<?php echo $controlLabelClass; ?>"><?php echo JText::_('EB_ADDITIONAL_CATEGORIES') ; ?></label>
			<div class="<?php echo $controlsClass; ?>">
				<div style="float: left;"><?php echo $this->lists['category_id'] ; ?></div>
				<div style="float: left; padding-top: 25px; padding-left: 10px;">Press <strong>Ctrl</strong> to select multiple categories</div>
			</div>
		</div>
		<div class="<?php echo $controlGroupClass;  ?>">
			<label class="<?php echo $controlLabelClass; ?>"><?php echo JText::_('EB_THUMB_IMAGE') ; ?></label>
			<div class="<?php echo $controlsClass; ?>">
				<input type="file" class="inputbox" name="thumb_image" size="60" />
				<?php
				if ($this->item->thumb)
				{
				?>
					<a href="<?php echo JURI::root().'media/com_eventbooking/images/'.$this->item->thumb; ?>" class="modal"><img src="<?php echo JURI::root().'media/com_eventbooking/images/thumbs/'.$this->item->thumb; ?>" class="img_preview" /></a>
					<input type="checkbox" name="del_thumb" value="1" /><?php echo JText::_('EB_DELETE_CURRENT_THUMB'); ?>
				<?php
				}
				?>
			</div>
		</div>
		<div class="<?php echo $controlGroupClass;  ?>">
			<label class="<?php echo $controlLabelClass; ?>"><?php echo JText::_('EB_LOCATION') ; ?></label>
			<div class="<?php echo $controlsClass; ?>">
				<?php echo $this->lists['location_id'] ; ?>
			</div>
		</div>
		<div class="<?php echo $controlGroupClass;  ?>">
			<label class="<?php echo $controlLabelClass; ?>">
				<?php echo JText::_('EB_EVENT_START_DATE'); ?>
			</label>
			<div class="<?php echo $controlsClass; ?>">
				<?php echo JHtml::_('calendar', ($this->item->event_date == $this->nullDate) ? '' : JHtml::_('date', $this->item->event_date, $format, null), 'event_date', 'event_date', '%Y-%m-%d', array('class' =>  'validate[required]')) ; ?>
				<?php echo $this->lists['event_date_hour'].' '.$this->lists['event_date_minute']; ?>
			</div>
		</div>
		<div class="<?php echo $controlGroupClass;  ?>">
			<label class="<?php echo $controlLabelClass; ?>">
				<?php echo JText::_('EB_EVENT_END_DATE'); ?>
			</label>
			<div class="<?php echo $controlsClass; ?>">
				<?php echo JHtml::_('calendar', ($this->item->event_end_date == $this->nullDate) ? '' : JHtml::_('date', $this->item->event_end_date, $format, null), 'event_end_date', 'event_end_date') ; ?>
				<?php echo $this->lists['event_end_date_hour'].' '.$this->lists['event_end_date_minute'] ; ?>
			</div>
		</div>

		<div class="<?php echo $controlGroupClass;  ?>">
			<label class="<?php echo $controlLabelClass; ?>">
				<?php echo JText::_('EB_REGISTRATION_START_DATE'); ?>
			</label>
			<div class="<?php echo $controlsClass; ?>">
				<?php echo JHtml::_('calendar', ($this->item->registration_start_date == $this->nullDate) ? '' : JHtml::_('date', $this->item->registration_start_date, $format, null), 'registration_start_date', 'registration_start_date') ; ?>
				<?php echo $this->lists['registration_start_hour'].' '.$this->lists['registration_start_minute'] ; ?>
			</div>
		</div>

		<div class="<?php echo $controlGroupClass;  ?>">
			<label class="<?php echo $controlLabelClass; ?>">
				<?php echo JText::_('EB_PRICE'); ?>
			</label>
			<div class="<?php echo $controlsClass; ?>">
				<input type="text" name="individual_price" id="individual_price" class="input-mini" size="10" value="<?php echo $this->item->individual_price; ?>" />
			</div>
		</div>
		<div class="<?php echo $controlGroupClass;  ?>">
			<label class="<?php echo $controlLabelClass; ?>">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'EB_EVENT_CAPACITY' );?>::<?php echo JText::_('EB_CAPACITY_EXPLAIN'); ?>"><?php echo JText::_('EB_CAPACITY'); ?></span>
			</label>
			<div class="<?php echo $controlsClass; ?>">
				<input type="text" name="event_capacity" id="event_capacity" class="input-mini" size="10" value="<?php echo $this->item->event_capacity; ?>" />
			</div>
		</div>
		<div class="<?php echo $controlGroupClass;  ?>">
			<label class="<?php echo $controlLabelClass; ?>"><?php echo JText::_('EB_REGISTRATION_TYPE'); ?></label>
			<div class="<?php echo $controlsClass; ?>">
				<?php echo $this->lists['registration_type'] ; ?>
			</div>
		</div>
		<div class="<?php echo $controlGroupClass;  ?>">
			<label class="<?php echo $controlLabelClass; ?>">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'EB_CUT_OFF_DATE' );?>::<?php echo JText::_('EB_CUT_OFF_DATE_EXPLAIN'); ?>"><?php echo JText::_('EB_CUT_OFF_DATE') ; ?></span>
			</label>
			<div class="<?php echo $controlsClass; ?>">
				<?php echo JHtml::_('calendar', ($this->item->cut_off_date == $this->nullDate) ? '' : JHtml::_('date', $this->item->cut_off_date, $format, null), 'cut_off_date', 'cut_off_date') ; ?>
			</div>
		</div>
		<div class="<?php echo $controlGroupClass;  ?>">
			<label class="<?php echo $controlLabelClass; ?>">
				<?php echo JText::_('EB_NOTIFICATION_EMAILS'); ?>
			</label>
			<div class="<?php echo $controlsClass; ?>">
				<input type="text" name="notification_emails" class="inputbox" size="70" value="<?php echo $this->item->notification_emails ; ?>" />
			</div>
		</div>
		<div class="<?php echo $controlGroupClass;  ?>">
			<label class="<?php echo $controlLabelClass; ?>">
				<?php echo JText::_('EB_PAYPAL_EMAIL'); ?>
			</label>
			<div class="<?php echo $controlsClass; ?>">
				<input type="text" name="paypal_email" class="inputbox" size="50" value="<?php echo $this->item->paypal_email ; ?>" />
			</div>
		</div>
		<?php
		if ($this->config->event_custom_field)
		{
			foreach ($this->form->getFieldset('basic') as $field)
			{
			?>
				<div class="<?php echo $controlGroupClass;  ?>">
					<label class="<?php echo $controlLabelClass; ?>">
						<?php echo $field->label;?>
					</label>
					<div class="<?php echo $controlsClass; ?>">
						<?php echo $field->input; ?>
					</div>
				</div>
			<?php
			}
		}
		?>
		<div class="<?php echo $controlGroupClass;  ?>">
			<label class="<?php echo $controlLabelClass; ?>">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'EB_ACCESS' );?>::<?php echo JText::_('EB_ACCESS_EXPLAIN'); ?>"><?php echo JText::_('EB_ACCESS'); ?></span>
			</label>
			<div class="<?php echo $controlsClass; ?>">
				<?php echo $this->lists['access']; ?>
			</div>
		</div>
		<div class="<?php echo $controlGroupClass;  ?>">
			<label class="<?php echo $controlLabelClass; ?>">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'EB_REGISTRATION_ACCESS' );?>::<?php echo JText::_('EB_REGISTRATION_ACCESS_EXPLAIN'); ?>"><?php echo JText::_('EB_REGISTRATION_ACCESS'); ?></span>
			</label>
			<div class="<?php echo $controlsClass; ?>">
				<?php echo $this->lists['registration_access']; ?>
			</div>
		</div>
		<div class="<?php echo $controlGroupClass;  ?>">
			<label class="<?php echo $controlLabelClass; ?>">
				<?php echo JText::_('EB_PUBLISHED'); ?>
			</label>
			<?php
				if (version_compare(JVERSION, '3.0', 'ge'))
				{
				?>
					<?php echo $this->lists['published']; ?>
				<?php
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
		<div class="<?php echo $controlGroupClass;  ?>">
			<label class="<?php echo $controlLabelClass; ?>">
				<?php echo  JText::_('EB_SHORT_DESCRIPTION'); ?>
			</label>
			<div class="<?php echo $controlsClass; ?>">
				<?php echo $editor->display( 'short_description',  $this->item->short_description , '100%', '180', '90', '6' ) ; ?>
			</div>
		</div>
		<div class="<?php echo $controlGroupClass;  ?>">
			<label class="<?php echo $controlLabelClass; ?>">
				<?php echo  JText::_('EB_DESCRIPTION'); ?>
			</label>
			<div class="<?php echo $controlsClass; ?>">
				<?php echo $editor->display( 'description',  $this->item->description , '100%', '250', '90', '10' ) ; ?>
			</div>
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

		function cancelEvent()
		{
			location.href   ="<?php echo JRoute::_('index.php?option=com_eventbooking&task=event.cancel&Itemid=' . $this->Itemid, false); ?>";
		}
	</script>
	<input type="hidden" name="option" value="com_eventbooking" />
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="return" value="<?php echo $this->return; ?>" />
	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>