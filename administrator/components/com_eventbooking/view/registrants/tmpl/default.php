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

if (version_compare(JVERSION, '3.0', 'ge'))
{
	JHtml::_('formbehavior.chosen', 'select');
}
$colSpan = 11;
if ($this->config->show_event_date)
{
	$colSpan++;
}    
if ($this->config->activate_deposit_feature)
{
	$colSpan++;
}    
if ($this->totalPlugins > 1) 
{
	$colSpan++ ;
}

if ($this->config->activate_invoice_feature)
{
	$colSpan++;
}
?>
<form action="index.php?option=com_eventbooking&view=registrants" method="post" name="adminForm" id="adminForm">
<table width="100%">
<tr>
	<td align="left">
		<?php echo JText::_( 'Filter' ); ?>:
		<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->state->filter_search;?>" class="text_area search-query" onchange="document.adminForm.submit();" />		
		<button onclick="this.form.submit();" class="btn"><?php echo JText::_( 'Go' ); ?></button>
		<button onclick="document.getElementById('filter_search').value='';this.form.submit();" class="btn"><?php echo JText::_( 'Reset' ); ?></button>		
	</td >	
	<td style="float:right;">
		<?php
		echo $this->lists['filter_published'];
		echo $this->lists['filter_event_id'];
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			echo $this->pagination->getLimitBox();
		}
		?>
	</td>
</tr>
</table>
<div id="editcell">
	<table class="adminlist table table-striped">
	<thead>
		<tr>
			<th width="2%" class="text_center">
				<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this)" />
			</th>			
			<th class="title" style="text-align: left;" width="10%">
				<?php echo JHtml::_('grid.sort',  JText::_('EB_FIRST_NAME'), 'tbl.first_name', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
			</th>						
			<th class="title" style="text-align: left;" width="10%">
				<?php echo JHtml::_('grid.sort',  JText::_('EB_LAST_NAME'), 'tbl.last_name', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
			</th>
			<th class="title" style="text-align: left;" width="15%">
				<?php echo JHtml::_('grid.sort',  JText::_('EB_EVENT'), 'ev.title', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
			</th>
			<?php
				if ($this->config->show_event_date) {
				?>
					<th width="7%" class="title" nowrap="nowrap">
						<?php echo JHtml::_('grid.sort',  JText::_('EB_EVENT_DATE'), 'ev.event_date', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
					</th>	
				<?php	
				}
			?>								
			<th width="10%" class="title" nowrap="nowrap">
				<?php echo JHtml::_('grid.sort',  JText::_('EB_EMAIL'), 'tbl.email', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
			</th>
			<th width="8%" class="title" nowrap="nowrap">
				<?php echo JHtml::_('grid.sort',  JText::_('EB_NUMBER_REGISTRANTS'), 'tbl.number_registrants', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
			</th>
			<th width="10%" class="title" nowrap="nowrap">
				<?php echo JHtml::_('grid.sort',  JText::_('EB_REGISTRATION_DATE'), 'tbl.register_date', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
			</th>													
			<th width="5%" class="title" nowrap="nowrap">
				<?php echo JHtml::_('grid.sort',  JText::_('EB_AMOUNT'), 'tbl.amount', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
			</th>											
				<?php
				    if ($this->config->activate_deposit_feature) 
					{
				    ?>
				    	<th width="5%" class="title" nowrap="nowrap">
            				<?php echo JHtml::_('grid.sort',  JText::_('EB_PAYMENT_STATUS'), 'tbl.payment_status', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
            			</th>	
				    <?php    
				    }
				    if ($this->config->show_coupon_code_in_registrant_list)
				    {
				    ?>
				    	<th width="7%" class="title" nowrap="nowrap">
            				<?php echo JHtml::_('grid.sort',  JText::_('EB_COUPON'), 'cp.code', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
            			</th>
				    <?php    
				    } 
				    if ($this->totalPlugins > 1)
				    {
				    ?>
    					<th width="5%" class="title" nowrap="nowrap">
    						<?php echo JHtml::_('grid.sort',  JText::_('EB_PAYMENT_METHOD'), 'tbl.payment_method', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
    					</th>	
    				<?php	
    				}
				?>						
			<th width="5%" class="title" nowrap="nowrap">
				<?php echo JHtml::_('grid.sort',  JText::_('EB_REGISTRATION_STATUS'), 'tbl.published', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
			</th>				
			<?php
			if ($this->config->activate_invoice_feature) 
			{				
			?>
				<th width="8%" class="center">
					<?php echo JHtml::_('grid.sort',  JText::_('EB_INVOICE_NUMBER'), 'tbl.invoice_number', $this->state->filter_order_Dir, $this->state->filter_order); ?>
				</th>
			<?php	
			}   
			?>																									
			<th width="3%" class="title" nowrap="nowrap">
				<?php echo JHtml::_('grid.sort',  JText::_('EB_ID'), 'tbl.id', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
			</th>
		</tr>
	</thead>
	<tfoot>
		<tr>			
			<td colspan="<?php echo $colSpan ; ?>">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>							
		</tr>
	</tfoot>
	<tbody>
	<?php
	$k = 0;	
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)
	{
		$row = $this->items[$i];
		$link 	= JRoute::_( 'index.php?option=com_eventbooking&view=registrant&id='. $row->id );
		$checked 	= JHtml::_('grid.id',   $i, $row->id );
		if ($row->published == 0 || $row->published == 1) 
		{
			$published 	= JHtml::_('grid.published', $row, $i, 'tick.png', 'publish_x.png');	
		}
		elseif($row->published == 3)
		{
			$published = JText::_('EB_WAITING_LIST');
		}
		else 
		{
			$imageSrc = 'components/com_eventbooking/assets/icons/cancelled.jpg' ;
			$title = JText::_('EB_CANCELLED') ;
			$published = '<img src="'.$imageSrc.'" title="'.$title.'" />';
		}				
		$isMember = $row->group_id > 0 ? true : false ;	
		if ($isMember) 
		{
			$groupLink = JRoute::_( 'index.php?option=com_eventbooking&view=registrant&id='. $row->group_id );			
		}							
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td class="text_center">
				<?php echo $checked; ?>
			</td>				
			<td>
				<a href="<?php echo $link; ?>">
					<?php echo $row->first_name ?>
				</a>
				<?php
					if ($row->is_group_billing) 
					{
						echo '<br />' ;
						echo JText::_('EB_GROUP_BILLING');
					}
					if ($isMember) 
					{
					?>
						<br />
						<?php echo JText::_('EB_GROUP'); ?><a href="<?php echo $groupLink; ?>"><?php echo $row->group_name ;  ?></a>
					<?php			
					}
				?>
			</td>			
			<td>
				<?php echo $row->last_name ; ?>
			</td>
			<td>
				<a href="index.php?option=com_eventbooking&view=event&id=<?php echo $row->event_id; ?>"><?php echo $row->title ; ?></a>
			</td>
			<?php
				if ($this->config->show_event_date) 
				{
				?>
					<td class="text_center">
						<?php echo JHtml::_('date', $row->event_date, $this->config->date_format, null) ; ?>
					</td>
				<?php	
				}
			?>										
			<td>
                <a href="mailto:<?php echo $row->email;?>"><?php echo $row->email;?></a>
			</td>							
			<td class="center" style="font-weight: bold;">
				<?php echo $row->number_registrants; ?>				
			</td>								
			<td class="center">
				<?php echo JHtml::_('date', $row->register_date, $this->config->date_format); ?>
			</td>			
			<td>
				<?php echo EventbookingHelper::formatAmount($row->amount, $this->config) ; ?>
			</td>
			<?php			    
			    if ($this->config->activate_deposit_feature) 
				{
			    ?>   
			    	<td> 
        			    <?php
        			        if($row->payment_status == 1) 
							{
        			            echo JText::_('EB_FULL_PAYMENT');
        			        } 
        			        else 
							{
        			            echo JText::_('EB_PARTIAL_PAYMENT');
        			        }
        			    ?>
			        </td>
			    <?php        
			    }
	            if ($this->config->show_coupon_code_in_registrant_list) 
				{
			    ?>
			    	<td>
			    		<?php echo $row->coupon_code ; ?>
			    	</td>
			    <?php    
			    }
			    if ($this->totalPlugins > 1) 
				{			    	
			    	$method = os_payments::getPaymentMethod($row->payment_method) ;			    	
			    ?>
			    	<td>
			    		<?php if ($method) echo JText::_($method->getTitle()); ?>
			    	</td>			    		
			    <?php	
			    }
			?>
			<td class="center">
				<?php
					echo $published ;
				?>
			</td>				
			<?php
				if ($this->config->activate_invoice_feature)
				{
				?>
					<td class="center">
						<?php 
							if ($row->invoice_number)
							{
							?>
								<a href="<?php echo JRoute::_('index.php?option=com_eventbooking&task=download_invoice&id='.($row->cart_id ? $row->cart_id : ($row->group_id ? $row->group_id : $row->id))); ?>" title="<?php echo JText::_('EB_DOWNLOAD'); ?>"><?php echo EventbookingHelper::formatInvoiceNumber($row->invoice_number, $this->config) ; ?></a>
							<?php	
							}	
						?>						
					</td>
				<?php	
				}
			?>										
			<td class="center">
				<?php echo $row->id; ?>
			</td>
		</tr>
		<?php
		$k = 1 - $k;
	}
	?>
	</tbody>
	</table>
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->state->filter_order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->filter_order_Dir; ?>" />	
	<?php echo JHtml::_( 'form.token' ); ?>
	<script type="text/javascript">
		Joomla.submitbutton = function(pressbutton) 
		{
			var form = document.adminForm;
			if (pressbutton == 'add') 
			{
				if (form.filter_event_id.value == 0)
				{
					alert("<?php echo JText::_("EB_SELECT_EVENT_TO_ADD_REGISTRANT"); ?>");
					form.filter_event_id.focus();
					return;	
				}					
			}
			Joomla.submitform( pressbutton );		
		}		
	</script>	
</form>