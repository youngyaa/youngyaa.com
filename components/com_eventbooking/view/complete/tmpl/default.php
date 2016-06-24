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
?>
<div id="eb-registration-complete-page" class="eb-container">
	<h1 class="eb-page-heading"><?php echo JText::_('EB_REGISTRATION_COMPLETE'); ?></h1>
	<?php
		if (!$this->tmpl)
		{
		?>
			<div class="btn-group pull-right">
				<a href="<?php echo JRoute::_('index.php?option=com_eventbooking&view=complete&registration_code='.$this->registrationCode.'&tmpl=component&Itemid='.$this->Itemid); ?>" target="_blank" title="<?php echo JText::_('EB_PRINT_THIS_PAGE'); ?>"><i class="icon-print"></i></a>
			</div>
		<?php
		}
	?>
	<div id="eb-message" class="eb-message"><?php echo $this->message; ?></div>
</div>
<?php
	if ($this->tmpl == 'component')
	{
	?>
		<script type="text/javascript">
			window.print();
		</script>
	<?php
	}
	echo $this->conversionTrackingCode;
?>