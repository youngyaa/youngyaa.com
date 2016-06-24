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
?>
<script type="text/javascript">
	Joomla.submitbutton = function (pressbutton)
	{
		var form = document.adminForm;
		if (pressbutton == 'cancel')
		{
			Joomla.submitform(pressbutton);
			return;
		}
		else if (form.code.value == "")
		{
			alert("<?php echo JText::_("EB_ENTER_COUPON"); ?>");
			form.code.focus();
		}
		else if (form.discount.value == "")
		{
			alert("<?php echo JText::_("EN_ENTER_DISCOUNT_AMOUNT"); ?>");
			form.discount.focus();
		}
		else
		{
			Joomla.submitform(pressbutton);
		}
	}
</script>
<form action="index.php?option=com_eventbooking&view=coupon" method="post" name="adminForm" id="adminForm">
	<div class="row-fluid">
		<table class="admintable adminform">
			<tr>
				<td width="180" class="key">
					<?php echo JText::_('EB_CODE'); ?>
				</td>
				<td>
					<input class="text_area" type="text" name="code" id="code" size="15" maxlength="250"
					       value="<?php echo $this->item->code; ?>"/>
				</td>
			</tr>
			<tr>
				<td width="100" class="key">
					<?php echo JText::_('EB_DISCOUNT'); ?>
				</td>
				<td>
					<input class="input-small" type="text" name="discount" id="discount" size="10" maxlength="250"
					       value="<?php echo $this->item->discount; ?>"/>&nbsp;&nbsp;<?php echo $this->lists['coupon_type']; ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<?php echo JText::_('EB_EVENT'); ?>
				</td>
				<td>
					<?php echo $this->lists['event_id']; ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<?php echo JText::_('EB_TIMES'); ?>
				</td>
				<td>
					<input class="input-small" type="text" name="times" id="times" size="5" maxlength="250"
					       value="<?php echo $this->item->times; ?>"/>
				</td>
			</tr>
			<tr>
				<td class="key">
					<?php echo JText::_('EB_TIME_USED'); ?>
				</td>
				<td>
					<?php echo $this->item->used; ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<?php echo JText::_('EB_VALID_FROM_DATE'); ?>
				</td>
				<td>
					<?php echo JHtml::_('calendar', $this->item->valid_from != $this->nullDate ? $this->item->valid_from : '', 'valid_from', 'valid_from'); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<?php echo JText::_('EB_VALID_TO_DATE'); ?>
				</td>
				<td>
					<?php echo JHtml::_('calendar', $this->item->valid_to != $this->nullDate ? $this->item->valid_to : '', 'valid_to', 'valid_to'); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<?php echo JText::_('EB_PUBLISHED'); ?>
				</td>
				<td>
					<?php echo $this->lists['published']; ?>
				</td>
			</tr>
		</table>
	</div>
	<div class="clearfix"></div>
	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>"/>
	<input type="hidden" name="task" value=""/>
	<?php
	if (!$this->item->used)
	{
	?>
		<input type="hidden" name="used" value="0"/>
	<?php
	}
	?>
</form>