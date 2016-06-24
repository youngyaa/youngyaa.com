<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     OSMembership
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die ; 
$itemId = JRequest::getInt('Itemid') ;
$cols = 4 ;
$db = JFactory::getDbo();
?>
<div id="osm-subscription-history" class="osm-container row-fluid">
<form method="post" name=os_form id="os_form" action="<?php echo JRoute::_('index.php?option=com_osmembership&view=subscriptions&Itemid='.$this->Itemid); ?>">			
<h1 class="osm-page-title"><?php echo JText::_('OSM_SUBSCRIPTION_HISTORY') ; ?></h1>
	<table class="table table-striped table-bordered table-condensed">
		<thead>		
			<tr>					
				<th>
					<?php echo JText::_('OSM_PLAN') ?>
				</th>							
				<th class="center">
					<?php echo JText::_('OSM_ACTIVATE_TIME') ; ?>
				</th>
				<th style="text-align: right;" class="hidden-phone">
					<?php echo JText::_('OSM_GROSS_AMOUNT') ; ?>
				</th>
				<th class="hidden-phone">
					<?php echo JText::_('OSM_SUBSCRIPTION_STATUS'); ?>
				</th>				
				<?php 
					if ($this->config->activate_invoice_feature) 
					{
						$cols++ ;
					?>
						<th class="hidden-phone" class="center">
							<?php echo JText::_('OSM_INVOICE_NUMBER') ; ?>
						</th>
					<?php	
					}
				?>											
			</tr>
		</thead>
		<tbody>
		<?php			
			$k = 0 ;
			for ($i = 0 , $n = count($this->items) ; $i < $n ; $i++) {
				$row = $this->items[$i] ;											
				$k = 1- $k ;
				$link = JRoute::_('index.php?option=com_osmembership&view=subscription&id='.$row->id.'&Itemid='.$this->Itemid);
				$symbol = $row->currency_symbol ? $row->currency_symbol : $row->currency;
			?>
				<tr>					
					<td>
						<a href="<?php echo $link; ?>"><?php echo $row->plan_title; ?></a>
					</td>											
					<td class="center">
						<strong><?php echo JHtml::_('date', $row->from_date, $this->config->date_format); ?></strong> <?php echo JText::_('OSM_TO'); ?>
						<strong>
							<?php
								if ($row->lifetime_membership || $row->to_date == '2099-12-31 23:59:59')
								{
									echo JText::_('OSM_LIFETIME');
								}
								else 
								{
									echo JHtml::_('date', $row->to_date, $this->config->date_format);
								} 
							?>																			
						</strong>
					</td>
					<td style="text-align: right;" class="hidden-phone">
						<?php echo OSmembershipHelper::formatCurrency($row->gross_amount, $this->config, $symbol)?>
					</td>
					<td class="hidden-phone">
						<?php
                            
                                switch ($row->published) {
                                    case 0 :
                                        echo JText::_('OSM_PENDING');
                                        break ;
                                    case 1 :
                                        echo JText::_('OSM_ACTIVE');
                                        break ;
                                    case 2 :
                                        echo JText::_('OSM_EXPIRED');
                                        break ;
                                    case 3 :
                                        echo JText::_('OSM_CANCELLED_PENDING');
                                        break ;
                                    case 4 :
                                        echo JText::_('OSM_CANCELLED_REFUNDED');
                                        break ;
                                }
                           
						?>
					</td>					
					<?php
						if ($this->config->activate_invoice_feature) 
						{
						?>
							<td class="center" class="hidden-phone">
								<?php 
									if ($row->invoice_number)
									{
									?>
										<a href="<?php echo JRoute::_('index.php?option=com_osmembership&task=download_invoice&id='.$row->id); ?>" title="<?php echo JText::_('OSM_DOWNLOAD'); ?>"><?php echo OSMembershipHelper::formatInvoiceNumber($row, $this->config) ; ?></a>
									<?php	
									}	
								?>								
							</td>							
						<?php	
						} 
					?>					
				</tr>
			<?php	
			}
			?>
			</tbody>
			<?php
			if ($this->pagination->total > $this->pagination->limit) 
			{
			?>
			<tfoot>	
				<tr>
					<td colspan="<?php echo $cols; ?>">
						<div class="pagination"><?php echo $this->pagination->getListFooter(); ?></div>
					</td>
				</tr>
			</tfoot>	
			<?php	
			}
		?>					
	</table>				 	       						
</form>
</div>