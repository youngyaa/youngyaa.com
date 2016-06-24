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
if ($this->config->prevent_duplicate_registration)
{
	$readOnly = ' readonly="readonly" ' ;
}
else
{
	$readOnly = '' ;
}
$btnClass = $this->bootstrapHelper->getClassMapping('btn');
?>
<div id="eb-mini-cart-page" class="eb-container">
<?php
if (count($this->items)) {
?>
	<h1 class="eb-page-heading"><?php echo JText::_('EB_ADDED_EVENTS'); ?></h1>
	<form method="post" name="adminForm" id="adminForm" action="index.php">
		<table class="table table-striped table-bordered table-condensed">
			<thead>
				<tr>
					<th class="col_event">
						<?php echo JText::_('EB_EVENT'); ?>
					</th>
					<?php
						if ($this->config->show_event_date)
						{
						?>
							<th class="col_event_date">
								<?php echo JText::_('EB_EVENT_DATE'); ?>
							</th>
						<?php
						}
					?>
					<th class="col_action">
						<?php echo JText::_('EB_REMOVE'); ?>
					</th>
					<th class="col_price">
						<?php echo JText::_('EB_PRICE'); ?>
					</th>
					<th class="col_quantity">
						<?php echo JText::_('EB_QUANTITY'); ?><a id="update_cart_icon"><img onclick="javascript:updateCart();" src="<?php echo JUri::base(true).'/media/com_eventbooking/assets/images/update_quantity.png' ?>" title="<?php echo JText::_("EB_UPDATE_QUANTITY"); ?>" align="top" /></a>
					</th>
					<th class="col_quantity">
						<?php echo JText::_('EB_SUB_TOTAL'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php
				$total = 0 ;
				$k = 0 ;
				for ($i = 0 , $n = count($this->items) ; $i < $n; $i++)
				{
					$item = $this->items[$i] ;
					$rate = $this->config->show_discounted_price ? $item->discounted_rate : $item->rate;
					$total += $item->quantity*$rate;
					$url = JRoute::_('index.php?option=com_eventbooking&view=event&id='.$item->id.'&tmpl=component&Itemid='.$this->Itemid);
				?>
					<tr>
						<td class="col_event">
							<a href="<?php echo $url; ?>"><?php echo $item->title; ?></a>
						</td>
						<?php
							if ($this->config->show_event_date) {
							?>
								<td class="col_event_date">
									<?php
										if ($item->event_date == EB_TBC_DATE)
										{
											echo JText::_('EB_TBC');
										}
										else
										{
											echo JHtml::_('date', $item->event_date, $this->config->event_date_format, null);
										}
									?>
								</td>
							<?php
							}
						?>
						<td align="center" class="col_action">
							<img onclick="javascript:removeCart(<?php echo $item->id; ?>);" class="eb-remove-item" id="<?php echo $item->id?>" src="<?php echo JUri::base().'media/com_eventbooking/assets/images/remove_from_cart.png'; ?>" border="0" />
							<input id="event_id" type="hidden" name="event_id[]" value="<?php echo $item->id; ?>" />
						</td>
						<td class="col_price">
							<?php echo EventbookingHelper::formatAmount($rate, $this->config); ?>
						</td>
						<td class="col_quantity">
							<input id="quantity" type="text" class="input-mini inputbox quantity_box" size="3" value="<?php echo $item->quantity ; ?>" name="quantity[]" <?php echo $readOnly ; ?> />
						</td>
						<td class="col_price">
							<?php echo EventbookingHelper::formatAmount($rate*$item->quantity, $this->config); ?>
						</td>
					</tr>
				<?php
				}
				if ($this->config->show_event_date)
				{
					$cols = 6 ;
				}
				else
				{
					$cols = 5 ;
				}
				?>
				<tr>
					<td colspan="<?php echo $cols - 1 ; ?>" style="text-align: right;">
						<span class="total_amount"><?php echo JText::_('EB_TOTAL'); ?></span>
					</td>
					<td>
						<?php echo EventBookingHelper::formatCurrency($total, $this->config); ?>
					</td>
				</tr>
				<?php
				?>
				<tr>
					<td colspan="<?php echo $cols ; ?>" style="text-align: right;">
						<button onclick="javascript:colorbox();" id="add_more_item" class="<?php echo $btnClass; ?> btn-primary" type="button">
							<?php echo JText::_('EB_ADD_MORE_EVENTS'); ?>
						</button>
						<button onclick="javascript:updateCart();" id="update_cart" class="<?php echo $btnClass; ?> btn-primary" type="button">
							<?php echo JText::_('EB_UPDATE'); ?>
						</button>
						<button onclick="javascript:checkOut();" id="check_out" class="<?php echo $btnClass; ?> btn-primary" type="button">
							<?php echo JText::_('EB_CHECKOUT'); ?>
						</button>
					</td>
				</tr>
			</tbody>
		</table>
		<input type="hidden" name="option" value="com_eventbooking" />
		<input type="hidden" name="task" value="cart.update_cart" />
	</form>
<?php
} else {
?>
	<p class="message"><?php echo JText::_('EB_NO_EVENTS_IN_CART'); ?></p>
<?php
}

if ($this->config->use_https)
{
	$checkoutUrl = JRoute::_('index.php?option=com_eventbooking&task=view_checkout&Itemid='.$this->Itemid, false, 1);
}
else
{
	$checkoutUrl = JRoute::_('index.php?option=com_eventbooking&task=view_checkout&Itemid='.$this->Itemid, false, 0);
}
?>
</div>
<script type="text/javascript">
	<?php echo $this->jsString ; ?>
	function colorbox()
	{
		jQuery.colorbox.close();
	}
	function checkOut()
	{
		document.location.href= "<?php echo $checkoutUrl; ?>";
	}
	function updateCart()
	{
		Eb.jQuery(function($) {
			var ret = checkQuantity();
			if(ret)
			{
				var eventId = $("input[id='event_id']").map(function(){return $(this).val();}).get();
				var quantity = $("input[id='quantity']").map(function(){return $(this).val();}).get();
				$.ajax({
					type : 'POST',
					url  : 'index.php?option=com_eventbooking&task=cart.update_cart&Itemid=<?php echo $this->Itemid ?>&redirect=0&event_id=' + eventId + '&quantity=' + quantity,
					dataType: 'html',
					beforeSend: function() {
					$('#add_more_item').before('<span class="wait"><img src="<?php echo JUri::base(true); ?>/media/com_eventbooking/ajax-loadding-animation.gif" alt="" /></span>');
					},
					success : function(html) {
						$('#cboxLoadedContent').html(html);
						$('.wait').remove();
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			}
		})
	}

	function removeCart(id)
	{
		Eb.jQuery(function($) {
			$.ajax({
				type :'POST',
				url  : 'index.php?option=com_eventbooking&task=cart.remove_cart&id=' +  id + '&Itemid=<?php echo $this->Itemid ?>&redirect=0',
				dataType: 'html',
				beforeSend: function() {
					$('#add_more_item').before('<span class="wait"><img src="<?php echo JUri::base(true); ?>/media/com_eventbooking/ajax-loadding-animation.gif" alt="" /></span>');
				},
				success : function(html) {
					 $('#cboxLoadedContent').html(html);
					 $('.wait').remove();
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		})
	}

	function checkQuantity() {
		var eventId ;
		var quantity ;
		var enteredQuantity ;
		var index ;
		var availableQuantity ;
		var length = jQuery('input[name="event_id[]"]').length;
		if (length) {
			//There are more than one events
			for (var  i = 0 ; i < length ; i++) {
				eventId = jQuery('input[name="event_id[]"]')[i].value;
				enteredQuantity =  jQuery('input[name="quantity[]"]')[i].value;
				index = findIndex(eventId, arrEventIds);
				availableQuantity = arrQuantities[index] ;
				if ((availableQuantity != -1) && (enteredQuantity >availableQuantity)) {
					alert("<?php echo JText::_("EB_INVALID_QUANTITY"); ?>" + availableQuantity);
					a.push(jQuery('input[name="event_id[]"]')[i].focus());
					return false ;
				}
			}
		} else {
			//There is only one event
			enteredQuantity = jQuery('input[name="quantity[]"]').value ;
			availableQuantity = arrQuantities[0] ;
			if ((availableQuantity != -1) && (enteredQuantity >availableQuantity)) {
				alert("<?php echo JText::_("EB_INVALID_QUANTITY"); ?>" + availableQuantity);
				jQuery('input[name="event_id[]"]').focus();
				return false ;
			}
		}
		return true ;
	}


	function findIndex(eventId, eventIds) {
		for (var i = 0 ; i < eventIds.length ; i++) {
			if (eventIds[i] == eventId) {
				return i ;
			}
		}
		return -1 ;
	}

</script>