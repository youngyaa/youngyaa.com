<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Stn_events
 * @author     Pawan <goyalpawan89@gmail.com>
 * @copyright  2016 Pawan
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . 'media/com_stn_events/css/form.css');
?>
<script type="text/javascript">
	js = jQuery.noConflict();
	js(document).ready(function () {
		
	});

	Joomla.submitbutton = function (task) {
		if (task == 'eventsloatsave') {
			var error = 0;
			jQuery('#timesloatform input[type="text"]').each(function(){
				if(jQuery(this).val() == ''){
					error = 1;
					jQuery(this).addClass('invalid');
				}
			});
			jQuery('#timesloatform textarea').each(function(){
				if(jQuery(this).val() == ''){
					error = 1;
					jQuery(this).addClass('invalid');
				}
			});
			if(error == 0){
				jQuery('#timesloatform').submit();
			}
		} else if(task == 'eventsloatnew'){
			var html = jQuery('#eventaddmore tbody').html();
			jQuery('#eventSlotList tbody').append(html);
		} else if(task == 'eventcancel'){
			window.location.href = '<?php echo JRoute::_("index.php?option=com_stn_events",false); ?>';
		}
	}
	jQuery(document).delegate('.deleterowdata','click',function(){
		jQuery(this).parents('tr').remove();
	});
</script>
<style>
.timetypefield {
	display: block!important;
}
.input-append.timetypefield > input {
	width: 50px;
}
textarea.invalid {
  border: 1px solid rgb(157, 38, 29);
}
#eventSlotList .modal.btn {
  left: 10%;
  padding: 5px 0;
  position: absolute;
  top: 40px;
  width: 80%;
  z-index: 9;
}
td{
 position: relative;
}
</style>
<div class="span12">
  <h2>Time Slot Setting for <?php echo date('Y/m/d',strtotime($this->datedetl)); ?> :</h2>
 <form method="post" enctype="multipart/form-data" id="timesloatform" action="<?php echo JRoute::_("index.php?option=com_stn_events&task=event.updateTimeSloates&tmpl=component",false); ?>">
 <input type="hidden" value="<?php echo $_GET['id']; ?>" name="jform[date_id]">
  <table class="table table-bordered" id="eventSlotList">
    <thead>
      <tr>
        <th>Start Time</th>
        <th>End Time</th>
        <th>Prize</th>
        <th>Prize Image</th>
        <th>Prize Provider</th>
        <th>Prize Description</th>
        <th>Action</th>
		<th>Grabers</th>
      </tr>
    </thead>
    <tbody>
      <?php /*echo '<pre>';
		print_r($this->timeslotes);
		echo '</pre>';*/
		if(count($this->timeslotes) == 0){
		?>
      <tr>
        <?php /* ?><?php
		$forms = $this->form;
		foreach($forms->getFieldset() as $field){
		if($field->type == 'myhidden'){echo $field->input; } else {
		?>
        <td><?php echo $field->input; ?></td>
        <?php } } ?>
        <td><a data-original-title="Delete" class="btn btn-micro deleterowdata jgrid hasTooltip"> <span class="icon-unpublish"></span></a></td>
		<td>View</td> <?php */ ?>
		<?php
		$forms = $this->form;
		foreach($forms->getFieldset() as $field){ 
		if($field->type == 'myhidden'){echo $field->input; } else { if($field->type != 'Media'){ ?>
      <td><?php echo $field->input; ?></td>
      <?php } else {echo '<span style="display:none;">'.$field->input.'</span>';} } } ?>
      <td><a data-original-title="Delete" class="btn btn-micro deleterowdata jgrid hasTooltip"> <span class="icon-unpublish"></span></a></td>
	  <td>View</td>
      </tr>
      <?php } else { 
	  foreach($this->timeslotes as $ts){
	  ?>
      <tr>
        <input type="hidden" value="<?php echo $ts->id; ?>" name="jform[id][]">
        <td><div style="display:block!important;" class="input-append timetypefield">
            <input type="text" value="<?php echo date('H:i:s',strtotime($ts->starttime)); ?>" name="jform[starttime][]">
            <span class="add-on"><i data-date-icon="icon-calendar" data-time-icon="icon-time" class="icon-time"></i></span></div>
        </td>
        <td><div style="display:block!important;" class="input-append timetypefield">
            <input type="text" value="<?php echo date('H:i:s',strtotime($ts->endtime)); ?>" name="jform[endtime][]">
            <span class="add-on"><i data-date-icon="icon-calendar" data-time-icon="icon-time" class="icon-time"></i></span></div>
        </td>
        <td><input type="text" value="<?php echo $ts->prize; ?>" name="jform[prize][]"></td>
        <td><?php /*?><input type="media" aria-required="true" required="" class="required" id="jform_prizeimage" name="jform[prizeimage][]"><?php */?>
		<div class="input-prepend input-append">
		<div class="media-preview add-on">
		<span title="" class="hasTipPreview"><span class="icon-eye"></span></span>
		</div>
		<input type="file" name="jform[prizeimage][]" style="display:none;">
			<input type="text" class="input-small required hasTipImgpath" title="" readonly="readonly" value="<?php echo 'images/stnevents/'.str_replace('images/stnevents/','',$ts->prizeimage); ?>" id="jform_prizeimage<?php echo $ts->id; ?>" name="jform[prizeimage][<?php echo $ts->id; ?>]">
		<a rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;asset=com_stn_events&amp;author=&amp;fieldid=jform_prizeimage<?php echo $ts->id; ?>&amp;folder=stnevents" title="Select" class="modal btn">
		Select</a>
		<a onclick="
		jInsertFieldValue('', 'jform_prizeimage<?php echo $ts->id; ?>');
		return false;
		" href="#" title="" class="btn hasTooltip" data-original-title="Clear">
		<span class="icon-remove"></span></a>
		</div>
		</td>
        <td><input type="text" value="<?php echo $ts->prizeprovider; ?>" name="jform[prizeprovider][]"></td>
        <td><textarea name="jform[prizedescription][]"><?php echo $ts->prizedescription; ?></textarea></td>
        <td><a class="btn btn-micro deleterowdata jgrid hasTooltip" data-original-title="Delete"> <span class="icon-unpublish"></span></a></td>
		<td><a class="btn btn-link" href="<?php echo JRoute::_('index.php?option=com_stn_events&view=event&layout=grabers&id='.$ts->id,false); ?>">View</a></td>
      </tr>
      <?php } } ?>
    </tbody>
  </table>
  </form>
</div>
<table id="eventaddmore" style="display:none;">
  <tbody>
    <tr>
      <?php
		$forms = $this->form;
		foreach($forms->getFieldset() as $field){ 
		if($field->type == 'myhidden'){echo $field->input; } else { if($field->type != 'Media'){ ?>
      <td><?php echo $field->input; ?></td>
      <?php } else {echo '<span style="display:none;">'.$field->input.'</span>';} } } ?>
      <td><a data-original-title="Delete" class="btn btn-micro deleterowdata jgrid hasTooltip"> <span class="icon-unpublish"></span></a></td>
	  <td>View</td>
    </tr>
  </tbody>
</table>
