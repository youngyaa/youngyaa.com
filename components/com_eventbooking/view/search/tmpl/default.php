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
if ($this->config->use_https)
{
	$ssl = 1;
}
else
{
	$ssl = 0;
}
JHtml::_('behavior.modal', 'a.eb-modal');
?>
<div id="eb-search-result-page" class="eb-container">
<h1 class="eb-page-heading"><?php echo JText::_('EB_SEARCH_RESULT'); ?></h1>
<form method="post" name="adminForm" id="adminForm" action="<?php echo JRoute::_('index.php?option=com_eventbooking&view=search&Itemid='.$this->Itemid); ?>">
	<?php
	if (count($this->items))
	{
		echo EventbookingHelperHtml::loadCommonLayout('common/events_default.php', array('events' => $this->items, 'config' => $this->config, 'Itemid' => $this->Itemid, 'nullDate' => $this->nullDate , 'ssl' => $ssl, 'viewLevels' => $this->viewLevels, 'bootstrapHelper' => $this->bootstrapHelper));
	}
	else
	{
	?>
		<p class="text-info"><?php echo JText::_('EB_NO_EVENTS_FOUND') ?></p>
	<?php
	}
	if ($this->pagination->total > $this->pagination->limit)
	{
	?>
		<div class="pagination">
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
	<?php
	}
	?>

	<script type="text/javascript">
		function cancelRegistration(registrantId) {
			var form = document.adminForm ;
			if (confirm("<?php echo JText::_('EB_CANCEL_REGISTRATION_CONFIRM'); ?>")) {
				form.task.value = 'registrant.cancel' ;
				form.id.value = registrantId ;
				form.submit() ;
			}
		}
	</script>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="" />
	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
</form>
</div>