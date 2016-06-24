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
$slotdetail = $this->timeslotdate;
$allgrabers = $this->timeslotgrabers;
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
</style>
<div class="span12">
  <h2>Grabers list for <?php echo date('Y/m/d',strtotime($slotdetail->title)); ?> time slot <?php echo date('H:i:s',strtotime($slotdetail->starttime)); ?> to <?php echo date('H:i:s',strtotime($slotdetail->endtime)); ?> :</h2>
  <br/>
  <p>Winner For This Time Slot : <?php echo $allgrabers[0]->name; ?></p>
  <br/>
 <form method="post" enctype="multipart/form-data" id="timesloatform" action="<?php echo JRoute::_("index.php?option=com_stn_events&task=event.updateTimeSloates&tmpl=component",false); ?>">
 <input type="hidden" value="<?php echo $_GET['id']; ?>" name="jform[date_id]">
  <table class="table table-bordered" id="eventSlotList">
    <thead>
      <tr>
        <th>#</th>
        <th>User Name</th>
        <th>User Email</th>
        <th>Grab time</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($allgrabers as $k => $graber){ ?>
	  <tr>
        <th><?php echo ++$k; ?></th>
        <th><?php echo $graber->name; ?></th>
        <th><?php echo $graber->email; ?></th>
        <th><?php echo $graber->gbcreated; ?></th>
      </tr>
	  <?php } ?>
    </tbody>
  </table>
  </form>
</div>