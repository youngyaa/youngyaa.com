<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined( '_JEXEC' ) or die ;
?>

<form action="index.php?option=com_osmembership&view=state" method="post" name="adminForm" id="adminForm">
<div class="row-fluid">	
	<table class="admintable adminform">
		<tr>
			<td width="100" class="key">
				<?php echo  JText::_('OSM_COUNTRY_NAME'); ?>
			</td>
			<td>
				<?php echo $this->lists['country_id'];?>
			</td>
		</tr>	
		<tr>
			<td width="100" class="key">
				<?php echo  JText::_('OSM_STATE_NAME'); ?>
			</td>
			<td>
				<input class="text_area" type="text" name="state_name" id="state_name" size="40" maxlength="250" value="<?php echo $this->item->state_name;?>" />
			</td>
		</tr>
		<tr>
			<td width="100" class="key">
				<?php echo  JText::_('OSM_STATE_CODE_3'); ?>
			</td>
			<td>
				<input class="text_area" type="text" name="state_3_code" id="state_3_code" maxlength="250" value="<?php echo $this->item->state_3_code;?>" />
			</td>
		</tr>
		<tr>
			<td width="100" class="key">
				<?php echo  JText::_('OSM_STATE_CODE_2'); ?>
			</td>
			<td>
				<input class="text_area" type="text" name="state_2_code" id="state_2_code" maxlength="250" value="<?php echo $this->item->state_2_code;?>" />
			</td>
		</tr>				
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
<div class="clearfix"></div>	
	<?php echo JHtml::_( 'form.token' ); ?>
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="task" value="" />
</form>