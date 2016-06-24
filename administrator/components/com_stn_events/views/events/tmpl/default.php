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
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . 'administrator/components/com_stn_events/assets/css/stn_events.css');
$document->addStyleSheet(JUri::root() . 'media/com_stn_events/css/list.css');

$user      = JFactory::getUser();
$userId    = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
$canOrder  = $user->authorise('core.edit.state', 'com_stn_events');
$saveOrder = $listOrder == 'a.`ordering`';

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_stn_events&task=events.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'eventList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$sortFields = $this->getSortFields();
?>
<script type="text/javascript">
	Joomla.orderTable = function () {
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>') {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	};
	Joomla.submitbutton = function (task) {
		if (task == 'saveeventset') {
			jQuery('#adminForm').submit();
		}
	}
	jQuery(document).ready(function () {
		jQuery('.publishunpublh').on('click', function () {
			var publsh = jQuery(this).attr('data-original-title');
			var currentitem = jQuery(this);
			if (publsh == 'Unpublish'){
				var status = 0;
				var publsh = 'Publish';
				var publshlower = 'icon-unpublish';
			} else {
                var status = 1;
				var publsh = 'Unpublish';
				var publshlower = 'icon-publish';
			}
			var eid = jQuery(this).attr('data-eid');
			jQuery.ajax({
				url: "<?php echo JRoute::_('index.php?option=com_stn_events&view=events&task=publishunpublh',false);?>",
				type:"POST",
				data : {id:eid,status:status},
				success: function(result){
					currentitem.attr('data-original-title',publsh);
					currentitem.find('span').attr('class',publshlower);
				}
			});
		});
	});
</script>
<style>
#system-message-container{width:100%!important;}
</style>
<form action="<?php echo JRoute::_('index.php?option=com_stn_events&task=events.updateSetting&tmpl=component'); ?>" method="post"
	  name="adminForm" id="adminForm">
<?php
	$forms = $this->forms;
	foreach($forms->getFieldset() as $field){
?>
<div class="span12 control-group" style="margin-left:0;">
 <div class="span3"><?php echo $field->label; ?></div>
  <div class="span9"><?php echo $field->input ?></div>
</div>
<?php } ?>
</form>
<div class="span8" style="float:none; margin: 50px auto;">
<table class="table table-bordered" id="eventList">
	<tr>
		<th>Date</th>
		<th>Action</th>
	</tr>
	<?php foreach ($this->items as $i => $item) { ?>
	<tr>
		<th><a href="<?php echo JRoute::_('index.php?option=com_stn_events&view=event&layout=slot&id='.(int) $item->id); ?>"><?php echo $item->title; ?></a></th>
		<th>
        <?php
        if($item->status == 0){
		$status = 'Publish';
		$statusclass = 'unpublish';
		} else {
		$status = 'Unpublish';
		$statusclass = 'publish';
		}
		?>
			<a title="" class="btn btn-micro jgrid hasTooltip publishunpublh" data-original-title="<?php echo $status; ?>" data-eid="<?php echo $item->id; ?>">
				<span class="icon-<?php echo $statusclass; ?>"></span></a>
		</th>
	</tr>
	<?php } ?>
</table></div>