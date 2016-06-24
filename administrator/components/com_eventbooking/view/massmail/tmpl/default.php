<?php
/**
 * @version        	2.0.0
 * @package        	Joomla
 * @subpackage		Event Booking
 * @author  		Tuan Pham Ngoc
 * @copyright    	Copyright (C) 2010 - 2015 Ossolution Team
 * @license        	GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die ;
JToolBarHelper::title( JText::_( 'EB_MASS_MAIL' ), 'massemail.png' );
JToolBarHelper::custom('send','send.png','send_f2.png', JText::_('EB_SEND_MAILS'), false);	
JToolBarHelper::cancel('cancel');
$editor = JFactory::getEditor(); 	
?>
<script type="text/javascript">
	Joomla.submitbutton = function(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			Joomla.submitform( pressbutton );
			return;				
		} else {
			//Need to check something here
			if (form.event_id.value == 0) {
				alert("<?php echo JText::_("EB_CHOOSE_EVENT"); ?>");
				form.event_id.focus() ;
				return ;				
			}			
			Joomla.submitform( pressbutton );
		}
	}
</script>
<form action="index.php?option=com_eventbooking&view=massmail" method="post" name="adminForm" id="adminForm">
<div class="row-fluid">	
	<table class="admintable adminform">
		<tr>
			<td width="100" class="key">
				<?php echo  JText::_('EB_EVENT'); ?>
			</td>
			<td width="60%">
				<?php echo $this->lists['event_id'] ; ?>
			</td>
			<td>
				&nbsp;
			</td>
		</tr>			
		<tr>
			<td class="key">
				<?php echo JText::_('EB_EMAIL_SUBJECT'); ?>
			</td>
			<td>
				<input type="text" name="subject" value="" size="70" class="inputbox" />	
			</td>				
			<td>
				&nbsp;
			</td>
		</tr>													
		<tr>
			<td class="key">
				<?php echo JText::_('EB_EMAIL_MESSAGE'); ?>
			</td>
			<td>
				<?php echo $editor->display( 'description',  '' , '100%', '250', '75', '10' ) ; ?>
			</td>
			<td valign="top">
				<strong><?php echo JText::_('EB_AVAILABLE_TAGS'); ?> : [FIRST_NAME], [LAST_NAME], [EVENT_TITLE], [EVENT_DATE], [SHORT_DESCRIPTION], [EVENT_LOCATION]</strong>
			</td>
		</tr>								
	</table>										
</div>		
<div class="clearfix"></div>	
	<?php echo JHtml::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_eventbooking" />	
	<input type="hidden" name="task" value="" />
</form>