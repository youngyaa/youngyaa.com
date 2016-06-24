<?php
/**
 * @package	Jticketing
 * @copyright Copyright (C) 2009 -2010 Techjoomla, Tekdi Web Solutions . All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     http://www.techjoomla.com
 */

// no direct access
	defined('_JEXEC') or die('Restricted access');
global $mainframe;

$document =JFactory::getDocument();

jimport('joomla.filter.output');
jimport( 'joomla.utilities.date');

JHtml::_('behavior.modal', 'a.modal');
$user =JFactory::getUser();
if(empty($user->id))
{
echo '<b>'.JText::_('USER_LOGOUT').'</b>';
return;
}
$Itemid=$eventid='';
$eventid=JRequest::getInt('event');
$Itemid=JRequest::getInt('Itemid');
$com_params=JComponentHelper::getParams('com_jticketing');
$integration = $com_params->get('integration');
$siteadmin_comm_per = $com_params->get('siteadmin_comm_per');
$currency = $com_params->get('currency');
$allow_buy_guestreg = $com_params->get('allow_buy_guestreg');
$tnc = $com_params->get('tnc');
$collect_attendee_info_checkout = $com_params->get('collect_attendee_info_checkout','','INT');
$bootstrapclass="techjoomla-bootstrap";
$tableclass="table table-striped  table-hover ";
$buttonclass="btn";
$buttonclassprimary="btn btn-primary";
$appybtnclass="btn btn-primary";
$payment_statuses=array('P'=>JText::_('JT_PSTATUS_PENDING'),
'C'=>JText::_('JT_PSTATUS_COMPLETED'),
		'D'=>JText::_('JT_PSTATUS_DECLINED'),
		'E'=>JText::_('JT_PSTATUS_FAILED'),
		'UR'=>JText::_('JT_PSTATUS_UNDERREVIW'),
		'RF'=>JText::_('JT_PSTATUS_REFUNDED'),
		'CRV'=>JText::_('JT_PSTATUS_CANCEL_REVERSED'),
		'RV'=>JText::_('JT_PSTATUS_REVERSED'),
);
$pstatus=array();
$pstatus[]=JHtml::_('select.option','P', JText::_('JT_PSTATUS_PENDING'));
$pstatus[]=JHtml::_('select.option','C', JText::_('JT_PSTATUS_COMPLETED'));
$pstatus[]=JHtml::_('select.option','RF', JText::_('JT_PSTATUS_REFUNDED'));
if(!empty($this->lists['search_event']))
$eventid =$this->lists['search_event'];
?>
<div>
<h3 class="componentheading"><?php echo JText::_('ATTND_LIST'); ?>	</h3>
</div>
<?php
	$integration=$this->jticketingmainhelper->getIntegration();
	//if Jomsocial show JS Toolbar Header
	if($integration==1)
	{
		$jspath=JPATH_ROOT.DS.'components'.DS.'com_community';
		if(file_exists($jspath))
		{
			require_once($jspath.DS.'libraries'.DS.'core.php');
		}

		$header='';
		$header=$this->jticketingmainhelper->getJSheader();
		if(!empty($header))
		echo $header;
	}

	//if Jomsocial show JS Toolbar Header
	if(empty($eventid))
	$eventid=JRequest::getInt('event');
	$linkbackbutton='';
?>

<form  method="post" name="adminForm"	id="adminForm">
		<div class="techjoomla-bootstrap">
			<div id="all" class="row-fluid"><div class="span12">
				<div class="form-actions">
					<div class="pull-right">
						<button  onclick="if (document.adminForm.boxchecked.value==0){
						alert('Please first make a selection from the list');}else{ Joomla.submitbutton('attendee_list.checkin')}" class="btn btn-small">
							<span class="icon-publish"></span>
							<?php echo JText::_('COM_JTICKETING_CHECKIN_MSG')?>
						</button>
						<button  onclick="if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('attendee_list.undochekin')}" class="btn btn-small">
							<span class="icon-unpublish"></span>
							<?php echo JText::_('COM_JTICKETING_CHECKIN_FAIL')?>
						</button>
						<button type="button" class="btn btn-success" title="<?php echo JText::_('EXPORT_CSV')?>" onclick="checkeventselected()" >
							<?php echo JText::_('EXPORT_CSV')?>
						</button>
					</div>
				</div>
				<!-- End of form actions-->
					<div class="jticketing-form-actions">
						<?php
						$search_event = $mainframe->getUserStateFromRequest( 'com_jticketingsearch_event', 'search_event','', 'string' );
						echo JHtml::_('select.genericlist', $this->status_event, "search_event_list", 'class="ad-status" size="1"
						onchange="document.getElementById(\'task\').value =\'\';document.getElementById(\'controller\').value =\'\';document.adminForm.submit();" name="search_event_list"',"value", "text", $this->lists['search_event_list']);		 ?>
							<?php echo JHtml::_('select.genericlist', $this->search_paymentStatuslist, "search_paymentStatuslist", 'class="search-status" size="1"
							onchange="document.adminForm.submit();" name="search_paymentStatuslist"',"value", "text", $this->lists['search_paymentStatuslist']);
							?>
					<?php
					if(JVERSION>='3.0')
					{
					?>
					<div class="btn-group pull-right hidden-phone">
						<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
							<?php
								echo $this->pagination->getLimitBox();
							?>
					</div>
					<?php
					}
					?>
					</div>
			<div class="table-responsive">
				<table    class="<?php echo $tableclass;?> jt_table" >
					<tr>
						<th width="1%" >
					<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
				</th>
						<th width="5%">
							<?php echo JHtml::_( 'grid.sort','TICKET_ID','id,order_items_id', $this->lists['order_Dir'], $this->lists['order']);?>
						</th>
						<th width="10%"><?php echo JHtml::_( 'grid.sort','ATTENDER_NAME','name', $this->lists['order_Dir'], $this->lists['order']); ?></th>
						<th width="10%"><?php echo JHtml::_( 'grid.sort','BOUGHTON','cdate', $this->lists['order_Dir'], $this->lists['order']); ?></th>
						<th align="center" width="10%"><?php echo JText::_( 'TICKET_TYPE_TITLE' );?></th>
						<th align="center" width="10%"><?php echo JText::_( 'TICKET_TYPE_RATE' );?></th>
						<th align="center" width="10%"><?php echo  JText::_( 'ORIGINAL_AMOUNT' ); ?></th>
						<th align="center" width="10%"><?php echo JText::_( 'PAYMENT_STATUS'); ?></th>
						<th align="center" width="5%"><?php echo  JText::_( 'PREVIEW_TICKET' ); ?></th>
						<th align="center"><?php echo  JText::_( 'COM_JTICKETING_CHECKIN' ); ?></th>

					</tr>

					<?php
					$j= $i = 0;
					$totalnooftickets=$totalprice=$totalcommission=$totalearn=0;
				if(empty($this->Data))
							{
							?>
							<td colspan="9" align="center"><b><?php echo JText::_("NODATA");?></b></td>
							<?php
							}
					else{
					foreach($this->Data as $data) {
					$ticketid=JText::_("TICKET_PREFIX").$data->id.'-'.$data->order_items_id;	;
					if($data->ticketcount<0)
					$data->ticketcount=0;
					if($data->amount<0)
					$data->amount=0;
					if($data->totalamount<0)
					$data->totalamount=0;
					$totalnooftickets=$totalnooftickets+$data->ticketcount;
					$totalprice=$totalprice+$data->amount;
					$totalearn=$totalearn+$data->totalamount;
							 if(empty($data->thumb))
								$data->thumb = 'components/com_community/assets/user_thumb.png';
								$link = JRoute::_('index.php?option=com_community&view=profile&userid='.$data->user_id);

					?>
				<tr >
					<td align="center">
				     <?php echo JHtml::_('grid.id',$j,$data->order_items_id);

				     ?>
				</td>
					<td align="center">
							<?php if($data->status=='C') echo $ticketid;?>

					</td>
					<!--<td align="enter">
					<?php

						echo ucfirst($data->title);?>
					</td>-->
					<td align="center">
							<?php
								if(!empty($data->name))
								{
									echo ucfirst($data->name);
								}
								else
								{
									echo JText::_('COM_JTICKETING_GUEST');
								} ?>
					</td>
					<td align="center"><?php
					$jdate = new JDate($data->cdate);

					 echo  str_replace('00:00:00','',$jdate->Format('d-m-Y'));

					 ?></td>
					 <td ><?php echo $data->ticket_type_title; ?></td>
					<td align="center">
							<?php  echo $this->jticketingmainhelper->getFromattedPrice( number_format(($data->amount),2),$currency);?>
						</td>
						<td align="center">
							<?php  echo $this->jticketingmainhelper->getFromattedPrice( number_format(($data->totalamount),2),$currency);?>
						</td>
					<td align="center"><?php echo $payment_statuses[$data->status];?></td>
					<td	align="center">
							<?php

							$attendee_details = JRoute::_(JUri::root().'index.php?option=com_jticketing&view=attendee_list&layout=attendee_details&eventid='.$data->event_details_id.'&attendee_id='.$data->attendee_id.'&tmpl=component');
							if($data->status=='C')
							{
								$link = JRoute::_(JUri::root().'index.php?option=com_jticketing&view=mytickets&tmpl=component&
								layout=ticketprint&$jticketing_usesess=0&jticketing_eventid='.$data->evid.'
								&jticketing_userid='.$data->user_id.'&jticketing_ticketid='.$data->id.'&jticketing_order_items_id='.$data->order_items_id);

							?>

							<a rel="{handler: 'iframe', size: {x: 600, y: 600}}" href="<?php echo $link; ?>" class="modal">
								<span class="editlinktip hasTip" title="<?php echo JText::_('PREVIEW_DES');?>" ><?php echo JText::_('PREVIEW');?></span>
							</a>
								<?php
								//For Extra Attendee Fields
								if($collect_attendee_info_checkout)
								{
								?>
									<a rel="{handler: 'iframe', size: {x: 600, y: 600}}" class="modal" href="<?php echo $attendee_details; ?>" >
									<span class="editlinktip hasTip" title="<?php echo JText::_('COM_JTICKETING_VIEW_ATTENDEE');?>" ><?php echo JText::_('COM_JTICKETING_VIEW_ATTENDEE');?></span>
									</a>
								<?php
								}
							}
							else
							{
								//For Extra Attendee Fields
								if($collect_attendee_info_checkout)
								{
								?>
									<a rel="{handler: 'iframe', size: {x: 600, y: 600}}" class="modal" href="<?php echo $attendee_details; ?>" >
									<span class="editlinktip hasTip" title="<?php echo JText::_('COM_JTICKETING_VIEW_ATTENDEE');?>" ><?php echo JText::_('COM_JTICKETING_VIEW_ATTENDEE');?></span>
									</a>
								<?php
								}
							}
							?>
						</td>
							<td align="center">
								<?php if($data->status=='C'){
								?>

								<a href="javascript:void(0);" class="hasTooltip" data-original-title="<?php echo ( $data->checkin ) ? JText::_( 'COM_JTICKETING_CHECKIN_FAIL' ) : JText::_( 'COM_JTICKETING_CHECKIN_MSG' );?>" onclick=" listItemTask('cb<?php echo $j;?>','<?php echo ( $data->checkin ) ? 'attendee_list.undochekin' : 'attendee_list.checkin';?>')">
									<img src="<?php echo JUri::root();?>administrator/components/com_jticketing/assets/images/<?php echo ( $data->checkin ) ? 'publish.png' : 'unpublish.png';?>" width="16" height="16" border="0" />
								</a>
								<?php
								}?>
							</td>
				</tr>
				<?php
					$i++;
					$j++;
					}
					?>

				<?php
				}
				?>

			</table>
		</div>
		<div class="row-fluid">
			<div class="span12">
				<?php
					if(JVERSION<3.0)
						$class_pagination='pager';
					else
						$class_pagination='pagination';
				?>
				<div class="<?php echo $class_pagination; ?> com_jgive_align_center">
					<?php echo $this->pagination->getListFooter(); ?>
				</div>
			</div><!--span12-->
		</div><!--row-fluid-->

	</div></div></div>
<input type="hidden" name="option" value="com_jticketing" />
<input type="hidden" name="task" id="task" value="" />
<input type="hidden" name="boxchecked" id="boxchecked" value="0" />
<input type="hidden" name="defaltevent_list" value="<?php echo $this->lists['search_event_list'];?>" />
<input type="hidden" name="controller" id="controller" value="attendee_list" />
<input type="hidden" name="view" value="attendee_list" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />

</form>


<!-- newly added for JS toolbar inclusion  -->
<?php
if($integration==1) //if Jomsocial show JS Toolbar Footer
{
$footer='';
	$footer=$this->jticketingmainhelper->getJSfooter();
	if(!empty($footer))
	echo $footer;
}
?>
<!-- eoc for JS toolbar inclusion	 -->


<script type="text/javascript">
function checkeventselected()
{
	var event_selected = techjoomla.jQuery('#search_event_list').val();

	if(!event_selected)
	{
		alert("<?php echo JText::_('COM_JTICKETING_SELECT_EVENT');	?>");
		return false;
	}
	document.getElementById('task').value = 'attendee_list.csvexport';
	document.getElementById('controller').value = 'attendee_list';
	document.adminForm.submit();
	document.getElementById('task').value = '';
}
</script>

