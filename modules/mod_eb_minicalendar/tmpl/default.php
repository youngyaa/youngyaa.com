<?php
/**
 * @version        2.0.0
 * @package        Joomla
 * @subpackage     Event Booking
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2010 - 2015 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die;
?>
<div style="margin: 0px; padding: 0px; width:100%">
<span style="display: none;" id="eb_minicalendar"></span>
<div id="extcal_minical997" class="extcal_minical">
<table class="extcal_minical" cellspacing="1" cellpadding="0" border="0" align="center" width="100%">
<tr>
<td valign="top">
<?php
	$Itemid = $itemId;
	if ($Itemid <= 1) 
	{
	    $Itemid = EventBookingHelper::getItemid();
	}	
	$link = JRoute::_('index.php?option=com_eventbooking&view=calendar&month='.$month.'&Itemid='.$Itemid) ;	
?>
<input type="hidden" name="itemId" value="<?php echo $Itemid; ?>">
<input type="hidden" name="month_ajax" class="month_ajax" value="<?php echo $month; ?>">
<input type="hidden" name="year_ajax" class="year_ajax" value="<?php echo $year; ?>">
<div id="calendar_result">
	<table class="extcal_navbar" border="0" width="100%">
		<tr>			
			<td><div class="mod_eb_minicalendar_link"><a id="prev_year" style="cursor: pointer;" rel="nofollow">&laquo;</a></div></td>
			<td><div class="mod_eb_minicalendar_link"><a id="prev_month" style="cursor: pointer;" rel="nofollow">&lt;</a></div></td>
			<td nowrap="nowrap" height="18" align="center" width="98%" valign="middle" class="extcal_month_label">
				<a class="mod_eb_minicalendar_link" href="<?php echo $link;?>">
					<?php echo $listMonth[$month-1]; ?> &nbsp;
				</a>
				<a class="mod_eb_minicalendar_link" href="<?php echo $link;?>">
					<?php echo $year; ?>
				</a>
			</td>		
			<td><div class="mod_eb_minicalendar_link"><a id="next_month" style="cursor: pointer;" rel="nofollow">&gt;</a></div></td>
			<td><div class="mod_eb_minicalendar_link"><a id="next_year" style="cursor: pointer;" rel="nofollow">&raquo;</a></div></td>
		</tr>
	</table>
	<table class="mod_eb_mincalendar_table" cellpadding="0" cellspacing="0" border="0"  width="100%">
		<tbody id="calendar_result">
			<tr class="mod_eb_mincalendar_dayname">
				<?php 
					foreach ($days as $dayname)
					{ 
				?>
		             <td class="mod_eb_mincalendar_td_dayname">
		                 <?php echo $dayname; ?>
		             </td>
		        <?php
		         	} 
		         ?>
			</tr>
	 	<?php
	         $datacount = count($data["dates"]);
	         $dn=0;
	         for ($w=0;$w<6 && $dn<$datacount;$w++)
	         {
	         ?>
			<tr>
	              <?php
	                  for ($d=0;$d<7 && $dn<$datacount;$d++)
	                  {
		              	$currentDay = $data["dates"][$dn];	              
		              	switch ($currentDay["monthType"])
		              	{
		              		case "prior":
		              		case "following":
			              	?>
			                   <td>&nbsp;</td>
			                <?php
		                  	break;
		              		case "current":
		              	        if ($currentDay["today"]){
		              				$class_today = "mod_eb_mincalendar_today";	              				
		              			}else{
		              				$class_today = "mod_eb_mincalendar_not_today";
		              			}
		              			$numberEvents = count($currentDay["events"]) ;
								$dayos = $currentDay['d'];
		              			if ($currentDay['d'] < 10) $dayos = "0".$currentDay['d'];

		              	        if($numberEvents > 1)
		                    	{
                                    $link = JRoute::_("index.php?option=com_eventbooking&view=calendar&layout=daily&day=$year-$month-$dayos&Itemid=$Itemid");
		                    	}
		                    	elseif ($numberEvents == 1)
		                    	{
                                    $link = JRoute::_(EventbookingHelperRoute::getEventRoute($currentDay['events'][0]->id, 0, $Itemid));
		                    	}
				                if ($numberEvents > 0)
				                {
					               $class_event = "mod_eb_mincalendar_event";
				                }
				                else
				                {
					              $class_event = "mod_eb_mincalendar_no_event";
				                }
		             		?>
			                   <td class="<?php echo $class_today.' '.$class_event; ?>">		                   		
			                    	<?php 
			                    		if ($d == 0)
			                    		{
			                    			$class = "sunday";
			                    		}
			                    		else if($d == 6)
			                    		{
			                    			$class = "saturday";
			                    		}
			                    		else
			                    		{
			                    			$class = "nomarl";
			                    		}
			                    		if ($numberEvents)
			                    		{		                    		
			                    	?>	
			                    		<a class="eb_minical_link" href="<?php echo $link; ?>" title="<?php echo  ($numberEvents > 1 ? $numberEvents.' '.JText::_('EB_EVENTS') :  $currentDay["events"][0]->title) ; ?>" rel="nofollow">
			                    			<span class="<?php echo $class?>"><?php echo $currentDay['d'];?></span> 
			                    		</a>
			                   		<?php 
			                    		}
			                    		else
			                    		{
			                    	?>
		                    			<span class="<?php echo $class; ?>"><?php echo $currentDay['d']; ?></span> 
			                    	<?php 
			                    		}
			                    echo "</td>\n";
		                	break;
		            	}
		                	$dn++;
		              }
	            echo "</tr>\n";
	          }
			?>	
		</tbody>
	</table>
</td>
</tr>
</table>
</div>
</div>