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
$data = $this->data;
$link = JRoute::_('index.php?option=com_eventbooking&view=calendar&month='.$this->month.'&Itemid='.$this->Itemid) ;
?>
<script type="text/javascript" src="<?php echo JUri::root().'media/com_eventbooking/assets/js/minicalendar.js'; ?>"></script>
<table class="extcal_navbar" border="0" width="100%">
	<tr>		
		<td><div class="mod_eb_minicalendar_link"><a id="prev_year" style="cursor: pointer;">&laquo;</a></div></td>
		<td><div class="mod_eb_minicalendar_link"><a id="prev_month" style="cursor: pointer;">&lt;</a></div></td>	
		<td nowrap="nowrap" height="18" align="center" width="98%" valign="middle" class="extcal_month_label">
			<a class="mod_eb_minicalendar_link" href="<?php echo $link;?>" rel="nofollow">
				<?php echo $this->listMonth[$this->month - 1]; ?> &nbsp;
			</a>
			<a class="mod_eb_minicalendar_link" href="<?php echo $link;?>" rel="nofollow">
				<?php echo $this->year; ?>
			</a>
		</td>	
		<td><div class="mod_eb_minicalendar_link"><a id="next_month" style="cursor: pointer;" rel="nofollow">&gt;</a></div></td>
		<td><div class="mod_eb_minicalendar_link"><a id="next_year" style="cursor: pointer;" rel="nofollow">&raquo;</a></div></td>
	</tr>
</table>
<table class="mod_eb_mincalendar_table" cellpadding="0" cellspacing="0" border="0"  width="100%">
	<tr class="mod_eb_mincalendar_dayname">
		<?php 
			foreach ($this->days as $dayname) 
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
        $dataCount = count($data["dates"]);
        $dn=0;
        for ($w=0; $w<6 && $dn < $dataCount; $w++)
        {
        	?>
        	<tr>
        	<?php 
            for ($d=0; $d<7 && $dn < $dataCount; $d++)
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
               	        if ($currentDay["today"])
                        {
              				$class_today = "mod_eb_mincalendar_today";	              				
              			}
                        else
                        {
              				$class_today = "mod_eb_mincalendar_not_today";
              			}
                    	$numberEvents = count($currentDay["events"]) ;
                    	$dayos = $currentDay['d'];
		              	if ($currentDay['d'] < 10) $dayos = "0".$currentDay['d'];

                        if($numberEvents > 1)
                        {
                            $link = JRoute::_("index.php?option=com_eventbooking&view=calendar&layout=daily&day=$this->year-$this->month-$dayos&Itemid=$this->Itemid");
                        }
                        elseif ($numberEvents == 1)
                        {
                            $link = JRoute::_(EventbookingHelperRoute::getEventRoute($currentDay['events'][0]->id, 0, $this->Itemid));
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
	                    	if(count($currentDay["events"]))
	                    	{
	                    	?>
	                    		<a href="<?php echo $link; ?>" title="<?php echo  ($numberEvents > 1 ? $numberEvents.JText::_('EB_EVENTS') :  $currentDay["events"][0]->title) ; ?>" rel="nofollow">
	                    			<span class="<?php echo $class?>"><?php echo $currentDay['d']; ?></span>
	                    		</a>
	                    	<?php
	                    	}
	                    	else 
	                    	{ 
	                    	?>
	                    		<span class="<?php echo $class?>"> <?php echo $currentDay['d']; ?></span>
	                    	<?php
	                    	} 
	                    	?>
	                        <?php
                        echo "</td>\n";
                    break;
                }
                $dn++;
            }
            echo "</tr>\n";
        }
    ?>
</table>
