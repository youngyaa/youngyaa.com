<?php
/**
 * @version    SVN: <svn_id>
 * @package    JTicketing
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access.
defined('_JEXEC') or die();
$jticketingmainhelper =new jticketingmainhelper();
$app  = JFactory::getApplication();

$this->jt_params     = $app->getParams('com_jticketing');
$this->enable_self_enrollment     =  $this->jt_params->get('enable_self_enrollment', '', 'INT');
$this->supress_buy_button     =  $this->jt_params->get('supress_buy_button', '', 'INT');
$this->accesslevels_for_enrollment     =  $this->jt_params->get('accesslevels_for_enrollment');
$user   = JFactory::getUser();
$groups = $user->getAuthorisedViewLevels();
$guest  = $user->get('guest');
$allow_access_level_enrollment = 0;

// Check access levels for enrollment
foreach ($groups as $group)
{
	if (in_array($group, $this->accesslevels_for_enrollment))
	{
		$allow_access_level_enrollment = 1;
		break;
	}
}

// echo "ACCESS LEVELS==";
// print_r($allow_access_level_enrollment);

$integration = $this->jt_params->get('integration', '', 'INT');
?>

<div class="jticket_pin_item_<?php echo $random_container;?> col" >
<!--thumbnail-->
	<div class="jticketing_pin_layout_element">
		<!-- Image-->

		<div class="jticketing_pin_layout_image">
			<a href="<?php echo $jticketingmainhelper->getEventlink($eventdata->id);?>">
				<img alt="200x150"  class="jticketing_thmb_style" src="<?php

				if ($integration == 2)
				{
					$imagePath = 'media/com_jticketing/images/';
				}

				if ($integration == 4)
				{
					$imagePath = '/media/com_easysocial/avatars/event/'.$eventdata->id.'/';
				}
				if(empty($eventdata->image))
				{
					$imagePath = JRoute::_(JUri::base() .'media/com_jticketing/images/default_event.png');
				}
				else
				{
					$imagePath = JRoute::_(JUri::base() . $imagePath . $eventdata->image);
				}

				echo $imagePath; ?>">
			</a>
		</div>

		<!-- Image-->
		 <div class="jticketing_pin_layout_desc"><!-- caption-->

			<h4 class="jticketing_pin_layout_title">
				<a href="<?php echo  $jticketingmainhelper->getEventlink($eventdata->id);?>">
					<?php echo $eventdata->title; ?>
				</a>
			</h4><!--jticketing_caption-->

			<div class="jticketing_short_desc">
					 <?php
					 if(strlen($eventdata->short_description)>=50)
						echo substr($eventdata->short_description,0,50).'...';
					else
						echo $eventdata->short_description;
					 ?>
			</div><!--jticketing_short_desc-->

		</div><!--caption-->

		<dl class="jticketing_schedule">
			<dd>
        <strong><i class="fa fa-clock-o"></i><?php echo JText::_('COM_JTICKETING_EVENT_STARTDATE') . ': ';?></strong>
				<span><?php echo JFactory::getDate($eventdata->startdate)->Format(JText::_('COM_JTICKETING_DATE_FORMAT_SHOW_AMPM')); ?></span>
			</dd>
			<?php
			if (!empty($eventdata->enddate) and $eventdata->enddate != '0000-00-00 00:00:00')
			{
			?>
			<dd>
					<strong><i class="fa fa-clock-o"></i><?php echo JText::_('COM_JTICKETING_EVENT_ENDDATE') . ': ';?></strong>
					<span><?php echo JFactory::getDate($eventdata->enddate)->Format(JText::_('COM_JTICKETING_DATE_FORMAT_SHOW_AMPM')); ?></span>
			</dd>
			<?php
			}
			?>

			<?php
			if (!empty($eventdata->booking_start_date))
			{
			?>
			<dd>
					<strong><i class="fa fa-calendar"></i><?php echo JText::_('COM_JTICKETING_EVENT_BOOKING_START_DATE') . ': ';?></strong>
					<span><?php echo JFactory::getDate($eventdata->booking_start_date)->Format(JText::_('COM_JTICKETING_DATE_FORMAT_SHOW_SHORT')); ?></span>
			</dd>
			<?php
			}
			?>

			<?php
			if (!empty($eventdata->booking_start_date))
			{
			?>
			<dd>
					<strong><i class="fa fa-calendar"></i><?php echo JText::_('COM_JTICKETING_EVENT_BOOKING_END_DATE') . ': ';?></strong>
					<span><?php echo JFactory::getDate($eventdata->booking_end_date)->Format(JText::_('COM_JTICKETING_DATE_FORMAT_SHOW_SHORT')); ?></span>
			</dd>
			<?php
			}
			?>

		</dl>

		<div class="jticketing_place">
			<?php if (JVERSION>=3.0){?>
				<i class="icon icon-location"></i>
			<?php }else { ?>
				<i class="icon-map-marker"></i>
			<?php } ?>
			<?php echo $eventdata->location;  ?>
		</div><!--jticketing_place-->

		<div class="jticketing_pin_layout_btns">

			<a href="<?php echo $jticketingmainhelper->getEventlink($eventdata->id);?>"
			class="btn btn-primary com_jticketing_button">
				<?php echo JText::_('COM_JTICKETING_DETAILS');?>
			</a>

			<?php
			$jticketingmainhelper     = new jticketingmainhelper;
			$this->showbuybutton = $jticketingmainhelper->showbuybutton($eventdata->id);
			$Itemid = JFactory::getApplication()->input->get('Itemid');
			if($this->showbuybutton)
			{
				if ($this->enable_self_enrollment == 1 and $allow_access_level_enrollment == 1)
				{
					if(JFactory::getUser()->authorise('core.enroll','com_jticketing.event.'.$eventdata->id))
					{

				?>
					<a
					href="<?php echo JRoute::_('index.php?option=com_jticketing&task=orders.bookTicket&eventid='.$eventdata->id.'&Itemid='.$Itemid,false);?>"
					class="btn btn-success com_jticketing_button">
						<?php echo JText::_('COM_JTICKETING_ENROLL_BUTTON'); ?>
					</a>

				<?php
					}
				}

				if ($this->enable_self_enrollment == 0 or ($this->enable_self_enrollment == 1 and $this->supress_buy_button==0))
				{

				?>
					<a
					href="<?php echo JRoute::_('index.php?option=com_jticketing&view=buy&layout=default&eventid='.$eventdata->id.'&Itemid='.$Itemid,false);?>"
					class="btn btn-success com_jticketing_button">
						<?php echo JText::_('COM_JTICKETING_BUY_BUTTON'); ?>
					</a>
				<?php

				}
				?>
			<?php } ?>
		 </div>
	</div>
</div>
