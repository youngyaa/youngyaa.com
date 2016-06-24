<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die ; 	
?>
<script type="text/javascript">
	Joomla.submitbutton = function (pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			Joomla.submitform(pressbutton, form);
			return;
		}
		else
		{
			if (form.rate.value == "")
			{
				alert("<?php echo JText::_("OSM_ENTER_TAX_RATE"); ?>");
				form.rate.focus();
			}
			else
			{
				Joomla.submitform(pressbutton, form);
			}
		}
	}
</script>
<form action="index.php?option=com_osmembership&view=tax" method="post" name="adminForm" id="adminForm">
<div style="float:left; width: 100%;">			
			<table class="admintable adminform">
				<tr>
					<td width="100" class="key">
						<?php echo  JText::_('OSM_PLAN'); ?>
					</td>
					<td>
						<?php echo $this->lists['plan_id']; ?>
					</td>
				</tr>
				<tr>
					<td width="100" class="key">
						<?php echo  JText::_('OSM_COUNTRY'); ?>
					</td>
					<td>
						<?php echo $this->lists['country']; ?>
					</td>
				</tr>
				<tr>
					<td width="100" class="key">
						<?php echo  JText::_('OSM_STATE'); ?>
					</td>
					<td>
						<?php echo $this->lists['state']; ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_('OSM_TAX_RATE'); ?>
					</td>
					<td>
						<input class="text_area" type="text" name="rate" id="rate" size="5" maxlength="250" value="<?php echo $this->item->rate;?>" />
					</td>
				</tr>
				<?php
					if (isset($this->lists['vies']))
					{
					?>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_VIES'); ?>
						</td>
						<td>
							<?php echo $this->lists['vies'];?>
						</td>
					</tr>
					<?php
					}
				?>
				<tr>
					<td class="key">
						<?php echo JText::_('OSM_PUBLISHED'); ?>
					</td>
					<td>
						<?php echo $this->lists['published']; ?>
					</td>
				</tr>
		</table>							
</div>		
<div class="clr"></div>	
	<?php echo JHtml::_( 'form.token' ); ?>
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="task" value="" />
</form>