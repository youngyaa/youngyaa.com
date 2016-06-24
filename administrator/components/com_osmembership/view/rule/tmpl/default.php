<?php
/**
 * @version		1.6.2
 * @package        Joomla
* @subpackage	OSMembership
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die ;

$editor = JFactory::getEditor(); 	
?>
<script type="text/javascript">	
	Joomla.submitbutton = function(pressbutton)
	{
		var form = document.adminForm;
		if (pressbutton == 'rule.cancel') {
			Joomla.submitform(pressbutton, form);
			return;				
		} else {
			//Validate the entered data before submitting
			if (form.from_plan_id.value == 0) {
				alert("<?php echo JText::_('OSM_CHOOSE_FROM_PLAN'); ?>");
				form.from_plan_id.focus();
				return ;
			}
			
			if (form.to_plan_id.value == 0) {
				alert("<?php echo JText::_('OSM_CHOOSE_TO_PLAN'); ?>");
				form.to_plan_id.focus();
				return ;
			}
			
			if (form.price.value == 0) {
				alert("<?php echo JText::_('OSM_ENTER_PRICE_FOR_UPGRADE'); ?>");
				form.price.focus();
				return ;
			}	
																
			Joomla.submitform(pressbutton, form);
		}								
	}		
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<div style="float:left; width: 100%;">	
			<table class="admintable adminform" style="width: 90%;">
				<tr>
					<td width="150" class="key">
						<?php echo  JText::_('OSM_FROM_PLAN'); ?>
					</td>
					<td>
						<?php echo $this->lists['from_plan_id']; ?>
					</td>
					<td>
						&nbsp;
					</td>
				</tr>						
				<tr>
					<td width="150" class="key">
						<?php echo  JText::_('OSM_TO_PLAN'); ?>
					</td>
					<td>
						<?php echo $this->lists['to_plan_id']; ?>
					</td>
					<td>
						&nbsp;
					</td>
				</tr>
				<tr>
					<td width="150" class="key">
						<?php echo  JText::_('OSM_PRICE'); ?>
					</td>
					<td>
						<input class="text_area" type="text" name="price" id="price" size="10" maxlength="250" value="<?php echo $this->item->price;?>" />
					</td>
					<td>
						&nbsp;
					</td>
				</tr>
				<tr>
					<td width="150" class="key">
						<?php echo  JText::_('OSM_MAX_PRESENCE'); ?>
					</td>
					<td>
						<input class="text_area" type="text" name="max_presence" id="max_presence" size="10" maxlength="250" value="<?php echo $this->item->max_presence;?>" />
					</td>
					<td>
						&nbsp;
					</td>
				</tr>										
				<tr>
					<td width="150" class="key">
						<?php echo  JText::_('OSM_MIN_PRESENCE'); ?>
					</td>
					<td>
						<input class="text_area" type="text" name="min_presence" id="min_presence" size="10" maxlength="250" value="<?php echo $this->item->min_presence;?>" />
					</td>
					<td>
						&nbsp;
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_('OSM_PUBLISHED'); ?>
					</td>
					<td>
						<?php echo $this->lists['published']; ?>
					</td>
					<td>
						&nbsp;
					</td>
				</tr>
		</table>										
</div>		
<div class="clr"></div>	
	<?php echo JHtml::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_osmembership" />
	<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="task" value="" />
</form>